<?php


require_once("../includes/config.php");

if (isset($_POST["videoId"]) && isset($_POST["username"]) && isset($_POST["score"])) {
    $query = $con->prepare("SELECT * FROM exams WHERE username=:username AND videoId=:videoId");
    $query->bindValue(":username", $_POST["username"]);
    $query->bindValue(":videoId", $_POST["videoId"]);
    $query->execute();
    if ($query->rowCount() == 0) { //dbにデータがなかったとき
        $query = $con->prepare("INSERT INTO exams (username, videoId,score) VALUES(:username, :videoId,:score)");
        $query->bindValue(":username", $_POST["username"]);
        $query->bindValue(":videoId", $_POST["videoId"]);
        $query->bindValue(":score", $_POST["score"]);

        $query->execute();
    } else {
        $query = $con->prepare("UPDATE exams SET content=:content, createdAt=NOW() WHERE username=:username AND videoId=:videoId");
        $query->bindValue(":username", $_POST["username"]);
        $query->bindValue(":videoId", $_POST["videoId"]);
        $query->bindValue(":score", $_POST["score"]);
        $query->execute();
    }
} else {
    echo "No videoId , username or content passed into file";
}
