<?php
require_once 'core/init.php';
	if (isset($_SESSION['user_id'])) {
		if (isset($_SERVER['HTTP_REFERER'])) {
			if (pathinfo($_SERVER['HTTP_REFERER'], PATHINFO_FILENAME) !== 'protected') {
				header('Location: '.$_SERVER['HTTP_REFERER']);
			} else {
				header('Location: '.SITE_ROOT.'/index.php');
			}
		} else {
			header('Location: '.SITE_ROOT.'/index');
		}
	}
include_once 'includes/overall/header.php';
?>

<h1>Sorry, You cannot view this page</h1>
<p>You need to login to do that.</p>

<?php include_once 'includes/overall/footer.php'; ?>