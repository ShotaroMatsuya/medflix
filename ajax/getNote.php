<?php
require_once("../includes/config.php");

if (isset($_POST["videoId"]) && isset($_POST["username"])) {
    $query = $con->prepare("SELECT content FROM notes WHERE username=:username AND videoId=:videoId");
    $query->bindValue(":username", $_POST["username"]);
    $query->bindValue(":videoId", $_POST["videoId"]);

    $query->execute();
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($query->fetch(PDO::FETCH_ASSOC)); //連想配列で返す
} else {
    echo "No videoId or username passed into file";
}
