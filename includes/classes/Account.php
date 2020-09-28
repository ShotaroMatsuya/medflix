<?php
class Account
{

    private $con;
    private $errorArray = array();
    public function __construct($con)
    {
        $this->con = $con;
    }
    public function register($fn, $ln, $un, $em, $em2, $pw, $pw2)
    {
        $this->validateFirstName($fn);
        $this->validateLastName($ln);
        $this->validateUsername($un);
        $this->validateEmails($em, $em2);
        $this->validatePasswords($pw, $pw2);

        if (empty($this->errorArray)) {
            return $this->insertUserDetails($fn, $ln, $un, $em, $pw); //true or false
        }
        return false;
    }

    private function insertUserDetails($fn, $ln, $un, $em, $pw)
    {
        $pw = hash("sha512", $pw); //hash化


        $query = $this->con->prepare("INSERT INTO users (firstName,lastName,username,email, password) 
                                    VALUES (:fn,:ln,:un,:em,:pw)");
        $query->bindValue(":fn", $fn);
        $query->bindValue(":ln", $ln);
        $query->bindValue(":un", $un);
        $query->bindValue(":em", $em);
        $query->bindValue(":pw", $pw);


        return $query->execute(); //executeメソッドの返り値はINSERTが成功した場合にtrue、失敗した場合にはfalse
    }

    private function validateFirstName($fn)
    {
        if (strlen($fn) < 2 || strlen($fn) > 25) {
            array_push($this->errorArray, Constants::$firstNameCharacters);
        }
    }
    private function validateLastName($ln)
    {
        if (strlen($ln) < 2 || strlen($ln) > 25) {
            array_push($this->errorArray, Constants::$lastNameCharacters);
        }
    }
    private function validateUsername($un)  //errorArrayにpush
    {
        if (strlen($un) < 2 || strlen($un) > 25) {
            array_push($this->errorArray, Constants::$usernameCharacters);
            return;
        }

        $query = $this->con->prepare("SELECT * FROM users WHERE username = :un");
        $query->bindValue(":un", $un);

        $query->execute();
        if ($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$usernameTaken);
        }
    }

    private function validateEmails($em, $em2)
    {
        if ($em != $em2) {
            array_push($this->errorArray, Constants::$emailsDontMatch);
            return;
        }
        if (!filter_var($em, FILTER_VALIDATE_EMAIL)) { //emailの書式をcheckしてくれるphpのビルトイン関数
            array_push($this->errorArray, Constants::$emailInvalid);
            return;
        }
        $query = $this->con->prepare("SELECT * FROM users WHERE email=:em");
        $query->bindValue(":em", $em);

        $query->execute();
        if ($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$emailTaken);
        }
    }
    private function validatePasswords($pw, $pw2)
    {
        if ($pw != $pw2) {
            array_push($this->errorArray, Constants::$passwordsDontMatch);
            return;
        }
        if (strlen($pw) < 5 || strlen($pw) > 25) {
            array_push($this->errorArray, Constants::$passwordLength);
        }
    }



    public function getError($error) //errorArrayからerrorを取り出す
    {
        if (in_array($error, $this->errorArray)) {
            return "<span class='errorMessage'>$error</span>";
        }
    }
}
