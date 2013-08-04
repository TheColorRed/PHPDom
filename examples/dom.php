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

$url = urldecode($_GET["url"]);
if(!preg_match("/^http:\/\//", $url)){
    $url = "http://$url";
}

echo "<h2>$url</h2>";

$dom = new PHPDom();

$dom->loadURL($url);

if($dom->getHttpCode() == 200){
    $stylesheets = $dom->getStyleSheets();
    foreach($stylesheets["css"] as $url => $sheet){
        echo "<h2>{$url}</h2>";
        echo "<pre>";
        print_r($sheet->getMarkup());
        echo "</pre>";
    }
}
