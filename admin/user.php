<?php require_once('header.php'); ?>

<!--<section class="content-header">
	<h1>User</h1>
</section> -->
&nbsp;<!--<div class="graph-form">
	<div class="form-body">
	<form method="post"> 
									
	<div class="form-group"> <label for="exampleInputEmail1">Add User</label> 
		<select name="accounttype" class="form-control select2" required="true">
		<option value="">Choose Account Type</option>
		<option value="Active Account">Admin</option> 
		<option value="Inactive Account">Inactive Account</option>
		<option value="Contact/Lead">Contact/Lead</option>
		<option value="Unknown">Unknown</option> 
		
	</select> </div>
	<div class="form-group"> <label for="exampleInputEmail1">Contact Name</label> <input type="text" name="cname" placeholder="Contact Name" value="" class="form-control" required="true"> </div>
	<!--<div class="form-group"> <label for="exampleInputEmail1">Company Name</label> <input type="text" name="comname" placeholder="Company Name" value="" class="form-control" required="true"> </div> 
	<div class="form-group"> <label for="exampleInputEmail1">Address</label> <textarea type="text" name="address" placeholder="Address" value="" class="form-control" required="true" rows="4" cols="3"></textarea> </div>
	<!--<div class="form-group"> <label for="exampleInputEmail1">City</label> <input type="text" name="city" placeholder="City" value="" class="form-control" required="true"> </div>-->
	<!--<div class="form-group"> <label for="exampleInputEmail1">State</label> <input type="text" name="state" placeholder="State" value="" class="form-control" required="true"> </div>-->
	<!--<div class="form-group"> <label for="exampleInputEmail1">Zip Code</label> <input type="text" name="zcode" placeholder="Zip Code" value="" class="form-control" required="true"> </div>
	<div class="form-group"> <label for="exampleInputEmail1">Work Phone Number</label><input type="text" name="wphnumber" value="" placeholder="Work Phone Number" class="form-control" maxlength="10" required="true" pattern="[0-9]+"> </div>
	<div class="form-group"> <label for="exampleInputEmail1">Cell Phone Number</label><input type="text" name="cellphnumber" value="" placeholder="Cell Phone Number" class="form-control" maxlength="10" pattern="[0-9]+"> </div>
	<!--<div class="form-group"> <label for="exampleInputEmail1">Other Phone Number</label><input type="text" name="ophnumber" value="" placeholder="Work Phone Number" class="form-control" maxlength="10" pattern="[0-9]+"> </div> 
	<div class="form-group"> <label for="exampleInputEmail1">Email Address</label> <input type="email" name="email" value="" placeholder="Email address" class="form-control" required="true"> </div> 
<div class="form-group"> <label for="exampleInputEmail1">Password</label>
	<input placeholder="password" type="password" name="password" required="true" id="password" class="form-control">
</div>
	<div class="form-group"> <label for="exampleInputPassword1">Website Address</label> <input type="text" name="websiteadd" value="" placeholder="Website Address" required="true" class="form-control"> </div>
	<div class="form-group"> <label for="exampleInputEmail1">Notes</label> <textarea type="text" name="notes" placeholder="Notes" value="" class="form-control" required="true" rows="4" cols="3"></textarea> </div>

	
	 <button type="submit" class="btn btn-default" name="submit" id="submit">Save</button> </form> 
</div>
</div> -->
 
