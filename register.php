<?php

require_once("includes/config.php");
require_once("includes/classes/FormSanitizer.php");
require_once("includes/classes/Constants.php");
require_once("includes/classes/Account.php");

$account = new Account($con);

if (isset($_POST["submitButton"])) {
    $firstName = FormSanitizer::sanitizeFormString($_POST["firstName"]);
    $lastName = FormSanitizer::sanitizeFormString($_POST["lastName"]);
    $username = FormSanitizer::sanitizeFormUsername($_POST["username"]);
    $email = FormSanitizer::sanitizeFormEmail($_POST["email"]);
    $email2 = FormSanitizer::sanitizeFormEmail($_POST["email2"]);
    $password = FormSanitizer::sanitizeFormPassword($_POST["password"]);
    $password2 = FormSanitizer::sanitizeFormPassword($_POST["password2"]);
    $isSubscribed  = $_POST["isSubscribed"] == '' ? '0' : $_POST["isSubscribed"];

    //DBにinsert
    $success = $account->register($firstName, $lastName, $username, $email, $email2, $password, $password2, $isSubscribed); //true or false

    //登録完了時の処理
    if ($success) {
        //Store session
        $_SESSION["userLoggedIn"] = $username;

        header("Location: index.php"); //redirect


    }
}
function getInputValue($name)
{
    if (isset($_POST[$name])) {
        echo $_POST[$name];
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <title>Welcome to Medflix</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css" />
</head>

<body>
    <div class="signInContainer">
        <div class="column">
            <div class="header">
                <img src="assets/images/logo.png" title="Logo" alt="Site logo" />
                <h3>新規登録</h3>
                <span>to continue to Medflix</span>
            </div>
            <form method="POST">
                <?php echo $account->getError(Constants::$firstNameCharacters); ?>
                <input type="text" name="firstName" placeholder="First name" value="<?php getInputValue("firstName");  ?>" required>
                <?php echo $account->getError(Constants::$lastNameCharacters); ?>

                <input type="text" name="lastName" placeholder="Last name" value="<?php getInputValue("lastName");  ?>" required>
                <?php echo $account->getError(Constants::$usernameCharacters); ?>
                <?php echo $account->getError(Constants::$usernameTaken); ?>

                <input type="text" name="username" placeholder="Username" value="<?php getInputValue("username");  ?>" required>

                <?php echo $account->getError(Constants::$emailsDontMatch); ?>
                <?php echo $account->getError(Constants::$emailInvalid); ?>
                <?php echo $account->getError(Constants::$emailTaken); ?>


                <input type="email" name="email" placeholder="Email" value="<?php getInputValue("email");  ?>" required>
                <input type="email" name="email2" placeholder="もう一度入力してください" value="<?php getInputValue("email2");  ?>" required>

                <?php echo $account->getError(Constants::$passwordsDontMatch); ?>
                <?php echo $account->getError(Constants::$passwordLength); ?>

                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="password2" placeholder="もう一度入力してください" required>
                <?php echo $account->getError(Constants::$emailInvalid); ?>
                <div>
                    <input type="radio" id="subscribe" name="isSubscribed" value="1"><label for="subscribe">定期購読者として入会する(料金は一切請求されません!)</label>

                </div>
                <input type="submit" name="submitButton" value="登録する">
            </form>
            <a href="login.php" class="signInMessage">すでにアカウントをお持ちの方はコチラ</a>

        </div>
    </div>
</body>

</html>