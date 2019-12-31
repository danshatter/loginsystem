<?php
require_once 'core/init.php';
include_once 'includes/overall/header.php';
?>
<h1>Profile</h1>
<?php
    if (isset($_GET['username']) === true) {
        $username = $_GET['username'];
        $sql = "SELECT `first_name`, `middle_name`, `last_name`, `sex`, `email`, `username`, `date_of_birth`, `date_registered`, `profile_picture_status`, `profile_picture_location` FROM `users` WHERE `username` = '$username';";
        if (mysqli_query($conn, $sql) == true) {
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if ($resultCheck !== 1) {
                echo '<h1>This User does not exist.</h1>';
            } else {
                while ($data = mysqli_fetch_assoc($result)) {
                    if ($data['profile_picture_status'] == 0) {
                        if ($data['sex'] === 'Male') {
                            $profile_image = 'profileimage/boy.png';
                        } elseif ($data['sex'] === 'Female') {
                            $profile_image = 'profileimage/girl.png';
                        }
                    } elseif ($data['profile_picture_status'] == 1) {
                        $profile_image = $data['profile_picture_location'];
                    }
                    echo '<img src="'.SITE_ROOT.'/'.$profile_image.'" style="width: 14%; height: 120px;"/><br/>';
                    echo '<h1>Name: '.$data['first_name'].' '.$data['middle_name'].' '.$data['last_name'].'</h1>';
                    echo '<h1>Sex: '.$data['sex'].'</h1>';
                    echo '<h1>Email: '.$data['email'].'</h1>';
                    echo '<h1>Username: '.$data['username'].'</h1>';
                    echo '<h1>Date of Birth: '.$data['date_of_birth'].'</h1>';
                    echo '<h1>Date Registered: '.$data['date_registered'].'</h1>';
                }
            }
        } 
    } else {
        header('Location: '.SITE_ROOT.'/index.php');
    }

include_once 'includes/overall/footer.php';
?>