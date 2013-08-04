<form action="" method="get">
    <p>
        <input type="text" name="url" placeholder="Webpage URL" value="<?php echo (isset($_GET["url"]) ? $_GET["url"] : ""); ?>" />
        <input type="submit" name="search" value="Parse Me!" />
    </p>
</form>

<?php
if(!isset($_GET["search"])){
    exit;
}
require_once __DIR__ . "/../PHPDom.php";

$url = $_GET["url"];
if(preg_match("/^http:\/\//", $url)){
    $url = "http://$url";
}
$dom = new PHPDom();

$dom->loadURL($url);

if($dom->getHttpCode() == 200){
    $stylesheets = $dom->getStyleSheets();
    foreach($stylesheets as $sheet){
        echo "<h2>{$sheet["url"]}</h2>";
        echo "<pre>";
        echo print_r($sheet["css"]);
        echo "</pre>";
    }
}
