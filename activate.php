<?php require_once 'core/init.php';
loggedin_redirect();
include_once 'includes/overall/header.php';

if (isset($_SESSION['activate_success'])) {
    echo '<p style="color: green;">Your account has been activated. You can now log in.</p>';
    unset($_SESSION['activate_success']);
} else {
    if (isset($_GET['code']) && isset($_GET['email'])) {
        $code = mysqli_real_escape_string($conn, $_GET['code']);
        $email = mysqli_real_escape_string($conn, $_GET['email']);

        $sql = "SELECT `is_active`, `code`, `date_registered` FROM users WHERE `code` = '$code' AND `email` = '$email';";
        if (mysqli_query($conn, $sql)) {
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if ($resultCheck !== 1) {
                echo '<p style="color: red;">Something broke. Just simply click on the link. If you choose to copy and paste the link, please don\'t edit the link. Copy and paste it the way it was sent to you.</p>';
            } else {
                while ($data = mysqli_fetch_assoc($result)) {
                    if ($data['is_active'] == 1) {
                        echo '<p style="color: green;">Your account has already been activated</p>';
                    } else {
                        if ($_SERVER['REQUEST_TIME'] > strtotime($data['date_registered']) + 300) {
                            echo '<p style="color: red;">Your link has expired</p>';
                        } else {
                            $sql = "UPDATE users SET `is_active` = '1' WHERE `email` = '$email';";
                            if (mysqli_query($conn, $sql) === true) {
                                $_SESSION['activate_success'] = '';
                                header('Location: '.$_SERVER['PHP_SELF'].'?email='.$_GET['email'].'&code='.$_GET['code']);
                            }  else {
                                echo '<p style="color: red;">An error occured. Please try to confirm the link again.</p>';
                            }
                        }
                    }
                }
            }
        }
    } else {
        header('Location: '.SITE_ROOT.'/index.php');
    }
}

include_once 'includes/overall/footer.php';