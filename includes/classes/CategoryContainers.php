<?php
class CategoryContainers
{
    private $con;
    private $username;

    public function __construct($con, $username)
    {
        $this->con = $con;
        $this->username = $username;
    }
    public function showAllCategories() //すべてのカテゴリーを表示
    {
        $query = $this->con->prepare("SELECT * FROM categories");
        $query->execute();

        $html = "<div class='previewCategories'>";

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) { //連想配列を返す
            $html .= $this->getCategoryHtml($row, null, true, true); //すべてのカテゴリーを取得
        }


        return $html . "</div>";
    }
    public function showCategory($categoryId, $title = null) //you might also likeページ
    {
        $query = $this->con->prepare("SELECT * FROM categories WHERE id=:id");
        $query->bindValue(":id", $categoryId);
        $query->execute();

        $html = "<div class='previewCategories noScroll'>";

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) { //連想配列を返す
            $html .= $this->getCategoryHtml($row, $title, true, true); //すべてのカテゴリーを取得

        }
        return $html . "</div>";
    }

    private function getCategoryHtml($sqlData, $title, $tvShows, $movies) //$sqlDataにはcategoriesテーブルの連想配列がループで次々に代入される
    {
        $categoryId = $sqlData["id"];
        $title = $title == null ? $sqlData["name"] : $title;
        if ($tvShows && $movies) { //どちらの情報も取得したいとき
            $entities = EntityProvider::getEntities($this->con, $categoryId, 30);
        } else if ($tvShows) { //tvショーのみ

        } else { //movieのみ

        }

        if (sizeof($entities) == 0) { //Entityオブジェクトが取得できなかったときはskip
            return;
        }
        $entitiesHtml = "";
        $previewProvider = new PreviewProvider($this->con, $this->username);
        foreach ($entities as $entity) {
            $entitiesHtml .= $previewProvider->createEntityPreviewSquare($entity);
        }
        return "<div class='category'>
                    <a href='category.php?id=$categoryId'>
                        <h3>$title</h3>
                    </a>
                    <div class='entities'>
                        $entitiesHtml
                    </div>
                </div>";
    }
}
