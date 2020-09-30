<?php
class Entity
{
    private $con;
    private $sqlData;

    public function __construct($con, $input)
    {
        $this->con = $con;

        if (is_array($input)) { //$inputが連想配列のとき
            $this->sqlData = $input;
        } else { //$inputがidのみのとき
            $query = $this->con->prepare("SELECT * FROM entities WHERE id = :id");
            $query->bindValue(":id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC); //連想配列に変える
        }
    }
    public function getId()
    {
        return $this->sqlData["id"];
    }
    public function getName()
    {
        return $this->sqlData["name"];
    }
    public function getThumbnail()
    {
        return $this->sqlData["thumbnail"];
    }
    public function getPreview()
    {
        return $this->sqlData["preview"];
    }
}
