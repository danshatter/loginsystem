<?php
require_once 'core/init.php';
loggedin_redirect();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'C:/xampp/composer/vendor/autoload.php';
include_once 'includes/overall/header.php';
?>
<h1>Recover</h1>

<?php
if (isset($_SESSION['recover_success']) === true) {
    echo '<p style="color: green;">Please check your mail. Your request has been sent.</p>';
    unset($_SESSION['recover_success']);
} else {
    $mode_allowed = array('username', 'password');
    if (isset($_GET['mode']) === true && in_array($_GET['mode'], $mode_allowed) === true) {
        $mode = $_GET['mode'];
        if (isset($_POST['recover']) === true) {
            if (trim($_POST['email']) === "") {
                echo '<p style="color: red;">Please enter your email address</p>';
            } else {
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                if ($mode === 'username') {
                    $sql = "SELECT `username`, `first_name`, `last_name` FROM `users` WHERE `email` = '$email';";
                    if (mysqli_query($conn, $sql) == true) {
                        $result = mysqli_query($conn, $sql);
                        $resultCheck = mysqli_num_rows($result);
                        if ($resultCheck !== 1) {
                            echo '<p style="color: red;">This user does not exist.</p>';
                        } else {
                            while ($data = mysqli_fetch_assoc($result)) {
                                $mail = new PHPMailer(true);
                                $mail->isSMTP();
                                $mail->Host = EMAIL['host'];
                                $mail->SMTPAuth = true;
                                $mail->SMTPSecure = 'tls';
                                $mail->Port = EMAIL['port'];
                                $mail->Username = EMAIL['username'];
                                $mail->Password = EMAIL['password'];
                                $mail->isHTML(true);
                                $mail->SMTPDebug = 4;
                                $mail->setFrom(EMAIL['username'], DB['servername']);
                                $mail->addAddress($_POST['email'], $data['first_name'].' '.$data['last_name']);
                                $mail->Subject = 'Recover '.ucfirst($mode);
                                $mail->Body = 'Dear '.$data['first_name'].',<br/><br/>Your username is: '.$data['username'].'<br/><br/>Please endeavour to remember it this time.<br/><br/>Best wishes.';
                                if (!$mail->send()) {
                                    $mail->send();
                                }
                                $_SESSION['recover_success'] = '';
                                header('Location: '.SITE_ROOT.'/recover.php?mode='.$mode);
                            }
                        }
                    } else {
                        echo '<p style="color: red;">An error occurred. Please try again later</p>';
                    }
                    
                } elseif ($mode === 'password') {
                    $sql = "SELECT `username`, `first_name`, `last_name` FROM `users` WHERE `email` = '$email';";
                    if (mysqli_query($conn, $sql) == true) {
                        $result = mysqli_query($conn, $sql);
                        $resultCheck = mysqli_num_rows($result);
                        if ($resultCheck !== 1) {
                            echo '<p style="color: red;">This user does not exist.</p>';
                        } else {
                            $string = 'qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPLKJHGFDSAZXCVBNM1234567890';
                            $shuffle = str_shuffle($string);

                            $temp_password = substr($shuffle, 10, 8);
                            $temp_password_db = password_hash($temp_password, PASSWORD_DEFAULT);

                            while ($data = mysqli_fetch_assoc($result)) {
                                $sql2 = "UPDATE `users` SET `password` = '$temp_password_db', `recovery` = '1' WHERE `email` = '$email';";
                                if (mysqli_query($conn, $sql2) == true) {
                                    $mail = new PHPMailer(true);
                                    $mail->isSMTP();
                                    $mail->Host = EMAIL['host'];
                                    $mail->SMTPAuth = true;
                                    $mail->SMTPSecure = 'tls';
                                    $mail->Port = EMAIL['port'];
                                    $mail->Username = EMAIL['username'];
                                    $mail->Password = EMAIL['password'];
                                    $mail->isHTML(true);
                                    $mail->SMTPDebug = 4;
                                    $mail->setFrom(EMAIL['username'], DB['servername']);
                                    $mail->addAddress($_POST['email'], $data['first_name'].' '.$data['last_name']);
                                    $mail->Subject = 'Recover '.ucfirst($mode);
                                    $mail->Body = 'Dear '.$data['first_name'].',<br/><br/>Your temporary password is: '.$temp_password.'<br/><br/>Use this next time you try to log in.<br/><br/>P.S. Please remember your password is case-sensitive.<br/><br/>Best wishes.';
                                    if (!$mail->send()) {
                                        $mail->send();
                                    }  
                                    $_SESSION['recover_success'] = '';
                                    header('Location: '.SITE_ROOT.'/recover.php?mode='.$mode);
                                } else {
                                    echo '<p style="color: red;">An internal error occurred. Please try again later</p>';
                                }
                            }
                        }
                    } else {
                        echo '<p style="color: red;">An error occurred. Please try again later</p>';
                    }
                }
            }
        }
    } else {
        header('Location: '.SITE_ROOT.'/index.php');
    }
?>
<p style="color: red;">You have requested to recover your <?php echo $_GET['mode']; ?>.</p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $_SERVER['QUERY_STRING']; ?>" method="POST">
    <ul>
        <label for="email">Email<span style="color:red;">*</span></label><li><input type="email" id="email" name="email" required/></li><br/>
        <button type="submit" name="recover">Recover</button>
        <li></li>
    </ul>
</form>

<?php
}
include_once 'includes/overall/footer.php'; ?>