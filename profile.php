<?php
require_once("includes/header.php");
require_once("includes/paypalConfig.php");
require_once("includes/classes/Account.php");
require_once("includes/classes/FormSanitizer.php");
require_once("includes/classes/Constants.php");
require_once("includes/classes/BillingDetails.php");

$user = new User($con, $userLoggedIn);
$detailsMessage = "";
$passwordMessage = "";
$subscriptionMessage = "";

if (isset($_POST["saveDetailsButton"])) {
    $account = new Account($con);

    $firstName = FormSanitizer::sanitizeFormString($_POST["firstName"]);
    $lastName = FormSanitizer::sanitizeFormString($_POST["lastName"]);
    $email = FormSanitizer::sanitizeFormEmail($_POST["email"]);

    if ($account->updateDetails($firstName, $lastName, $email, $userLoggedIn)) {
        // success
        $detailsMessage = "<div class='alertSuccess'>
                            Details update successfully!
                        </div>";
    } else {
        //Failure
        $errorMessage = $account->getFirstError();
        $detailsMessage = "<div class='alertError'>
                            $errorMessage
                        </div>";
    }
}

if (isset($_POST["savePasswordButton"])) {
    $account = new Account($con);

    $oldPassword = FormSanitizer::sanitizeFormPassword($_POST["oldPassword"]);
    $newPassword = FormSanitizer::sanitizeFormPassword($_POST["newPassword"]);
    $newPassword2 = FormSanitizer::sanitizeFormPassword($_POST["newPassword2"]);


    if ($account->updatePassword($oldPassword, $newPassword, $newPassword2, $userLoggedIn)) {
        // success
        $passwordMessage = "<div class='alertSuccess'>
                            Password update successfully!
                        </div>";
    } else {
        //Failure
        $errorMessage = $account->getFirstError();
        $passwordMessage = "<div class='alertError'>
                            $errorMessage
                        </div>";
    }
}

if (isset($_GET['success']) && $_GET['success'] == 'true') {
    $token = $_GET['token'];
    $agreement = new \PayPal\Api\Agreement();

    $subscriptionMessage = "<div class='alertError'>Something went wrong!</div>";

    try {
        // Execute agreement
        $agreement->execute($token, $apiContext);

        //billingsDetailsテーブルにrecordを追加
        $result = BillingDetails::insertDetails($con, $agreement, $token, $userLoggedIn);
        // usersテーブルの情報の更新
        $result = $result && $user->setIsSubscribed(1);

        if ($result) {
            $subscriptionMessage = "<div class='alertSuccess'>
                                        You're all signed up!
                                    </div>";
        }
    } catch (PayPal\Exception\PayPalConnectionException $ex) {
        echo $ex->getCode();
        echo $ex->getData();
        die($ex);
    } catch (Exception $ex) {
        die($ex);
    }
} else if (isset($_GET['success']) && $_GET['success'] == 'false') {
    $subscriptionMessage = "<div class='alertError'>
                            User cancelled or something went wrong!
                        </div>";
}
?>

<div class="settingsContainer column">
    <div class="historySection">
        <h2>User History</h2>
        <h3>ノート作成履歴</h3>
        <?php

        if ($user->getUserNotes() != null) {

            foreach ($user->getUserNotes() as $note) {
                $date = $note['createdAt'];
                $videoId = $note['videoId'];
                $video = new Video($con, $videoId);
                $videoTitle = $video->getTitle();
                echo "<ul class='list-group my-3'>
                        
                        <a href='watch.php?id=$videoId' class='list-group-item list-group-item-action p-3 d-flex justify-content-around'>
                        <span class='lead'>編集日: $date </span> <span class=' text-primary h4'>$videoTitle</span>
                        </a>
                       
                        </ul>";
            }
        } else {
            echo "<h4 class='text-light my-5'>ノートは作成されていません</h4>";
        }
        ?>
        <h3>確認テスト成績</h3>
        <?php
        if ($user->getUserExams() != null) {
            foreach ($user->getUserExams() as $exam) {
                $date = $exam['createdAt'];
                $videoId = $exam['videoId'];
                $score = $exam['score'];
                $isPassed = $exam['isPassed'] == 1 ? '<i class="far fa-check-circle text-success h3">合格</i>' : '<i class="far fa-times-circle text-danger h3">不合格</i>';
                $video = new Video($con, $videoId);
                $videoTitle = $video->getTitle();
                echo "<ul class='list-group my-3'>
                        
                        <a href='watch.php?id=$videoId' class='list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center'>$isPassed
                        <span class='lead'>実施日: $date </span> <span class=' text-primary h4'>$videoTitle</span><span class='text-danger'>$score 点/10問</span>
                        </a>
                       
                        </ul>";
            }
        } else {
            echo "<h4 class='text-light my-5'>確認テストの履歴がありません。</h4>";
        }

        ?>

    </div>


    <div class="formSection">
        <form method="POST">

            <h2>User details</h2>


            <?php


            $firstName = isset($_POST["firstName"]) ? $_POST["firstName"] : $user->getFirstName();
            $lastName = isset($_POST["lastName"]) ? $_POST["lastName"] : $user->getLastName();
            $email = isset($_POST["email"]) ? $_POST["email"] : $user->getEmail();

            ?>
            <input type="text" name="firstName" placeholder="First name" value="<?php echo $firstName ?>">
            <input type="text" name="lastName" placeholder="Last name" value="<?php echo $lastName ?>">
            <input type="email" name="email" placeholder="Email" value="<?php echo $email ?>">
            <div class="message">
                <?php echo $detailsMessage; ?>
            </div>
            <input type="submit" value="Save" name="saveDetailsButton">

        </form>

    </div>
    <div class="formSection">
        <form method="POST">
            <h2>Update password</h2>
            <input type="password" name="oldPassword" placeholder="Old password">
            <input type="password" name="newPassword" placeholder="New password">
            <input type="password" name="newPassword2" placeholder="Confirm password">
            <div class="message">
                <?php echo $passwordMessage; ?>
            </div>

            <input type="submit" value="Save" name="savePasswordButton">

        </form>

    </div>
    <div class="formSection">
        <h2>Subscription</h2>
        <div class="message">
            <?php echo $subscriptionMessage; ?>
        </div>
        <?php
        if ($user->getIsSubscribed()) {
            echo "<h3>You are subscribed! Go to PayPal to cancel.</h3>";
        } else {
            echo "<a href='billing.php'>Subscribe to Reeceflix</a>";
        }

        ?>
    </div>
</div>