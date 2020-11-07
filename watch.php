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
        <button class="btn btn-danger btn-sm" onclick="restartVideo();"><i class="fas fa-redo"></i>見直す</button>
        <div class="upNextContainer">
            <h2>お疲れさまでした</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#Modal">
                確認問題を解く
            </button>
            <h3><?php echo $upNextVideo->getTitle();  ?></h3>
            <h3><?php echo $upNextVideo->getSeasonAndEpisode();  ?></h3>
            <button class="playNext btn btn-success" onclick="watchVideo(<?php echo $upNextVideo->getId(); ?>)">
                <i class="fas fa-play"></i> 次の動画へ
            </button>
        </div>
    </div>
    <video controls autoplay onended="showUpNext();">
        <!-- Chromeだとmuteにしていないとautoplayは機能しないらしい -->
        <source src="<?php echo $video->getFilePath(); ?>" type="video/mp4">
    </video>
</div>



<!-- Quiz MODAL -->
<div class="modal fade text-dark" id="Modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title message">
                    </h5>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
            </div>
            <div class="modal-body">

                <div class="que font-weight-bold text-center">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn  btn-primary btn-block q-next"></button>
                    <a class="btn btn-danger btn-lg again d-none" href="#" data-dismiss="modal">もう一度視聴する</a>
                    <button class="btn btn-success btn-lg fin d-none" onclick="watchVideo(<?php echo $upNextVideo->getId(); ?>)">次の動画へ</button>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- NoteSection -->
<div class="container py-5">
    <div class="btn btn-primary btn-block p-4 note-btn" id="note"></div>
    <div class="note-section">

        <div class="form-group mb-5">
            <textarea class="form-control" name="content" id="content" cols="30" rows="10" placeholder="take notes here."></textarea>
        </div>
        <div class="d-flex justify-content-end">
            <button id="noteBtn" class="btn btn-success btn-lg mx-4 p-3">保存</button>
            <button id="deleteBtn" class="btn btn-danger btn-lg mx-2 p-3">削除</button>
        </div>

    </div>




</div>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

<script src="assets/js/main.js"></script>
<script>
    initVideo("<?php echo $video->getId(); ?>", "<?php echo $userLoggedIn ?>");
    //videoのidとuser情報をパラメータにセット
    initNote("<?php echo $video->getId(); ?>", "<?php echo $userLoggedIn ?>");

    initQuiz("<?php echo $video->getId(); ?>", "<?php echo $userLoggedIn ?>");
</script>