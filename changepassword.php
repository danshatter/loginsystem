<?php require_once 'core/init.php';
protect_page();
include_once 'includes/overall/header.php';
?>
<h1>Change Password</h1>

<?php
if (isset($_SESSION['change_password_success'])) {
    echo '<p style="color: green; text-align: left;">Your password has been changed successfully</p>';
    unset($_SESSION['change_password_success']);
} else {
    if (isset($_GET['force']) === true && $user_data['recovery'] == 1) {
        echo '<p style="color: red;">You have to change your password in order to proceed. The old password is the temporary password sent to your mail.<p>';
    }

    if (isset($_POST['change_password'])) {
        $old_password = mysqli_real_escape_string($conn, $_POST['old_password']);
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
        $new_password_again = mysqli_real_escape_string($conn, $_POST['new_password_again']);

        if ($old_password === "" || $new_password === "" || $new_password_again === "") {
            echo '<p style="color: red; text-align: left;">All fields with an asterisk must be filled</p>';
        } else {
            if (password_verify($old_password, $user_data['password']) === false) {
                echo '<p style="color: red; text-align: left;">Your old password is wrong</p>';
            } elseif (password_verify($old_password, $user_data['password']) === true) {
                if ($old_password === $new_password) {
                    echo '<p style="color: red; text-align: left;">Your new password and old password must be different</p>';
                } else {
                    if ($new_password !== $new_password_again) {
                        echo '<p style="color: red; text-align: left;">Your new passwords do not match</p>';
                    } else {
                        if (strlen($new_password) < 8 || strlen($new_password) > 25) {
                            $errors[] = 'Your password must be at least 8 characters and not more than 25 characters';
                        }
                    
                        if (preg_match("/[a-z]/", $new_password) == false) {
                            $errors[] = 'Your password must contain a small alphabet';
                        }
                    
                        if (preg_match("/[A-Z]/", $new_password) == false) {
                            $errors[] = 'Your password must contain a capital alphabet';
                        }
                    
                        if (preg_match("/[0-9]/", $new_password) == false) {
                            $errors[] = 'Your password must contain a number';
                        }
                    
                        if (preg_match("/[@_$%&?!#]/", $new_password) == false) {
                            $errors[] = 'Your password must contain a special character (@_$%&?!#)';
                        }
    
                        if (empty($errors) === false) {
                            output_errors($errors);
                        } else {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $user_id = $user_data['user_id'];
    
                            $sql = "UPDATE users SET `password` = '$hashed_password', `recovery` = '0' WHERE user_id = '$user_id';";
                            if (mysqli_query($conn, $sql) === true) {
                                $_SESSION['change_password_success'] = '';
                                header('Location: '.$_SERVER['PHP_SELF']);
                            } else {
                                echo 'An error occurred';
                            }
                        }
                    }
                }      
            }
        }
    }
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <ul>    
        <label for="old_password">Old Password<span style="color:red;">*</span></label><li><input type="password" id="old_password" name="old_password" required/></li><br/>
        <label for="new_password">New Password<span style="color:red;">*</span></label><li><input type="password" id="new_password" name="new password" required/></li><br/>
        <label for="new_password_again">New Password again<span style="color:red;">*</span></label><li><input type="password" id="new_password_again" name="new_password_again" required/></li><br/>
        <button type="submit" name="change_password">Change Password</button>
    </ul>
</form>

<?php
}
include_once 'includes/overall/footer.php';
?>