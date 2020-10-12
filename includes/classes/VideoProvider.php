<?php
class VideoProvider
{
    public static function getUpNext($con, $currentVideo) //動画視聴後に呼び出されるstaticメソッド
    //変数currentVideoにはVideoインスタンスが代入されている。
    {
        //videosテーブルから(entity=映画タイトルが一致しかつ、現在のvideoと異なるもの)かつ(シーズンが一致してepisodeが現在よりも先のもの)そうでなければ(シーズンが上のもの)を取得
        $query = $con->prepare("SELECT * FROM videos 
                                    WHERE entityId=:entityId AND id != :videoId
                                    AND (
                                        (season = :season AND episode > :episode) 
                                        OR season > :season
                                    ) 
                                    ORDER BY season,episode ASC LIMIT 1");
        $query->bindValue(":entityId", $currentVideo->getEntityId());
        $query->bindValue(":season", $currentVideo->getSeasonNumber());
        $query->bindValue(":episode", $currentVideo->getEpisodeNumber());
        $query->bindValue(":videoId", $currentVideo->getId());

        $query->execute();
        if ($query->rowcount() == 0) { //もし一件も取得できなければ、一番最初のストーリの再生数が多いものをrecommendする
            $query = $con->prepare("SELECT * FROM videos 
                                    WHERE season <= 1 AND episode <= 1
                                    AND id != :videoId 
                                    ORDER BY views DESC LIMIT 1");
            $query->bindValue(":videoID", $currentVideo->getId());
            $query->execute();
        }
        $row = $query->fetch(PDO::FETCH_ASSOC);
        return new Video($con, $row); //新たなvideoインスタンスを返す
    }
    public static function getEntityVideoForUser($con, $entityId, $username)
    { //前回みた動画のentityを取得する
        $query = $con->prepare("SELECT videoId FROM videoProgress 
                                Inner JOIN videos
                                ON videoProgress.videoId = videos.id
                                WHERE videos.entityId = :entityId
                                AND videoProgress.username = :username
                                ORDER BY videoProgress.dateModified DESC
                                LIMIT 1");
        $query->bindValue(":entityId", $entityId);
        $query->bindValue(":username", $username);
        $query->execute();
        if ($query->rowCount() == 0) { //videoProgressテーブルにデータが無かった場合、一番最初のepisodeを取得
            $query = $con->prepare("SELECT id FROM videos 
                                    WHERE entityId = :entityId 
                                    ORDER BY season , episode 
                                    ASC LIMIT 1");
            $query->bindValue(":entityId", $entityId);
            $query->execute();
        }
        return $query->fetchColumn(); //videoのidのみを一つだけ取得するのでfetchColumnでよし
    }
}
