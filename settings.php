<?php
require_once 'core/init.php';
protect_page();
include_once 'includes/overall/header.php';
?>
<h1>Settings</h1>
<?php
if (isset($_SESSION['change_settings_success'])) {
    echo '<p style="color: green;">Your settings has been changed successfully.</p>';
    unset($_SESSION['change_settings_success']);
} else {
    if (isset($_POST['change_settings'])) {
        $first_name = trim($_POST['first_name']);
        $middle_name = trim($_POST['middle_name']);
        $last_name = trim($_POST['last_name']);
        $dob = $_POST['dob'];
        $receive_mail = $_POST['receive_mail'];

        if ($first_name === "" || $last_name === "" || $dob === "") {
            $errors[] = 'All fields with an asterisk must be filled';
        } else {
            $required_char = array('first_name', 'middle_name', 'last_name');
            foreach ($_POST as $key => $value) {
                if (preg_match("/^[a-zA-Z]*$/", $value) == false && in_array($key, $required_char) ) {
                    $errors[] = 'Your name contains invalid characters';
                }
            }

            if (preg_match("/\\s/", $first_name) == true || preg_match("/\\s/", $middle_name) == true || preg_match("/\\s/", $last_name) == true) {
                $errors[] = 'Your first name, middle name, and last name should not contain spaces';
            }

            if ($receive_mail === 'on') {
                $receive_mail = 1;
            } else {
                $receive_mail = 0;
            }

            if (empty($errors) === false) {
                output_errors($errors);
            } else {
                $first_name_db     = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['first_name']))));
                $middle_name_db    = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['middle_name']))));
                $last_name_db     = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['last_name']))));
                $dob_db          = mysqli_real_escape_string($conn, $_POST['dob']);
                $receive_mail_db = mysqli_real_escape_string($conn, $_POST['receive_mail']);
                $user_id          = $user_data['user_id'];
                
                $sql = "UPDATE `users` SET `first_name` = '$first_name_db', `middle_name` = '$middle_name_db', `last_name` = '$last_name_db', `date_of_birth` = '$dob_db', `receive_mail` = '$receive_mail' WHERE `user_id` = '$user_id';";
                if (mysqli_query($conn, $sql) === true) {
                    $_SESSION['change_settings_success'] = '';
                    header('Location: '.$_SERVER['PHP_SELF']);
                } else {
                    echo '<p style="color: red;">Your settings change was not successful. Please try again later.</p>';
                }

            }
        }
    }
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <ul>
        <label for="first_name">First Name<span style="color:red;">*</span></label><li><input type="text" id="first_name" name="first_name" value= "<?php echo $user_data['first_name'];?>" required/></li><br/>
        <label for="middle_name">Middle Name</label><li><input type="text" id="middle_name" name="middle_name" value= "<?php if (isset($user_data['middle_name'])) { echo $user_data['middle_name'];}?>" /></li><br/>
        <label for="last_name">Last Name<span style="color:red;">*</span></label><li><input type="text" id="last_name" name="last_name" value= "<?php echo $user_data['last_name']; ?>" required/></li><br/>
        <label for="dob">Date of Birth<span style="color:red;">*</span></label><li><input type="date" id="dob" name="dob" value="<?php echo $user_data['date_of_birth']; ?>" required/></li><br/>
    </ul>
    <input type="checkbox" name="receive_mail" id="receive_mail" <?php if ($user_data['receive_mail'] == 1) { echo 'checked'; }?>><label for="receive_mail"> Would you like to receive updates from us?</label><br/><br/>
    <button type="submit" name="change_settings">Change</button>
    
    
</form>
<?php
}
include_once 'includes/overall/footer.php';
?>