<?php
require_once("includes/config.php");
require_once("includes/classes/FormSanitizer.php");
require_once("includes/classes/Constants.php");
require_once("includes/classes/Account.php");

$account = new Account($con);


if (isset($_POST["submitButton"])) {

    $username = FormSanitizer::sanitizeFormUsername($_POST["username"]);
    $password = FormSanitizer::sanitizeFormPassword($_POST["password"]);

    //DBからselect
    $success = $account->login($username, $password);

    //登録完了時の処理
    if ($success) {
        //Store session
        $_SESSION["userLoggedIn"] = $username;
        header("Location: index.php"); //redirect


    }
}

//passwordを間違えたときにもinputタグ内にusernameのvalueをセットしてあげる
function getInputValue($name)
{
    if (isset($_POST[$name])) {
        echo $_POST[$name];
    }
}



?>

<!DOCTYPE html>
<html>

<head>
    <title>Welcome to Medflix</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css" />
</head>

<body>
    <div class="signInContainer">
        <div class="column">
            <div class="header">
                <img src="assets/images/logo.png" title="Logo" alt="Site logo" />
                <h3>ログイン</h3>
                <span>to continue to Medflix</span>
            </div>
            <form method="POST">
                <?php echo $account->getError(Constants::$loginFailed); ?>

                <input type="text" name="username" placeholder="Username" value="<?php getInputValue("username");  ?>" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" name="submitButton" value="ログイン">
            </form>
            <a href="register.php" class="signInMessage">アカウントをお持ちでない方はコチラ</a>

        </div>
    </div>
</body>

</html>