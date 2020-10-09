<?php
class Video //videosテーブルからrow情報取得
{

    private $con;
    private $sqlData;
    private $entity;

    public function __construct($con, $input)
    {
        $this->con = $con;

        if (is_array($input)) { //$inputが連想配列のとき
            $this->sqlData = $input;
        } else { //$inputがidのみのとき
            $query = $this->con->prepare("SELECT * FROM videos WHERE id = :id");
            $query->bindValue(":id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC); //連想配列に変える
        }
        $this->entity = new Entity($con, $this->sqlData["entityId"]); //videosテーブル内のentityIdからEntityオブジェクトを生成(thumbnailを取得するためにのちのち必要)
    }
    public function getId()
    {
        return $this->sqlData["id"];
    }
    public function getTitle()
    {
        return $this->sqlData["title"];
    }
    public function getDescription()
    {
        return $this->sqlData["description"];
    }
    public function getFilePath()
    {
        return $this->sqlData["filePath"];
    }
    public function getThumbnail()
    {
        return $this->entity->getThumbnail(); //thumbnailはbelongしているentity_idより取得する必要がある
    }
    public function getEpisodeNumber()
    {
        return $this->sqlData["episode"];
    }

    public function incrementViews()
    {
        $query = $this->con->prepare("UPDATE videos SET views=views+1 WHERE id=:id");
        $query->bindValue(":id", $this->getId());

        $query->execute();
        // print_r($this->con->errorInfo());
    }
}
