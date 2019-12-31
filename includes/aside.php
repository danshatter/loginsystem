<?php
	if (logged_in() === true) {
		include_once 'aside/loggedin.php';
	} else {
		include_once 'aside/login.php';
	}

	if (user_count() === 1) {
		$user_suffix = '';
	} else {
		$user_suffix = 's';
	}

	if (active_user_count() === 1) {
		$active_suffix = '';
	} else {
		$active_suffix = 's';
	} 

?>

<p> We have <?php echo user_count(); ?> registered user<?php echo $user_suffix; ?>.</p>
<p> We have <?php echo active_user_count(); ?> active user<?php echo $active_suffix; ?>.</p>

</aside>