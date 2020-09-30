<?php
class PreviewProvider
{
    private $con;
    private $username;

    public function __construct($con, $username)
    {
        $this->con = $con;
        $this->username = $username;
    }
    public function createPreviewVideo($entity)
    {
        if ($entity == null) {
            $entity = $this->getRandomEntity();
        }
        $id = $entity->getId();
        $name = $entity->getName();
        $preview = $entity->getPreview();
        $thumbnail = $entity->getThumbnail();
        return "<div class='previewContainer'>
                    <img src='$thumbnail' class='previewImage' hidden>
                    <video autoplay muted class='previewVideo'>
                        <source src='$preview' type='video/mp4'>
                    </video>
                    <div class='previewOverlay'>
                    hello
                    </div>

                </div>";
    }

    private function getRandomEntity()
    {
        //entitiesテーブルからランダムのカラムを一つ抽出する
        $query = $this->con->prepare("SELECT * FROM entities ORDER BY RAND() LIMIT 1");
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC); //keyとvalueがセットの連想配列が返ってくる,1件だけのときはfetchメソッド
        return new Entity($this->con, $row);
    }
}
