<!-- This is the main configuration File of Zamboanga Barter Online Market-->
<?php
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");
include("admin/inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';

// Getting all language variables into array as global variable
$i=1;
$statement = $pdo->prepare("SELECT * FROM tbl_language");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	define('LANG_VALUE_'.$i,$row['lang_value']);
	$i++;
}

$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row)
{
	$logo = $row['logo'];
	$favicon = $row['favicon'];
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
	$meta_title_home = $row['meta_title_home'];
    $meta_keyword_home = $row['meta_keyword_home'];
    $meta_description_home = $row['meta_description_home'];
    $before_head = $row['before_head'];
    $after_body = $row['after_body'];
}

// Checking the order table and removing the pending transaction that are 24 hours+ old. Very important
$current_date_time = date('Y-m-d H:i:s');
$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Pending'));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	$ts1 = strtotime($row['payment_date']);
	$ts2 = strtotime($current_date_time);     
	$diff = $ts2 - $ts1;
	$time = $diff/(3600);
	if($time>24) {

		// Return back the stock amount
		$statement1 = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
		$statement1->execute(array($row['payment_id']));
		$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result1 as $row1) {
			$statement2 = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
			$statement2->execute(array($row1['product_id']));
			$result2 = $statement2->fetchAll(PDO::FETCH_ASSOC);							
			foreach ($result2 as $row2) {
				$p_qty = $row2['p_qty'];
			}
			$final = $p_qty+$row1['quantity'];

			$statement = $pdo->prepare("UPDATE tbl_product SET p_qty=? WHERE p_id=?");
			$statement->execute(array($final,$row1['product_id']));
		}
		
		// Deleting data from table
		$statement1 = $pdo->prepare("DELETE FROM tbl_order WHERE payment_id=?");
		$statement1->execute(array($row['payment_id']));

		$statement1 = $pdo->prepare("DELETE FROM tbl_payment WHERE id=?");
		$statement1->execute(array($row['id']));
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

	<!-- Meta Tags -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<!-- Icon TOP LODING -->
	<link rel="icon" type="image/png" href="assets/uploads/<?php echo $favicon; ?>"> 

	<!-- Stylesheets -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/owl.carousel.min.css">
	<link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
	<link rel="stylesheet" href="assets/css/jquery.bxslider.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/rating.css">
	<link rel="stylesheet" href="assets/css/spacing.css">
	<link rel="stylesheet" href="assets/css/bootstrap-touch-slider.css">
	<link rel="stylesheet" href="assets/css/animate.min.css">
	<link rel="stylesheet" href="assets/css/tree-menu.css">
	<link rel="stylesheet" href="assets/css/select2.min.css">
	<link rel="stylesheet" href="assets/css/main.css">
	<link rel="stylesheet" href="assets/css/responsive.css">

	<?php

	$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
	$statement->execute();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$about_meta_title = $row['about_meta_title'];
		$about_meta_keyword = $row['about_meta_keyword'];
		$about_meta_description = $row['about_meta_description'];
		$faq_meta_title = $row['faq_meta_title'];
		$faq_meta_keyword = $row['faq_meta_keyword'];
		$faq_meta_description = $row['faq_meta_description'];
		$blog_meta_title = $row['blog_meta_title'];
		$blog_meta_keyword = $row['blog_meta_keyword'];
		$blog_meta_description = $row['blog_meta_description'];
		$contact_meta_title = $row['contact_meta_title'];
		$contact_meta_keyword = $row['contact_meta_keyword'];
		$contact_meta_description = $row['contact_meta_description'];
		$pgallery_meta_title = $row['pgallery_meta_title'];
		$pgallery_meta_keyword = $row['pgallery_meta_keyword'];
		$pgallery_meta_description = $row['pgallery_meta_description'];
		$vgallery_meta_title = $row['vgallery_meta_title'];
		$vgallery_meta_keyword = $row['vgallery_meta_keyword'];
		$vgallery_meta_description = $row['vgallery_meta_description'];
	}

	$cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	
	if($cur_page == 'index.php' || $cur_page == 'login.php' || $cur_page == 'registration.php' || $cur_page == 'cart.php' || $cur_page == 'checkout.php' || $cur_page == 'forget-password.php' || $cur_page == 'reset-password.php' || $cur_page == 'product-category.php' || $cur_page == 'product.php') {
		?>
		<title><?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}

	if($cur_page == 'about.php') {
		?>
		<title><?php echo $about_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $about_meta_keyword; ?>">
		<meta name="description" content="<?php echo $about_meta_description; ?>">
		<?php
	}
	if($cur_page == 'faq.php') {
		?>
		<title><?php echo $faq_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $faq_meta_keyword; ?>">
		<meta name="description" content="<?php echo $faq_meta_description; ?>">
		<?php
	}
	if($cur_page == 'contact.php') {
		?>
		<title><?php echo $contact_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $contact_meta_keyword; ?>">
		<meta name="description" content="<?php echo $contact_meta_description; ?>">
		<?php
	}
	if($cur_page == 'product.php')
	{
		$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
		$statement->execute(array($_REQUEST['id']));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) 
		{
		    $og_photo = $row['p_featured_photo'];
		    $og_title = $row['p_name'];
		    $og_slug = 'product.php?id='.$_REQUEST['id'];
			$og_description = substr(strip_tags($row['p_description']),0,200).'...';
		}
	}

	if($cur_page == 'dashboard.php') {
		?>
		<title>Dashboard - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	if($cur_page == 'customer-profile-update.php') {
		?>
		<title>Update Profile - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	if($cur_page == 'customer-billing-shipping-update.php') {
		?>
		<title>Update Delivery Address - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	if($cur_page == 'customer-password-update.php') {
		?>
		<title>Update Password - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	if($cur_page == 'customer-order.php') {
		?>
		<title>Orders - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	?>
	
	<?php if($cur_page == 'blog-single.php'): ?>
		<meta property="og:title" content="<?php echo $og_title; ?>">
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?php echo BASE_URL.$og_slug; ?>">
		<meta property="og:description" content="<?php echo $og_description; ?>">
		<meta property="og:image" content="assets/uploads/<?php echo $og_photo; ?>">
	<?php endif; ?>

	<?php if($cur_page == 'product.php'): ?>
		<meta property="og:title" content="<?php echo $og_title; ?>">
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?php echo BASE_URL.$og_slug; ?>">
		<meta property="og:description" content="<?php echo $og_description; ?>">
		<meta property="og:image" content="assets/uploads/<?php echo $og_photo; ?>">
	<?php endif; ?>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>

	<script type="text/javascript" src="//platform-api.sharethis.com/js/sharethis.js#property=5993ef01e2587a001253a261&product=inline-share-buttons"></script>

<?php echo $before_head; ?>

</head>
<body>

<?php echo $after_body; ?>
<!--
<div id="preloader">
	<div id="status"></div>
</div>-->

<!-- top bar -->
<div class="top">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="left">
					<ul>
							<!-- TOP BAR SETTING -->
						<li><i class="fa fa-phone"></i> <?php echo $contact_phone; ?></li>
						<li><i class="fa fa-envelope-o"></i> <?php echo $contact_email; ?></li>
					</ul>
					
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="right">
					<ul>
						<?php
						$statement = $pdo->prepare("SELECT * FROM tbl_social");
						$statement->execute();
						$result = $statement->fetchAll(PDO::FETCH_ASSOC);
						foreach ($result as $row) {
							?>
							<?php if($row['social_url'] != ''): ?>
							<li><a href="<?php echo $row['social_url']; ?>"><i class="<?php echo $row['social_icon']; ?>"></i></a></li>
							<?php endif; ?>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

	<!--LOGO TEXT BANNER /Zamboanga Barter | Online Market/ MEN, WOMEN, KIDS, ABOUT, HELP, CONTACT, -->
<div class="header">
	<div class="container">
		<div class="row inner">
			<div class="col-md-4 logo">
				<a href="index.php"><img src="assets/uploads/<?php echo $logo; ?>" alt="logo image"></a> Zamboanga Barter Online Market
			</div>
			
			<div class="col-md-5 right">
				<ul>
					
					<?php
					if(isset($_SESSION['customer'])) {
						?>
						<li><i class="fa fa-user"></i> <?php echo LANG_VALUE_13; ?> <?php echo $_SESSION['customer']['cust_name']; ?></li>
						<li><a href="dashboard.php"><i class="fa fa-home"></i> <?php echo LANG_VALUE_89; ?></a></li>
						<?php
					} else {
						?>
						<li><a href="login.php"><i class="fa fa-sign-in"></i> <?php echo LANG_VALUE_9; ?></a></li>
						<li><a href="registration.php"><i class="fa fa-user-plus"></i> <!--<?php echo LANG_VALUE_15; ?>-->Sign Up</a></li>
						<?php	
					}
					?>

					<li><a href="cart.php"><i class="fa fa-shopping-cart"></i> <?php echo LANG_VALUE_19; ?> (<?php echo LANG_VALUE_1; ?>
					<?php
					if(isset($_SESSION['cart_p_id'])) {
						$table_total_price = 0;
						$i=0;
	                    foreach($_SESSION['cart_p_qty'] as $key => $value) 
	                    {
	                        $i++;
	                        $arr_cart_p_qty[$i] = $value;
	                    }                    $i=0;
	                    foreach($_SESSION['cart_p_current_price'] as $key => $value) 
	                    {
	                        $i++;
	                        $arr_cart_p_current_price[$i] = $value;
	                    }
	                    for($i=1;$i<=count($arr_cart_p_qty);$i++) {
	                    	$row_total_price = $arr_cart_p_current_price[$i]*$arr_cart_p_qty[$i];
	                        $table_total_price = $table_total_price + $row_total_price;
	                    }
						echo $table_total_price;
					} else {
						echo '0.00';
					}
					?></a></li>
				</ul>
			</div>
			<div class="col-md-3 search-area">
				<form class="navbar-form navbar-left" role="search" action="search-result.php" method="get">
					<?php $csrf->echoInputField(); ?>
					<div class="form-group">
						<input type="text" class="form-control search-top" placeholder="<?php echo LANG_VALUE_2; ?>" name="search_text">
					</div>
					<button type="submit" class="btn btn-default"><?php echo LANG_VALUE_3; ?></button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="nav">
	<div class="container">
		<div class="row">
			<div class="col-md-12 pl_0 pr_0">
				<div class="menu-container">
					<div class="menu">
						<ul>
		<!--<li><a href="index.php">Home</a></li> -->
				<li><a href="product-category.php?id=1&type=top-category">Men</a>
				<ul>
				<li><a href="product-category.php?id=1&type=mid-category">Men Accessories</a>
				<ul>
				<li><a href="product-category.php?id=1&type=end-category">Headwear </a></li>
				<li><a href="product-category.php?id=2&type=end-category">Sunglasses</a></li>
				<li><a href="product-category.php?id=3&type=end-category">Watches</a></li>
				<li><a href="product-category.php?id=57&type=end-category">Belts</a></li>
				
				</ul>
				</li>
				<li><a href="product-category.php?id=2&type=mid-category">Men's Shoes</a>
				<ul>
				<li><a href="product-category.php?id=25&type=end-category">Casual Shoes</a></li>
				<li><a href="product-category.php?id=56&type=end-category">Formal Shoes</a></li> 
																	</ul>
											</li>
									<li><a href="product-category.php?id=8&type=mid-category">Bottoms</a>
												<ul>
																	<li><a href="product-category.php?id=16&type=end-category">Pants</a></li>
														<li><a href="product-category.php?id=17&type=end-category">Jeans</a></li>
																	<li><a href="product-category.php?id=18&type=end-category">Joggers</a></li>
																	<li><a href="product-category.php?id=19&type=end-category">Shorts</a></li>
																			</ul>
											</li>
								<li><a href="product-category.php?id=9&type=mid-category">T-shirts & Shirts</a>
												<ul>
																<li><a href="product-category.php?id=20&type=end-category">T-shirts</a></li>
								<li><a href="product-category.php?id=21&type=end-category">Casual Shirts</a></li>
														<li><a href="product-category.php?id=22&type=end-category">Formal Shirts</a></li>
					<li><a href="product-category.php?id=23&type=end-category">Polo Shirts</a></li>
							<li><a href="product-category.php?id=24&type=end-category">Vests</a></li>
																</ul>
											</li>
														</ul>
								</li>
					<li><a href="product-category.php?id=2&type=top-category">Women</a>
									<ul>
						<li><a href="product-category.php?id=3&type=mid-category">Beauty Products</a>
												<ul>
												
												<li><a href="product-category.php?id=39&type=end-category">Fragrance</a></li>
											<li><a href="product-category.php?id=40&type=end-category">Skincare</a></li>
											
											
											<li><a href="product-category.php?id=44&type=end-category">Lips</a></li>
											<li><a href="product-category.php?id=45&type=end-category">Face Care</a></li>
											
											</ul>
											
									<li><a href="product-category.php?id=4&type=mid-category">Accessories</a>
												<ul>
									<li><a href="product-category.php?id=8&type=end-category">Watches</a></li>
												<li><a href="product-category.php?id=9&type=end-category">Sunglasses</a></li>
									<li><a href="product-category.php?id=42&type=end-category">Jewellery</a></li>
										<li><a href="product-category.php?id=60&type=end-category">Bags</a></li>
								</ul>
											</li>
							<li><a href="product-category.php?id=6&type=mid-category">Shoes</a>
												<ul>
									<li><a href="product-category.php?id=12&type=end-category">Sandals</a></li>
									
									
								<li><a href="product-category.php?id=51&type=end-category">Sneakers</a></li>
												<li><a href="product-category.php?id=55&type=end-category">Slippers & Casual Shoes</a></li>
																	</ul>
											</li>
												<li><a href="product-category.php?id=7&type=mid-category">Clothing</a>
												<ul>
												<li><a href="product-category.php?id=14&type=end-category">Hoodies</a></li>
													
													<li><a href="product-category.php?id=32&type=end-category">Dresses</a></li>
													
																
											
																										</ul>
											</li>
																				</ul>
								</li>
						<li><a href="product-category.php?id=3&type=top-category">Kids</a>
									<ul>
							<li><a href="product-category.php?id=10&type=mid-category">Clothing</a>
												<ul>
							<li><a href="product-category.php?id=26&type=end-category">Boys</a></li>
											<li><a href="product-category.php?id=27&type=end-category">Girls</a></li>
											</ul>
											</li>
								<li><a href="product-category.php?id=11&type=mid-category">Shoes</a>
												<ul>
											<li><a href="product-category.php?id=28&type=end-category">Boys</a></li>
														<li><a href="product-category.php?id=29&type=end-category">Girls</a></li>
											</ul>
											</li>
																																																	<!--<li><a href="product-category.php?id=12&type=mid-category">Accessories</a>
																																																			<ul>
																																																	<li><a href="product-category.php?id=30&type=end-category">Boys</a></li>
																																																		<li><a href="product-category.php?id=31&type=end-category">Girls</a></li>
																																																</ul> -->
											</li>
																				</ul>
								</li> 
							<li><a href="product-category.php?id=4&amp;type=top-category">Foods</a>
							<li><a href="about.php">About Us</a></li>
							<li><a href="faq.php">Help</a></li>
							

							<li><a href="contact.php">Contact Us</a></li>   
							
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="bootstrap-touch-slider" class="carousel bs-slider fade control-round indicators-line" data-ride="carousel" data-pause="hover" data-interval="false" >

    <!-- Indicators -->
    <ol class="carousel-indicators">
                    <li data-target="#bootstrap-touch-slider" data-slide-to="0" class="active"></li>
                        <li data-target="#bootstrap-touch-slider" data-slide-to="1" ></li>
                        <li data-target="#bootstrap-touch-slider" data-slide-to="2" ></li>
                </ol>

    <!-- Wrapper For Slides -->
    <div class="carousel-inner" role="listbox">

                    <div class="item active" style="background-image:url(assets/uploads/slider-1.jpg);">
                <div class="bs-slider-overlay"></div>
                <div class="container">
                    <div class="row">
					<!--TEXT COLOR-->
				<!--	<p><font color="white">	<h1 data-animation="animated flipInX">Zamboanga Barter Online Market</h1></font> -->
					<!--END TEXT COLOR-->
                       <div class="slide-text slide_style_center"> 
				<!--	<marquee bgcolor="cyan"><h1 style="text-align:center;"> Zamboanga Barter Online Market </h1></marquee> -->
                      <h1 data-animation="animated fadeInDown">Zamboanga Barter Online Market</h1>
                           <!-- <p data-animation="animated fadeInDown">Accessories</p>
                            <a href="https://www.google.com/" target="_blank"  class="btn btn-primary" data-animation="animated fadeInDown">Shop Now</a> -->
                        </div>
                    </div>
                </div>
            </div>
                        <div class="item " style="background-image:url(assets/uploads/slider-2.jpg);">
                <div class="bs-slider-overlay"></div>
                <div class="container">
                    <div class="row">
                        <div class="slide-text slide_style_center">
						<!--<marquee><h1 style="text-align:center;"> WE OFFERS BEST PRODUCTS </h1></marquee>-->
                            <h1 data-animation="animated flipInX">WE OFFERS BEST BARTER PRODUCTS</h1>
                            <p data-animation="animated fadeInDown"></p>
                            <a href="https://Zamboanga-Barter-Online-Market.com" target="_blank"  class="btn btn-primary" data-animation="animated fadeInDown">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
                        <div class="item " style="background-image:url(assets/uploads/slider-1.jpg);">
                <div class="bs-slider-overlay"></div>
                <div class="container">
                    <div class="row">
                        <div class="slide-text slide_style_left">
                            <h1 data-animation="animated zoomInLeft">WE OFFERS QUALITY AND LOW PRICE.</h1>
                            <p data-animation="animated fadeInLeft">Shop Now For Great Deals</p>
                            <a href="#" target="_blank"  class="btn btn-primary" data-animation="animated fadeInLeft">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
                </div>

    <!-- Slider Left Control -->
    <a class="left carousel-control" href="#bootstrap-touch-slider" role="button" data-slide="prev">
        <span class="fa fa-angle-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>

    <!-- Slider Right Control -->
    <a class="right carousel-control" href="#bootstrap-touch-slider" role="button" data-slide="next">
        <span class="fa fa-angle-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>

</div>













<!--<section class="home-newsletter">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="single">
									<form action="" method="post">
					<input type="hidden" name="_csrf" value="e08f8d79363cd50b0e6e90f421a72d6c" />					<h2>Subscribe To Our Newsletter</h2>
					<div class="input-group">
			        	<input type="email" class="form-control" placeholder="Enter Your Email Address" name="email_subscribe">
			         	<span class="input-group-btn">
			         	<button class="btn btn-theme" type="submit" name="form_subscribe">Subscribe</button>
			         	</span>
			        </div>
				</div>
				</form>
			</div>
		</div>
	</div>
</section>
-->



<div class="footer-bottom">
	<div class="container">
		<div class="row">
			<div class="col-md-12 copyright">
				Zamboanga Barter Online Market System 		</div>
		</div>
	</div>
</div>

 <!--SIDE BAR -->
<a href="#" class="scrollup">
	<i class="fa fa-angle-up"></i>
</a>


<script src="assets/js/jquery-2.2.4.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="https://js.stripe.com/v2/"></script>
<script src="assets/js/megamenu.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/owl.animate.js"></script>
<script src="assets/js/jquery.bxslider.min.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script src="assets/js/rating.js"></script>
<script src="assets/js/jquery.touchSwipe.min.js"></script>
<script src="assets/js/bootstrap-touch-slider.js"></script>
<script src="assets/js/select2.full.min.js"></script>
<script src="assets/js/custom.js"></script>
<script>
	function confirmDelete()
	{
	    return confirm("Do you sure want to delete this data?");
	}
	$(document).ready(function () {
		advFieldsStatus = $('#advFieldsStatus').val();

		$('#paypal_form').hide();
		$('#stripe_form').hide();
		$('#bank_form').hide();

        $('#advFieldsStatus').on('change',function() {
            advFieldsStatus = $('#advFieldsStatus').val();
            if ( advFieldsStatus == '' ) {
            	$('#paypal_form').hide();
				$('#stripe_form').hide();
				$('#bank_form').hide();
            } else if ( advFieldsStatus == 'PayPal' ) {
               	$('#paypal_form').show();
				$('#stripe_form').hide();
				$('#bank_form').hide();
            } else if ( advFieldsStatus == 'Stripe' ) {
               	$('#paypal_form').hide();
				$('#stripe_form').show();
				$('#bank_form').hide();
            } else if ( advFieldsStatus == 'Bank Deposit' ) {
            	$('#paypal_form').hide();
				$('#stripe_form').hide();
				$('#bank_form').show();
            }
        });
	});


	$(document).on('submit', '#stripe_form', function () {
        // createToken returns immediately - the supplied callback submits the form if there are no errors
        $('#submit-button').prop("disabled", true);
        $("#msg-container").hide();
        Stripe.card.createToken({
            number: $('.card-number').val(),
            cvc: $('.card-cvc').val(),
            exp_month: $('.card-expiry-month').val(),
            exp_year: $('.card-expiry-year').val()
            // name: $('.card-holder-name').val()
        }, stripeResponseHandler);
        return false;
    });
    Stripe.setPublishableKey('pk_test_0SwMWadgu8DwmEcPdUPRsZ7b');
    function stripeResponseHandler(status, response) {
        if (response.error) {
            $('#submit-button').prop("disabled", false);
            $("#msg-container").html('<div style="color: red;border: 1px solid;margin: 10px 0px;padding: 5px;"><strong>Error:</strong> ' + response.error.message + '</div>');
            $("#msg-container").show();
        } else {
            var form$ = $("#stripe_form");
            var token = response['id'];
            form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
            form$.get(0).submit();
        }
    }

</script>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/619393196885f60a50bc051c/1fkk7d7c6';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</body>
</html>