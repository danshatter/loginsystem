<?php
function user_exists($username) {
	global $conn;
	$sql = "SELECT user_id FROM users WHERE username = '$username';";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck === 1) {
		return true;
	} else {
		return false;
	}
}

function email_exists($email) {
	global $conn;
	$sql = "SELECT user_id FROM users WHERE email = '$email';";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck === 1) {
		return true;
	} else {
		return false;
	}
}

function output_errors($errors) {
	$error = implode('<br/>', $errors);
	echo '<p style="color: red; text-align=left;">'.$error.'</p>';
}

function logged_in() {
	if (isset($_SESSION['user_id']) === true) {
		return true;
	}
}

function user_data($user_id) {
	global $conn;
	$sql = "SELECT * FROM users WHERE user_id = '$user_id';";
	$result = mysqli_query($conn, $sql);
	$resultCheck = mysqli_num_rows($result);
	if ($resultCheck === 1) {
		while ($row = mysqli_fetch_assoc($result)) {
			return $row;
		}
	} else {
		echo 'No data on user';
	}

}

function user_count() {
	global $conn;
	$sql = "SELECT user_id FROM users;";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_num_rows($result);
	return $row;
}

function active_user_count() {
	global $conn;
	$sql = "SELECT user_id FROM users WHERE is_active = '1';";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_num_rows($result);
	return $row;
}

function protect_page() {
	if (isset($_SESSION['user_id']) === false) {
		header('Location: '.SITE_ROOT.'/protected.php');
	}
}

function loggedin_redirect() {
	if (isset($_SESSION['user_id']) === true) {
		header('Location: '.SITE_ROOT.'/index.php');
	}
}

function admin_protect($user_id) {
	global $conn;
	$sql = "SELECT `is_admin` FROM `users` WHERE `user_id` = '$user_id';";
	$result = mysqli_query($conn, $sql);

	while ($data = mysqli_fetch_assoc($result)) {
		if ($data['is_admin'] != 1) {
			header('Location: '.SITE_ROOT.'/index.php');
			die();
		}
	}
}

function upload_errors($error) {
    if ($error === 0) {
        return 'No error';
    } elseif ($error === 1) {
        return 'is larger than upload_max_filesize. File must be less than 2MB';
    } elseif ($error === 2) {
        return 'is larger than MAX_FILE_SIZE. File must be less than MAX_FILE_SIZE';
    } elseif ($error === 3) {
        return 'failed because of partial upload';
    } elseif ($error === 4) {
        return 'No file selected';
    } elseif ($error === 6) {
        return 'failed because of no temporary directory';
    } elseif ($error === 7) {
        return 'failed because file can\'t write to disk';
    } else {
        return 'failed because of an error with the file extension';
    }
}

function activated($user_id) {
	global $conn;
	$sql = "SELECT `is_active` FROM `users` WHERE `user_id` = '$user_id';";
	$result = mysqli_query($conn, $sql);
	while ($data = mysqli_fetch_assoc($result)) {
		if (logged_in() === true && $data['is_active'] != 1) {
			session_unset();
			session_destroy();
			header('Location: '.SITE_ROOT.'/index.php');
			exit();
		}
	}
}

function force($user_id) {
	global $conn;
	$sql = "SELECT `recovery` FROM `users` WHERE `user_id` = '$user_id';";
	$result = mysqli_query($conn, $sql);
	$current_file = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
	while ($data = mysqli_fetch_assoc($result)) {
		if (logged_in() === true && $current_file !== 'changepassword' && $data['recovery'] == 1) {
			header('Location: '.SITE_ROOT.'/changepassword.php?force');
			die();
		}
	}
}