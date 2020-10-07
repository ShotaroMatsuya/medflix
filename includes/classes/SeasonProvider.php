<?php
class SeasonProvider
{
    private $con;
    private $username;

    public function __construct($con, $username)
    {
        $this->con = $con;
        $this->username = $username;
    }
    public function create($entity)
    {
        $seasons = $entity->getSeasons(); //すべてのseasonとtv-showが取得されている

        if (sizeof($seasons) == 0) {
            return;
        }
        $seasonsHtml = "";

        // $videos = $seasons->getVideos()[0];
        // var_dump($this->createVideoSquare($videos));

        foreach ($seasons as $season) { //$seasonオブジェクトはcurrentSeasonNumとvideoオブジェクトをプロパティとして持っている

            $seasonNumber =  $season->getSeasonNumber();
            $videosHtml = "";
            foreach ($season->getVideos() as $video) { //$videoオブジェクトが入ったarrayをループ
                $videosHtml .= $this->createVideoSquare($video);
            }
            $seasonsHtml .= "<div class='season'>
                                <h3>Season $seasonNumber</h3>   
                                <div class='videos'>
                                    $videosHtml
                                </div>      
                            </div>";
        }
        return $seasonsHtml;
    }

    private function createVideoSquare($video)
    {
        $id = $video->getId();
        $thumbnail = $video->getThumbnail();
        $name = $video->getTitle();
        $description = $video->getDescription();
        $episodeNumber = $video->getEpisodeNumber();
        return "<a href='watch.php?id=$id'>
                    <div class='episodeContainer'>
                        <div class='contents'>
                            <img src='$thumbnail'>
                            <div class='videoInfo'>
                                <h4>$name</h4>
                                <span>$description</span>
                            </div>
                        </div>
                    </div>
                </a>";
    }
}
