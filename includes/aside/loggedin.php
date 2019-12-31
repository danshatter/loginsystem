<aside>
	<div class="widget">
		<h2>Hello <?php
			if ($user_data['is_admin'] == 1) {
				echo 'Admin ';
			}
		?><?php echo $user_data['first_name'];?>,</h2>
		<div class="inner">
		<?php
			if (isset($_SESSION['upload'])) {
				echo '<p style="color: green; text-align: left;">Profile picture uploaded successfully</p>';
				unset($_SESSION['upload']);
			}

			if (isset($_SESSION['delete'])) {
				echo '<p style="color: green; text-align: left;">Profile picture deleted successfully</p>';
				unset($_SESSION['delete']);
			}

			if (isset($_SESSION['change'])) {
				echo '<p style="color: green; text-align: left;">Profile picture updated successfully</p>';
				unset($_SESSION['change']);
			}
		?>
		<img src="<?php echo SITE_ROOT; ?>/<?php
			if ($user_data['profile_picture_status'] == 0) {
				if ($user_data['sex'] === 'Male') {
					echo 'profileimage/boy.png';
				} elseif ($user_data['sex'] === 'Female') {
					echo 'profileimage/girl.png';
				}
			} elseif ($user_data['profile_picture_status'] == 1) {
				echo $user_data['profile_picture_location'];
			}
		?>" style="width: 70%; height: 150px;"/><br/>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $_SERVER['QUERY_STRING']; ?>" method="POST" enctype="multipart/form-data">
		<?php if (logged_in() === true): ?>
				<input type="file" name="profile_pic"><br/>
			<?php if ($user_data['profile_picture_status'] == 0): ?>
				<button type="submit" name="upload_profile">Upload Profile</button><br/>
			<?php else: ?>
				<button type="submit" name="change_profile">Change Profile</button><br/>
				<button type="submit" name="delete_profile">Delete Profile Picture</button>
			<?php endif; ?>
		<?php endif; ?>
		</form>
<?php
if (isset($_POST['upload_profile'])) {
	$profile_pic = $_FILES['profile_pic'];
	$name = $profile_pic['name'];
	$size = $profile_pic['size'];
	$error = $profile_pic['error'];
	$tmp_name = $profile_pic['tmp_name'];
	$type = strtolower($profile_pic['type']);

	if ($error !== 0) {
		echo '<p style="color: red; text-align: left;">'.$name.' '.upload_errors($error).'</p>';
	} else {
		$allowed = array('image/jpg', 'image/jpeg', 'image/png');
		if (!in_array($type, $allowed)) {
			echo '<p style="color: red; text-align: left;">Your profile picture must be \'jpg\', \'jpeg\', or \'png\' format</p>';
		} else {
			$a = explode('.', $name);
			$ext = strtolower(end($a));
			$allowed = array('jpg', 'jpeg', 'png');
			if (!in_array($ext, $allowed)) {
				echo '<p style="color: red; text-align: left;">We are having an issue with the file extension. Please endeavour to fix it or upload another picture</p>';
			} else {
				if (!is_uploaded_file($tmp_name)) {
					echo '<p style="color: red; text-align: left;">This action is not allowed</p>';
				} else {
					$filename = $user_data['username'].'.'.$ext;
					$file_destination = 'profileimage/'.$filename;
					if (move_uploaded_file($tmp_name, $file_destination) === true) {
						$user_id = $user_data['user_id'];
						$sql = "UPDATE `users` SET `profile_picture_status` = '1', `profile_picture_location` = '$file_destination' WHERE `user_id` = '$user_id';";
						if (mysqli_query($conn, $sql) == true) {
							$_SESSION['upload'] = 'success';
							header('Location: '.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
						} else {
							echo '<p style="color: red; text-align: left;">An internal error occurred</p>'; 
						}
					} else {
						echo '<p style="color: red; text-align: left;">File upload was not successful</p>'; 
					}
				}
			}
		}			
	}
}

if (isset($_POST['delete_profile'])) {
	$error = array();
	$file = glob('profileimage/'.$user_data['username'].'*');
	for ($i=0; $i < count($file); $i++) { 
		if (unlink($file[$i]) === false) {
			echo '<p style="color: red; text-align: left;">An internal error occurred while deleting your profile picture</p>';
			$error[] = 'not successful';
		}
	}

	if (count($error) === 0) {
		$user_id = $user_data['user_id'];
		$sql = "UPDATE `users` SET `profile_picture_status` = '0', `profile_picture_location` = '' WHERE `user_id` = '$user_id';";
		if (mysqli_query($conn, $sql) == true) {
			$_SESSION['delete'] = 'success';
			header('Location: '.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
		} else {
			echo '<p style="color: red; text-align: left;">An internal error occurred while deleting your profile</p>';
		}
	}
}

if (isset($_POST['change_profile'])) {
	$profile_pic = $_FILES['profile_pic'];
	$name = $profile_pic['name'];
	$size = $profile_pic['size'];
	$error = $profile_pic['error'];
	$tmp_name = $profile_pic['tmp_name'];
	$type = $profile_pic['type'];

	if ($error !== 0) {
		echo '<p style="color: red; text-align: left;">'.$name.' '.upload_errors($error).'</p>';
	} else {
		$allowed = array('image/jpg', 'image/jpeg', 'image/png');
		if (!in_array($type, $allowed)) {
			echo '<p style="color: red; text-align: left;">Your profile picture must be \'jpg\', \'jpeg\', or \'png\' format</p>';
		} else {
			$a = explode('.', $name);
			$ext = strtolower(end($a));
			$allowed = array('jpg', 'jpeg', 'png');
			if (!in_array($ext, $allowed)) {
				echo '<p style="color: red; text-align: left;">We are having an issue with the file extension. Please endeavour to fix it or upload another picture</p>';
			} else {
				if (!is_uploaded_file($tmp_name)) {
					echo '<p style="color: red; text-align: left;">This action is not allowed</p>';
				} else {
					$file = glob('profileimage/'.$user_data['username'].'*');
					$errors = array();
					for ($i=0; $i < count($file); $i++) { 
						if (unlink($file[$i]) === false) {
							echo '<p style="color: red; text-align: left;">An internal error occurred while changing your profile picture</p>';
							$errors[] = 'not successful';
						}
					}

					if (count($errors) === 0) {
						$filename = $user_data['username'].'.'.$ext;
						$file_destination = 'profileimage/'.$filename;
						if (move_uploaded_file($tmp_name, $file_destination) === true) {
							$_SESSION['change'] = 'success';
							header('Location: '.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
						} else {
							echo '<p style="color: red; text-align: left;">File change was not successful</p>'; 
						}
					}
				}
			}
		}			
	}
}
?>
<form action="<?php echo SITE_ROOT; ?>/logout.php" method="POST">
	<ul>
		<li><button type="submit" name="logout">Logout</button><br/><br/></li>
	</ul>
</form>
	<ul>
		<li><a href="<?php echo SITE_ROOT; ?>/changepassword.php">Change Password</a></li>
		<li><a href="<?php echo SITE_ROOT; ?>/settings.php">Change Settings</a></li>
	</ul>
		</div>
	</div>
<br/><br/>