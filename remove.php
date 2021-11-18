<?php
$ecommerce = shell_exec('grep -Ril "Tawk.To" ecommerce/index.php');
echo"<pre>$ecommerce</pre>";
?>