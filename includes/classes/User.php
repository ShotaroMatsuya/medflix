<?php
class User //usersテーブルから情報を取得
{
    private $con, $sqlData;
    public function __construct($con, $username)
    {
        $this->con = $con;
        $query = $con->prepare("SELECT * FROM users WHERE username=:username");
        $query->bindValue(":username", $username);
        $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }
    public function getFirstName()
    {
        return $this->sqlData["firstName"];
    }
    public function getLastName()
    {
        return $this->sqlData["lastName"];
    }
    public function getEmail()
    {
        return $this->sqlData["email"];
    }
    public function getIsSubscribed()
    {
        return $this->sqlData["isSubscribed"];
        // return true;
    }
    public function getUsername()
    {
        return $this->sqlData["username"];
    }

    public function setIsSubscribed($value)
    {
        $query = $this->con->prepare("UPDATE users SET isSubscribed=:isSubscribed
                                        WHERE username=:un");
        $query->bindValue(":isSubscribed", $value); //$valueには0か1が入る
        $query->bindValue(":un", $this->getUsername());
        if ($query->execute()) { //updateに成功したら..
            $this->sqlData["isSubscribed"] = $value; //sqlDataプロパティも更新しておく
            return true;
        }
        return false;
    }
    public function getUserNotes()
    {
        $query = $this->con->prepare("SELECT * FROM notes where username=:un ORDER BY createdAt DESC LIMIT 5");
        $query->bindValue(":un", $this->getUsername());
        $query->execute();

        $noteLists = array();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $noteLists[] = $row;
        }
        return $noteLists;
    }
    public function getUserExams()
    {
        $query = $this->con->prepare("SELECT * FROM exams where username=:un ORDER BY createdAt DESC LIMIT 5");
        $query->bindValue(":un", $this->getUsername());
        $query->execute();
        $examLists = array();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $examLists[] = $row;
        }
        return $examLists;
    }
}
