<?php
$hideNav = true; //navBarを非表示に
require_once("includes/header.php");

if (!isset($_GET['id'])) {
    ErrorMessage::show("NO ID passed into page");
}
// echo "<pre>";
// var_dump($_GET["id"]);
// echo "</pre>";

$user = new User($con, $userLoggedIn);
if (!$user->getIsSubscribed()) {
    ErrorMessage::show("your must be subscribed to see this.
                        <a href='profile.php'>Click here to subscribe</a>");
}

$video = new Video($con, $_GET["id"]);
$video->incrementViews();
$upNextVideo = VideoProvider::getUpNext($con, $video);
?>

<div class="watchContainer">


    <div class="videoControls watchNav">
        <button onclick="goBack()"><i class="fas fa-arrow-left"></i></button>
        <h1><?php echo $video->getTitle(); ?></h1>
    </div>
    <div class="videoControls upNext" style="display:none">
        <button onclick="restartVideo();"><i class="fas fa-redo"></i></button>
        <div class="upNextContainer">
            <h2>Up next:</h2>
            <h3><?php echo $upNextVideo->getTitle();  ?></h3>
            <h3><?php echo $upNextVideo->getSeasonAndEpisode();  ?></h3>
            <button class="playNext" onclick="watchVideo(<?php echo $upNextVideo->getId(); ?>)">
                <i class="fas fa-play"></i> Play
            </button>
        </div>
    </div>
    <video controls autoplay onended="showUpNext();">
        <!-- Chromeだとmuteにしていないとautoplayは機能しないらしい -->
        <source src="<?php echo $video->getFilePath(); ?>" type="video/mp4">
    </video>
</div>
<script>
    initVideo("<?php echo $video->getId(); ?>", "<?php echo $userLoggedIn ?>");
    //videoのidとuser情報をパラメータにセット
</script>