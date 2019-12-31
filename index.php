<?php
require_once 'core/init.php';
include_once 'includes/overall/header.php';
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
	<input type="text" name="search" id="search" style="width: 50%;" onkeyup="loadajax();" placeholder="Search Users">
	<div id="ajax" style="width: 50%;"></div>
</form>
<h1>Home</h1>
<p>Just a template</p>

<?php
	if (logged_in() === true) {
		echo '<h1>You are logged in.</h1>';
	} else {
		echo '<h1>Not Logged in.</h1>';
	}

	if (logged_in() === true && $user_data['is_admin'] == 1) {
		echo '<h1>You are an Administrator</h1>';
	}
?>

<script>
	function loadajax() {
		if (window.XMLHttpRequest) {
			xmlhttp = new XMLHttpRequest();
		} else {
			xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
		}

		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById('ajax').innerHTML = xmlhttp.responseText;
			}
		}

		xmlhttp.open('GET', 'ajax.php?search='+document.getElementById('search').value, true);
		xmlhttp.send();
	}
</script>
<?php include_once 'includes/overall/footer.php'; ?>

