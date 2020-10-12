<?php
class PreviewProvider //preview画面とvideoリストのdom構成
{
    private $con;
    private $username;

    public function __construct($con, $username)
    {
        $this->con = $con;
        $this->username = $username;
    }
    public function createCategoryPreviewVideo($categoryId)
    {
        $entitiesArray = EntityProvider::getEntities($this->con, $categoryId, 1); //指定されたカテゴリからランダムで1つのentityを取得
        if (sizeof($entitiesArray) == 0) {
            ErrorMessage::show("No TV shows to display");
        }
        return $this->createPreviewVideo($entitiesArray[0]);
    }
    public function createTVShowPreviewVideo()
    {
        $entitiesArray = EntityProvider::getTVShowEntities($this->con, null, 1); //カテゴリ未指定でランダムで1つのentityを取得
        if (sizeof($entitiesArray) == 0) {
            ErrorMessage::show("No TV shows to display");
        }
        return $this->createPreviewVideo($entitiesArray[0]);
    }
    public function createMoviesPreviewVideo()
    {
        $entitiesArray = EntityProvider::getMoviesEntities($this->con, null, 1); //カテゴリ未指定でランダムで1つのentityを取得
        if (sizeof($entitiesArray) == 0) {
            ErrorMessage::show("No movies to display");
        }
        return $this->createPreviewVideo($entitiesArray[0]);
    }
    public function createPreviewVideo($entity) //引き数はEntityオブジェクト
    {
        if ($entity == null) {
            $entity = $this->getRandomEntity();
        }
        $id = $entity->getId();
        $name = $entity->getName();
        $preview = $entity->getPreview();
        $thumbnail = $entity->getThumbnail();
        //videoProgressテーブルからuserが最後に見ていたvideoインスタンスを取得する
        $videoId = VideoProvider::getEntityVideoForUser($this->con, $id, $this->username);
        $video = new Video($this->con, $videoId);

        $isProgress = $video->isInProgress($this->username);
        $playButtonText = $isProgress ? "Continue watching" : "Play";
        $seasonEpisode = $video->getSeasonAndEpisode();
        $subHeading = $video->isMovie() ? "" : "<h4>$seasonEpisode</h4>";



        return "<div class='previewContainer'>
                    <img src='$thumbnail' class='previewImage' hidden>
                    <video autoplay muted class='previewVideo' onended='previewEnded()'><!-- onended属性はvideoタグで使える、イベントハンドラー -->
                        <source src='$preview' type='video/mp4'>
                    </video>
                    <div class='previewOverlay'>
                        <div class='mainDetails'>
                            <h3>$name</h3>
                            $subHeading

                            <div class='buttons'>
                                <button onclick='watchVideo($videoId);'><i class='fas fa-play'></i> $playButtonText</button>
                                <button onclick='volumeToggle(this)'><i class='fas fa-volume-mute'></i></button>
                            </div>
                        </div>
                    </div>
                </div>";
    }
    public function createEntityPreviewSquare($entity) //Entityオブジェクトを引数に受け取りvideoリストを作成
    {
        $id = $entity->getId();
        $thumbnail = $entity->getThumbnail();
        $name = $entity->getName();

        return "<a href='entity.php?id=$id'>
                    <div class='previewContainer small'>
                        <img src='$thumbnail' title='$name'>
                    </div>
                </a>";
    }



    private function getRandomEntity()
    {
        // //entitiesテーブルからランダムのカラムを一つ抽出する
        // $query = $this->con->prepare("SELECT * FROM entities ORDER BY RAND() LIMIT 1");
        // $query->execute();
        // $row = $query->fetch(PDO::FETCH_ASSOC); //keyとvalueがセットの連想配列が返ってくる,1件だけのときはfetchメソッド
        // return new Entity($this->con, $row);

        $entity = EntityProvider::getEntities($this->con, null, 1); //カテゴリー未指定の場合。返り値はEntityオブジェクトのarray
        return $entity[0];
    }
}
