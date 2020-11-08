<?php
require_once("../includes/config.php");

if (isset($_POST["videoId"]) && isset($_POST["username"])) {
    $query = $con->prepare("UPDATE exams SET isPassed=1
                            WHERE username=:username AND videoId=:videoId");
    $query->bindValue(":username", $_POST["username"]);
    $query->bindValue(":videoId", $_POST["videoId"]);
    $query->execute();
    // if ($query->execute()) {

    //     echo "setPassed successfully!";
    // } else {
    //     echo "fail to update the record!";
    // }
} else {
    echo "No videoId or username passed into file";
}
