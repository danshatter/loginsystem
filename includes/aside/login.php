<aside>
	<div class="widget">
		<h2>Widget Header</h2>
		<div class="inner">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<ul>
					Username/email: <li><input type="text" name="useremail" value="<?php echo (isset($_POST['useremail'])) ? $_POST['useremail'] : ''; ?>"></li>
					Password: <li><input type="password" name="password"></li>
					<button type="submit" name="login">Login</button>
				</ul>
			</form>
		</div>
	</div>
    <?php
	if (isset($_POST['login'])) {
		$password = mysqli_real_escape_string($conn, $_POST['password']);
		$useremail = trim(mysqli_real_escape_string($conn, $_POST['useremail']));

		if (trim($useremail) === "" || $password === "") {
			echo '<p style="color: red; text-align: center;">Please fill in all fields</p>';
		} else {
			$sql = "SELECT `user_id`, `is_active`, `password` FROM users WHERE username = '$useremail' OR email = '$useremail';";
			$result = mysqli_query($conn, $sql);
			$resultCheck = mysqli_num_rows($result);

			if ($resultCheck !== 1) {
				echo '<p style="color: red; text-align: center;">Username/email is not registered. Please check your input again</p>';
			} elseif ($resultCheck === 1) {
                while ($data = mysqli_fetch_assoc($result)) {
                    $password_dehash = password_verify($password, $data['password']);
                    if ($password_dehash === false) {
                        echo '<p style="color: red; text-align: center;">Invalid Username/Email and Password Combination.</p>';
                    } elseif ($password_dehash === true) {
                        if ($data['is_active'] != 1)  {
                            echo '<p style="color: red; text-align: center;">You have not activated your account.</p>';
                        } else {
                            $_SESSION['user_id'] = $data['user_id'];
                            header('Location: '.$_SERVER['PHP_SELF']);
                        }
                    }
                }		
			}
		}			
	}
?>
<ul>
	<li>Forgotten <a href="<?php echo SITE_ROOT; ?>/recover.php?mode=username">Username</a> or <a href="<?php echo SITE_ROOT; ?>/recover.php?mode=password">Password</a>?</li>
	<li>Don't have an account? <a href="<?php echo SITE_ROOT; ?>/register.php">Sign up</a> now.</li>
</ul>
<br/><br/>