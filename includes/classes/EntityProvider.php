<?php
class EntityProvider
{

    public static function getEntities($con, $categoryId, $limit)
    {

        $sql = "SELECT * FROM entities ";
        if ($categoryId != null) { //カテゴリーが指定されていない場合(=トップ画面のとき)
            $sql .= "WHERE categoryId=:categoryId ";
        }
        $sql .= "ORDER BY RAND() LIMIT :limit";

        $query = $con->prepare($sql);

        if ($categoryId != null) { //カテゴリーが指定されているとき(=カテゴリーページにいるとき)
            $query->bindValue(":categoryId", $categoryId);
        }
        $query->bindValue(":limit", $limit, PDO::PARAM_INT); //デフォルトだとstr型なのでINT型に変換する
        $query->execute();

        $result = array();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) { //結果を連想配列にする
            $result[] = new Entity($con, $row);
        }
        return $result;
    }
}
