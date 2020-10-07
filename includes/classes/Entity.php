<?php
class Entity //entityテーブルからrow情報の取得
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

    //sqlDataにはentitiesテーブルからfetchされた情報が連想配列で格納されている
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
    public function getCategoryId()
    {
        return $this->sqlData["categoryId"];
    }
    public function getSeasons()
    {
        $query = $this->con->prepare("SELECT * FROM videos 
        WHERE entityId=:id 
        AND isMovie=0 /*isMovie=0でドラマのみを取得*/
        ORDER BY season,episode ASC"); /* seasonが同じNo.だったときにはepisondeが若い順に取得するという意味 */
        $query->bindValue(":id", $this->getId());
        $query->execute();

        $seasons = array(); //seasonオブジェクトが入る配列
        $videos = array(); //videoオブジェクトが入る配列
        $currentSeason = null;
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            if ($currentSeason != null && $currentSeason != $row["season"]) { //seasonカラムの値が変わったとき
                $seasons[] = new Season($currentSeason, $videos); //1シーズンにつきseasonオブジェクトを生成
                $videos = array(); //init
            }
            $currentSeason = $row["season"];
            $videos[] = new Video($this->con, $row); //一つのvideoにつきvideoオブジェクトを生成
        }
        if (sizeof($videos) != 0) { //最後のシーズンは
            $seasons[] = new Season($currentSeason, $videos);
        }
        return $seasons;
    }
}
