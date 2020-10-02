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
            $html .= $this->getCategoryHtml($row, null, true, true);
        }


        return $html . "</div>";
    }

    private function getCategoryHtml($sqlData, $title, $tvShows, $movies)
    {
        $categoryId = $sqlData["id"];
        $title = $title == null ? $sqlData["name"] : $title;
        if ($tvShows && $movies) {
            $entities = EntityProvider::getEntities($this->con, $category, 30);
        } else if ($tvShows) {
            //Get tv show entities
        } else {
            //Get movie entities
        }

        if (sizeof(($entities) == 0)) {
            return;
        }
        $entitiesHtml = "";
        $previewProvider = new PreviewProvider($this->con, $this->username);
        foreach ($entities as $entity) {
            $entitiesHtml .= $previewProvider->createEntityPreviewSquare($entity);
        }
        return $entitiesHtml . "<br>";



        return  $title . "<br>";
    }
}
