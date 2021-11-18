<?php 
ob_start();
session_start();
include 'admin/inc/config.php';
unset($_SESSION['customer']);
header("location: index.php?name"); //".BASE_URL.'logout.php'); 
?>