<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {

	if($_SESSION['user']['role'] == 'Super Admin') {

		$valid = 1;

	    if(empty($_POST['full_name'])) {
	        $valid = 0;
	        $error_message .= "Name can not be empty<br>";
	    }

	    if(empty($_POST['email'])) {
	        $valid = 0;
	        $error_message .= 'Email address can not be empty<br>';
	    } else {
	    	if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
		        $valid = 0;
		        $error_message .= 'Email address must be valid<br>';
		    } else {
		    	// current email address that is in the database
		    	$statement = $pdo->prepare("SELECT * FROM tbl_user WHERE id=?");
				$statement->execute(array($_SESSION['user']['id']));
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);
				foreach($result as $row) {
					$current_email = $row['email'];
				}

		    	$statement = $pdo->prepare("SELECT * FROM tbl_user WHERE email=? and email!=?");
		    	$statement->execute(array($_POST['email'],$current_email));
		    	$total = $statement->rowCount();							
		    	if($total) {
		    		$valid = 0;
		        	$error_message .= 'Email address already exists<br>';
		    	}
		    }
	    }

	    if($valid == 1) {
			
			$_SESSION['user']['full_name'] = $_POST['full_name'];
	    	$_SESSION['user']['email'] = $_POST['email'];

			// updating the database
			$statement = $pdo->prepare("UPDATE tbl_user SET full_name=?, email=?, phone=? WHERE id=?");
			$statement->execute(array($_POST['full_name'],$_POST['email'],$_POST['phone'],$_SESSION['user']['id']));

	    	$success_message = 'User Information is updated successfully.';
	    }
	}
	else {
		$_SESSION['user']['phone'] = $_POST['phone'];

		// updating the database
		$statement = $pdo->prepare("UPDATE tbl_user SET phone=? WHERE id=?");
		$statement->execute(array($_POST['phone'],$_SESSION['user']['id']));

		$success_message = 'User Information is updated successfully.';	
	}
}

if(isset($_POST['form2'])) {

	$valid = 1;

	$path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path!='') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

    if($valid == 1) {

    	// removing the existing photo
    	if($_SESSION['user']['photo']!='') {
    		unlink('../assets/uploads/'.$_SESSION['user']['photo']);	
    	}

    	// updating the data
    	$final_name = 'user-'.$_SESSION['user']['id'].'.'.$ext;
        move_uploaded_file( $path_tmp, '../assets/uploads/'.$final_name );
        $_SESSION['user']['photo'] = $final_name;

        // updating the database
		$statement = $pdo->prepare("UPDATE tbl_user SET photo=? WHERE id=?");
		$statement->execute(array($final_name,$_SESSION['user']['id']));

        $success_message = 'User Photo is updated successfully.';
    	
    }
}

if(isset($_POST['form3'])) {
	$valid = 1;

	if( empty($_POST['password']) || empty($_POST['re_password']) ) {
        $valid = 0;
        $error_message .= "Password can not be empty<br>";
    }

    if( !empty($_POST['password']) && !empty($_POST['re_password']) ) {
    	if($_POST['password'] != $_POST['re_password']) {
	    	$valid = 0;
	        $error_message .= "Passwords do not match<br>";	
    	}        
    }

    if($valid == 1) {

    	$_SESSION['user']['password'] = md5($_POST['password']);

    	// updating the database
		$statement = $pdo->prepare("UPDATE tbl_user SET password=? WHERE id=?");
		$statement->execute(array(md5($_POST['password']),$_SESSION['user']['id']));

    	$success_message = 'User Password is updated successfully.';
    }
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add User</h1>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<button><li><a href="/././ecommerce/adduser/clientms/admin/add-client.php">Add User</a></li></button>
	</div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_user WHERE id=?");
$statement->execute(array($_SESSION['user']['id']));
$statement->rowCount();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	$full_name = $row['full_name'];
	$email     = $row['email'];
	$phone     = $row['phone'];
	$photo     = $row['photo'];
	$status    = $row['status'];
	$role      = $row['role'];
}
?>


