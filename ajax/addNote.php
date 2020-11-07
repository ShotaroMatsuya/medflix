<?php


require_once("../includes/config.php");

if (isset($_POST["videoId"]) && isset($_POST["username"]) && isset($_POST["content"])) {
    $query = $con->prepare("SELECT * FROM notes WHERE username=:username AND videoId=:videoId");
    $query->bindValue(":username", $_POST["username"]);
    $query->bindValue(":videoId", $_POST["videoId"]);
    $query->execute();
    if ($query->rowCount() == 0) { //dbにデータがなかったとき
        $query = $con->prepare("INSERT INTO notes (username, videoId,content) VALUES(:username, :videoId,:content)");
        $query->bindValue(":username", $_POST["username"]);
        $query->bindValue(":videoId", $_POST["videoId"]);
        $query->bindValue(":content", $_POST["content"]);

        $query->execute();
    } else {
        $query = $con->prepare("UPDATE notes SET content=:content, createdAt=NOW() WHERE username=:username AND videoId=:videoId");
        $query->bindValue(":username", $_POST["username"]);
        $query->bindValue(":videoId", $_POST["videoId"]);
        $query->bindValue(":content", $_POST["content"]);
        $query->execute();
    }
} else {
    echo "No videoId , username or content passed into file";
}
