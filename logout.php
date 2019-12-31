<?php
require_once 'core/init.php';
if (isset($_POST['logout']) && isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
}
header('Location: '.SITE_ROOT.'/index.php');