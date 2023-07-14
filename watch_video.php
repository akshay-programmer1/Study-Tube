<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:home.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>watch video</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">



</head>
<body>

<?php include 'components/user_header.php'; ?>
<!-- watch video section starts  -->

<section class="watch-video">

<?php
   $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND status = ?");
   $select_content->execute([$get_id, 'active']);
   if($select_content->rowCount() > 0){
      while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
         $content_id = $fetch_content['id'];
      
         $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
         $select_tutor->execute([$fetch_content['tutor_id']]);
         $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

         // Check if the user has already viewed this video
         $select_us_con = $conn->prepare("SELECT * FROM `us_con` WHERE user_id1 = ? AND content_id = ?");
         $select_us_con->execute([$user_id, $get_id]);
         if($select_us_con->rowCount() == 0) {
            // Insert new record if the user hasn't viewed the video before
            $insert_us_con = $conn->prepare("INSERT INTO `us_con`(user_id1,content_id,videoSeen,SeekTime) VALUES(?,?,?,?)");
            $insert_us_con->execute([$user_id, $get_id, 0, 0]);
             // Initialize $video_seen to 0
             $video_seen = 0;

         } else {
            // Get the videoSeen attribute value
            $fetch_us_con = $select_us_con->fetch(PDO::FETCH_ASSOC);
            $video_seen = $fetch_us_con['videoSeen'];
         }
   $sql = "SELECT COUNT(*) as num_viewers FROM `us_con` WHERE `videoSeen` = 1 AND `videoSeen` IS NOT NULL AND `content_id` = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$get_id]);
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   $num_viewers = $row['num_viewers'];

         ?>
   <?php
   if($user_id != ""){
   ?>
   <div class="video-details">
   <video id="videoPlayer" src="uploaded_files/<?= $fetch_content['video']; ?>" class="video" poster="uploaded_files/<?= $fetch_content['thumb']; ?>" <?php if($video_seen == 0){echo 'autoplay';} elseif($video_seen == 1){echo 'controls autoplay';} else{echo 'autoplay';} ?> controlsList="nodownload"></video>
   <div class="info">
      <p><i class="fas fa-calendar"></i><span><?= $fetch_content['date']; ?></span></p>
      <p><img src="images/view.png"><span><?= $num_viewers?></span></p>
   </div>
   <div class="tutor">
      <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
      <div>
         <h3><?= $fetch_tutor['name']; ?></h3>
         <span><?= $fetch_tutor['profession']; ?></span>
      </div>
   </div>
   <div class="description"><p><?= $fetch_content['description']; ?></p></div>
</div>
<?php
   }else{
      echo '<p class="empty">Please Login In!</p>';
   }
   ?>
   <?php
         }
      }else{
         echo '<p class="empty">no videos added yet!</p>';
      }
   ?>
   <script>
   const videoPlayer = document.getElementById('videoPlayer');
   videoPlayer.addEventListener('ended', function() {
      fetch('mark_video_seen.php?content_id=<?= $content_id ?>')
         .then(response => response.text())
         .then(data => console.log(data))
         .catch(error => console.log(error));
         location.reload();
   });
</script>
</section>

<!-- watch video section ends -->



<!-- custom js file link  -->
<script src="js/script.js"></script>
</body>
</html>