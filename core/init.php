<?php
session_start();
ob_start();
$errors = array();
define('SITE_ROOT', '/loginsystem');
include_once 'credentials/secure.php';
include_once 'database/connect.php';
include_once 'functions/func.php';
if (isset($_SESSION['user_id'])) {
	$user_data = user_data($_SESSION['user_id']);
	activated($user_data['user_id']);
	force($user_data['user_id']);
}		
