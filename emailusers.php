<?php
require_once 'core/init.php'; 
protect_page();
admin_protect($user_data['user_id']);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'C:/xampp/composer/vendor/autoload.php';
include_once 'includes/overall/header.php';
?>
<h1>Mail</h1>
<?php
if (isset($_SESSION['emailusers_success']) === true) {
    echo '<p style="color: green;">You have sent the mail successfully.</p>';
    unset($_SESSION['emailusers_success']);
} else {
    if (isset($_POST['sendemail'])) {
        $body = nl2br(trim(ucwords($_POST['body'])));
        $subject = ucwords($_POST['subject']);

        if ($body === "" || $subject === "") {
            echo '<p style="color: red; text-align: left;">Please fill in all fields.</p>';
        } else {
            $sql = "SELECT `first_name`, `last_name`, `username`, `email` FROM `users` WHERE `receive_mail` = '1' AND `is_admin` = '0';";
            if (mysqli_query($conn, $sql) == false) {
                echo '<p style="color: red;">An internal error occurred</p>';
            } else {
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) === 0) {
                    echo '<p style="color: red;">No users want to receive emails presently</p>';
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
                        $mail->Subject = $subject;
                        $mail->Body = 'Dear '.$data['first_name'].',<br/><br/>'.$body.'<br><br>Thank you '.$data['username'].'.';
                        if (!$mail->send()) {
                            $mail->send();
                        }
                    }
                    $_SESSION['emailusers_success'] = '';
                    header('Location: '.$_SERVER['PHP_SELF']);
                }  
            }
        }
    }
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <ul>    
        <label for="subject">Subject<span style="color:red;">*</span></label><li><input type="text" id="subject" name="subject" value="<?php echo (isset($_POST['subject'])) ? $_POST['subject'] : ''; ?>" required/></li><br/>
        <label for="body">Body<span style="color:red;">*</span></label><li><textarea name="body" id="body" cols="40" rows="10" required><?php echo (isset($_POST['body'])) ? $_POST['body'] : ''; ?></textarea><br/><br/>
        <button type="submit" name="sendemail">Send</button>
    </ul>
</form>

<?php
}
include_once 'includes/overall/footer.php';
?>