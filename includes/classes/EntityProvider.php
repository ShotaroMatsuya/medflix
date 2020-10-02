<?php
class EntityProvider
{

    public static function getEntities($con, $categoryId, $limit) //カテゴリーIDからEntityをすべて取得する
    {

        $sql = "SELECT * FROM entities ";
        if ($categoryId != null) { //カテゴリーが指定されている場合
            $sql .= "WHERE categoryId=:categoryId ";
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
}
