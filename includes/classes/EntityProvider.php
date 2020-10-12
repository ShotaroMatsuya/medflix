<?php
class EntityProvider
{

    public static function getEntities($con, $categoryId, $limit) //カテゴリーIDからEntityをすべて取得する
    {

        $sql = "SELECT * FROM entities ";
        if ($categoryId != null) { //カテゴリーが指定されている場合
            $sql .= "WHERE categoryId=:categoryId "; //appendするので最後はスペースを空けておく
        }
        $sql .= "ORDER BY RAND() LIMIT :limit"; //カテゴリーが指定されていない場合はRandomに取得

        $query = $con->prepare($sql);

        if ($categoryId != null) { //カテゴリーが指定されてるときbindValueを実行
            $query->bindValue(":categoryId", $categoryId);
        }
        $query->bindValue(":limit", $limit, PDO::PARAM_INT); //デフォルトだとstr型なのでINT型に変換する
        $query->execute();

        $result = array();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) { //結果を連想配列にする
            $result[] = new Entity($con, $row);
        }
        return $result; //Entityオブジェクトが入ったarray
    }
    public static function getTVShowEntities($con, $categoryId, $limit) //カテゴリーIDからEntityをすべて取得する(TVShowのみ)
    {

        $sql = "SELECT DISTINCT(entities.id) FROM entities/* Distinctで重複した値の取得を防ぐ */
                INNER JOIN videos ON entities.id = videos.entityId
                WHERE videos.isMovie = 0 "; //appendしていくのでスペースを空ける
        if ($categoryId != null) { //カテゴリーが指定されている場合
            $sql .= "AND categoryId=:categoryId ";
        }
        $sql .= "ORDER BY RAND() LIMIT :limit"; //カテゴリーが指定されていない場合はRandomに取得

        $query = $con->prepare($sql);

        if ($categoryId != null) { //カテゴリーが指定されてるときbindValueを実行
            $query->bindValue(":categoryId", $categoryId);
        }
        $query->bindValue(":limit", $limit, PDO::PARAM_INT); //デフォルトだとstr型なのでINT型に変換する
        $query->execute();

        $result = array();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) { //結果を連想配列にする
            $result[] = new Entity($con, $row["id"]); //entityIdを1行ずつ取得
        }
        return $result; //Entityオブジェクトが入ったarray
    }
    public static function getMoviesEntities($con, $categoryId, $limit) //カテゴリーIDからEntityをすべて取得する(Moviesのみ)
    {

        $sql = "SELECT DISTINCT(entities.id) FROM entities/* Distinctで重複した値の取得を防ぐ */
                INNER JOIN videos ON entities.id = videos.entityId
                WHERE videos.isMovie = 1 "; //appendしていくのでスペースを空ける
        if ($categoryId != null) { //カテゴリーが指定されている場合
            $sql .= "AND categoryId=:categoryId ";
        }
        $sql .= "ORDER BY RAND() LIMIT :limit"; //カテゴリーが指定されていない場合はRandomに取得

        $query = $con->prepare($sql);

        if ($categoryId != null) { //カテゴリーが指定されてるときbindValueを実行
            $query->bindValue(":categoryId", $categoryId);
        }
        $query->bindValue(":limit", $limit, PDO::PARAM_INT); //デフォルトだとstr型なのでINT型に変換する
        $query->execute();

        $result = array();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) { //結果を連想配列にする
            $result[] = new Entity($con, $row["id"]); //entityIdを1行ずつ取得
        }
        return $result; //Entityオブジェクトが入ったarray
    }
    public static function getSearchEntities($con, $term) //検索termからEntityをすべて取得する
    {

        $sql = "SELECT * FROM entities WHERE name LIKE CONCAT('%',:term,'%') LIMIT 30";

        $query = $con->prepare($sql);


        $query->bindValue(":term", $term);
        $query->execute();

        $result = array();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) { //結果を連想配列にする
            $result[] = new Entity($con, $row);
        }
        return $result; //Entityオブジェクトが入ったarray
    }
}
