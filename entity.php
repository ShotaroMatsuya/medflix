<?php
require_once("includes/header.php");


if (!isset($_GET['id'])) {
    ErrorMessage::show("NO ID passed into page");
}
$entityId = $_GET['id'];
$entity = new Entity($con, $entityId);

$preview = new PreviewProvider($con, $userLoggedIn);
echo $preview->createPreviewVideo($entity); //引数はnullの場合はrandom

$seasonProvider = new SeasonProvider($con, $userLoggedIn);
echo $seasonProvider->create($entity);

$categoryContainers = new CategoryContainers($con, $userLoggedIn);
echo $categoryContainers->showCategory($entity->getCategoryId(), "You might also like");
