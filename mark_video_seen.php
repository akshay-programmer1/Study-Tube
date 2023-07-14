<?php
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_GET['content_id'])){
   $content_id = $_GET['content_id'];
}else{
   $content_id = '';
}

if(!empty($user_id) && !empty($content_id)){
   $update_us_con = $conn->prepare("UPDATE `us_con` SET `videoSeen`= 1 WHERE user_id1 = ? AND content_id = ?");
   $update_us_con->execute([$user_id, $content_id]);
}

?>
