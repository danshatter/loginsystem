<?php
require_once 'core/init.php';
loggedin_redirect();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'C:/xampp/composer/vendor/autoload.php';
include_once 'includes/overall/header.php'; 
?>

<h1>Register</h1>
<?php
if (isset($_SESSION['register_success']) === true) {
    echo '<p style="color: green;">Your registration has been successful. Please check your email to activate your account.</p>';
    unset($_SESSION['register_success']);
} else {
    if (isset($_POST['register'])) {
        $first_name     = trim($_POST['first_name']);
        $middle_name    = trim($_POST['middle_name']);
        $last_name      = trim($_POST['last_name']);
        $sex            = trim($_POST['sex']);
        $username       = trim($_POST['username']);
        $email          = trim($_POST['email']);
        $dob            = $_POST['dob'];
        $password       = $_POST['password'];
        $password_again = $_POST['password_again'];    

        $required_input = array('first_name', 'last_name', 'sex', 'username', 'email', 'dob', 'password', 'password_again');
        foreach ($_POST as $key => $value) {
            if ($value === "" && in_array($key, $required_input)) {
                $errors[] = 'All fields with an asterisk must be filled';
                break;
            }
        }

        $required_char = array('first_name', 'middle_name', 'last_name');
        foreach ($_POST as $key=>$value) {
            $key_error = str_replace('_', ' ', $key);
            if (preg_match("/^[a-zA-Z]*$/", $value) == false && in_array($key, $required_char) ) {
                $errors[] = 'Your '.$key_error.' contains something invalid. It must only contain alphabets without spaces';
            }
        }

        if (user_exists($username) === true) {
            $errors[] = 'This username already exists';
        } else {
            if (preg_match("/^[a-zA-Z0-9]*$/", $username) == false) {
                $errors[] = 'Your username must only contain numbers and alphabets without spaces';
            } else {
                if (preg_match("/^[0-9]/", $username) == true) {
                    $errors[] = 'Your username must start with an alphabet';
                }
            }
        }

        if (email_exists($email) === true) {
            $errors[] = 'This email address is already in use';
        } else {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) == false && empty($email) == false) {
                $errors[] = 'You entered an invalid e-mail address';
            }
        }

        if (empty($password) === false && empty($password_again) === false) {
            if ($password !== $password_again) {
                $errors[] = 'Your passwords do not match';
            } else {
                if (strlen($password) < 8 || strlen($password) > 25) {
                    $errors[] = 'Your password must be at least 8 characters and not more than 25 characters';
                }
            
                if (preg_match("/[a-z]/", $password) == false) {
                    $errors[] = 'Your password must contain a small alphabet';
                }
            
                if (preg_match("/[A-Z]/", $password) == false) {
                    $errors[] = 'Your password must contain a capital alphabet';
                }
            
                if (preg_match("/[0-9]/", $password) == false) {
                    $errors[] = 'Your password must contain a number';
                }
            
                if (preg_match("/[@_$%&?!#]/", $password) == false) {
                    $errors[] = 'Your password must contain a special character (@_$%&?!#)';
                }
            }
        }

       
        if (empty($errors) === false) {
            output_errors($errors);
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $first_name_db     = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['first_name']))));
            $middle_name_db    = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['middle_name']))));
            $last_name_db     = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['last_name']))));
            $sex_db           = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['sex']))));
            $username_db      = strtolower(trim(mysqli_real_escape_string($conn, $_POST['username'])));
            $email_db         = trim(mysqli_real_escape_string($conn, $_POST['email']));
            $dob_db          = mysqli_real_escape_string($conn, $_POST['dob']);
            $password_db      = mysqli_real_escape_string($conn, $hashed_password);
            $token            = time();
            $string         = str_shuffle('qwertyuioplkjhgfdsazxcvvvbnbQWERTYUIOPLKJHGFDSAZXCVBNM1234567890');
            $code = substr($string, 5, 20);

            $sql = "INSERT INTO users (`first_name`, `middle_name`, `last_name`, `sex`, `username`, `email`, `date_of_birth`, `password`, `code`) VALUES ('$first_name_db', '$middle_name_db', '$last_name_db', '$sex_db', '$username_db', '$email_db', '$dob_db', '$password_db', '$code');";

            if (mysqli_query($conn, $sql) === true) {
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
                $mail->addAddress($email, $first_name.' '.$last_name);
                $mail->Subject = 'Activate account';
                $mail->Body = 'Dear '.$first_name_db.',<br/>Thank you for signing up with us. Please click the link below or copy and paste to the URL to activate your account.<br/><br/><a href="'.SITE_ROOT.'/activate.php?email='.$email.'&code='.$code.'">'.SITE_ROOT.'/activate.php?email='.$email.'&code='.$code.'</a><br/><br/>Thank you for registering with us.';
                if (!$mail->send()) {
                    $mail->send();
                }
                $_SESSION['register_success'] = '';
                header('Location: '.$_SERVER['PHP_SELF']);
            } else {
                echo '<p style="color: red;">Your registration was not successful. Please try again later.</p>';
            }
        }

    }
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <ul>
        <label for="first_name">First Name<span style="color:red;">*</span></label><li><input type="text" id="first_name" name="first_name" value= "<?php if (isset($_POST['first_name'])) { echo $_POST['first_name'];}?>" required/></li><br/>
        <label for="middle_name">Middle Name</label><li><input type="text" id="middle_name" name="middle_name" value= "<?php if (isset($_POST['middle_name'])) { echo $_POST['middle_name'];}?>" /></li><br/>
        <label for="last_name">Last Name<span style="color:red;">*</span></label><li><input type="text" id="last_name" name="last_name" value= "<?php if (isset($_POST['last_name'])) { echo $_POST['last_name'];} ?>" required/></li><br/>
        <label>Sex</label><span style="color:red;">*</span></label><br/>
        <input type="radio" id="male" name="sex" value= "male" required <?php echo (isset($_POST['sex']) && $_POST['sex'] === 'male') ? 'checked' : '' ?>/><label for="male">Male</label>
        <input type="radio" id="female" name="sex" value= "female" required <?php echo (isset($_POST['sex']) && $_POST['sex'] === 'female') ? 'checked' : '' ?>/><label for="female">Female</label><br/><br/>
        <label for="username">Username<span style="color:red;">*</span></label><li><input type="text" id="username" name="username" value="<?php if (isset($_POST['username'])) { echo $_POST['username'];} ?>" required/></li><br/>
        <label for="email">Email<span style="color:red;">*</span></label><li><input type="email" id="email" name="email" value="<?php if (isset($_POST['email'])) { echo $_POST['email'];} ?>" required/></li><br/>
        <label for="dob">Date of Birth<span style="color:red;">*</span></label><li><input type="date" id="dob" name="dob" value="<?php if (isset($_POST['dob'])) { echo $_POST['dob'];} ?>" required/></li><br/>
        <label for="password">Password<span style="color:red;">*</span></label><li><input type="password" id="password" name="password" required/></li><br/>
        <label for="password_again">Password again<span style="color:red;">*</span></label><li><input type="password" id="password_again" name="password_again" required/></li><br/>
        <button type="submit" name="register">Register</button>
    </ul>
</form>
<?php
}
?>
<?php include_once 'includes/overall/footer.php'; ?>
