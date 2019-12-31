<?php
require_once 'core/init.php';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    if (empty($search) === false) {
        $sql = "SELECT `first_name`, `middle_name`, `last_name`, `username`, `profile_picture_location`, `sex` FROM `users` WHERE `first_name` LIKE CONCAT('%', '$search', '%') OR `middle_name` LIKE CONCAT('%', '$search', '%') OR `last_name` LIKE CONCAT('%', '$search', '%') OR `username` LIKE CONCAT('%', '$search', '%');";
        if (mysqli_query($conn, $sql) == true) {
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if ($resultCheck !== 0) {
                while ($data = mysqli_fetch_assoc($result)) {
                    if (empty($data['profile_picture_location']) === true) {
                        if ($data['sex'] === 'Male') {
                            $data['profile_picture_location'] = 'profileimage/boy.png';
                        } elseif ($data['sex'] === 'Female') {
                            $data['profile_picture_location'] = 'profileimage/girl.png';
                        }
                    }
                    $ajax = '<a href="'.SITE_ROOT.'/profile.php?username='.$data['username'].'" style="text-decoration: none;"><li style="border-bottom: 1px solid black; border-right: 1px solid black; font-family: gabriola; font-size: 20px; padding: 0px;"><img src="'.SITE_ROOT.'/'.$data['profile_picture_location'].'" style="width: 35px; height: 35px; vertical-align: middle; text-decoration: none; margin-right: 8px;"/>'.$data['first_name'].' '.$data['last_name'].'</li></a>';
                    echo '<ul style="background-color: #c3c3c3;">'.$ajax.'</ul>';
                }
            }
        }
    }
}