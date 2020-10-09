<?php

require_once("includes/header.php");

if (!isset($_GET['id'])) {
    ErrorMessage::show("NO ID passed into page");
}
// echo "<pre>";
// var_dump($_GET["id"]);
// echo "</pre>";

$video = new Video($con, $_GET["id"]);
$video->incrementViews();
?>

<div class="watchContainer">


    <div class="videoControls watchNav">
        <button onclick="goBack()"><i class="fas fa-arrow-left"></i></button>
        <h1><?php echo $video->getTitle(); ?></h1>
    </div>
    <video controls autoplay>
        <!-- Chromeだとmuteにしていないとautoplayは機能しないらしい -->
        <source src="<?php echo $video->getFilePath(); ?>" type="video/mp4">
    </video>
</div>
<script>
    initVideo("<?php echo $video->getId(); ?>", "<?php echo $userLoggedIn ?>");
    //videoのidとuser情報をパラメータにセット
</script>