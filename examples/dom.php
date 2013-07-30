<?php
require_once __DIR__."/../PHPDom.php";

$dom = new PHPDom();

$dom->loadURL("http://phpsnips.com");

if($dom->getHttpCode() == 200){
    
}