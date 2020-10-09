<?php
require_once("../includes/config.php");

if (isset($_POST["videoId"]) && $_POST["username"]) {
    $query = $con->prepare("SELECT * FROM videoProgress WHERE username=:username AND videoID=:videoId");
    $query->bindValue(":username", $_POST["username"]);
    $query->bindValue(":videoId", $_POST["videoId"]);
    $query->execute();
    if ($query->rowCount() == 0) { //dbにデータがなかったとき
        $query = $con->prepare("INSERT INTO videoProgress (username, videoId) VALUES(:username, :videoId)");
        $query->bindValue(":username", $_POST["username"]);
        $query->bindValue(":videoId", $_POST["videoId"]);
        $query->execute();
    }
} else {
    echo "No videoId or username passed into file";
}