<!--<section class="content">

	<div class="row">
		<div class="col-md-12">
				
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_1" data-toggle="tab">Update Information</a></li>
						<li><a href="#tab_2" data-toggle="tab">Update Photo</a></li>
						<li><a href="#tab_3" data-toggle="tab">Update Password</a></li>
					</ul> -->
	<!--				<div class="tab-content">
          				<div class="tab-pane active" id="tab_1">
							
							<form class="form-horizontal" action="" method="post">
							<div class="box box-info">
								<div class="box-body">
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Name <span>*</span></label>
										<?php
										if($_SESSION['user']['role'] == 'Super Admin') {
											?>
												<div class="col-sm-4">
													<input type="text" class="form-control" name="full_name" value="<?php echo $full_name; ?>">
												</div>
											<?php
										} else {
											?>
												<div class="col-sm-4" style="padding-top:7px;">
													<?php echo $full_name; ?>
												</div>
											<?php
										}
										?>
										
									</div>
									<div class="form-group">
							            <label for="" class="col-sm-2 control-label">Existing Photo</label>
							            <div class="col-sm-6" style="padding-top:6px;">
										<li><a href="#tab_2" data-toggle="tab">Update Photo</a></li>
							               <!-- <img src="../assets/uploads/<?php echo $photo; ?>" class="existing-photo" width="140"> -->
							 <!--           </div>
							        </div>
									
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Email Address <span>*</span></label>
										<?php
										if($_SESSION['user']['role'] == 'Super Admin') {
											?>
												<div class="col-sm-4">
													<input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
												</div>
											<?php
										} else {
											?>
											<div class="col-sm-4" style="padding-top:7px;">
												<?php echo $email; ?>
											</div>
											<?php
										}
										?>
										
									</div>
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Phone </label>
										<div class="col-sm-4">
											<input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>">
										</div>
									</div>
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Role <span>*</span></label>
										<div class="col-sm-4" style="padding-top:7px;">
											<!--<?php echo $role; ?>--><!--Admin -->
				<!--						</div>
									</div>
									<div class="form-group">
										<label for="" class="col-sm-2 control-label"></label>
										<div class="col-sm-6">
											<button type="submit" class="btn btn-success pull-left" name="form1">Add User</button>
										</div>
									</div>
								</div>
							</div>
							</form>
          				</div>
          				<div class="tab-pane" id="tab_2">
							<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
							<div class="box box-info">
								<div class="box-body">
									<div class="form-group">
							            <label for="" class="col-sm-2 control-label">New Photo</label>
							            <div class="col-sm-6" style="padding-top:6px;">
							                <input type="file" name="photo">
							            </div>
							        </div>
							        <div class="form-group">
										<label for="" class="col-sm-2 control-label"></label>
										<div class="col-sm-6">
											<button type="submit" class="btn btn-success pull-left" name="form2">Update Photo</button>
										</div>
									</div>
								</div>
							</div>
							</form>
          				</div>
          				<div class="tab-pane" id="tab_3">
							<form class="form-horizontal" action="" method="post">
							<div class="box box-info">
								<div class="box-body">
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Password </label>
										<div class="col-sm-4">
											<input type="password" class="form-control" name="password">
										</div>
									</div>
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Retype Password </label>
										<div class="col-sm-4">
											<input type="password" class="form-control" name="re_password">
										</div>
									</div>
							        <div class="form-group">
										<label for="" class="col-sm-2 control-label"></label>
										<div class="col-sm-6">
											<button type="submit" class="btn btn-success pull-left" name="form3">Update Password</button>
										</div>
									</div>
								</div>
							</div>
							</form>

          				</div>
          			</div>
				</div>			

		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_top_category");
$statement->execute();
$total_top_category = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_mid_category");
$statement->execute();
$total_mid_category = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_end_category");
$statement->execute();
$total_end_category = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_product");
$statement->execute();
$total_product = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Completed'));
$total_order_completed = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE shipping_status=?");
$statement->execute(array('Completed'));
$total_shipping_completed = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Pending'));
$total_order_pending = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=? AND shipping_status=?");
$statement->execute(array('Completed','Pending'));
$total_order_complete_shipping_pending = $statement->rowCount();
?>

<!-- Start code Input here to add user -->

<?php require_once('footer.php'); ?>