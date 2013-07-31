<?php

require_once __DIR__ . "/classes/URL.php";

class PHPDom{

    public static $version     = "1.0.0-alpha";
    protected $url         = "";
    protected $html        = "";
    protected $httpCode    = "";
    protected $stylesheets = array("links"  => array(), "markup" => array());

    public function loadURL($url, $other_params = array()){
        $this->url  = $url;
        $u          = new URL($url);
        $this->html = $u->go($other_params);
        $this->loadStylesheets();
    }

    public function getHttpCode(){
        return $this->httpCode;
    }

    protected function loadStylesheets(){
        $matches = array();
        preg_match_all("/<link.+href.+[\"'](.+\.css)[\"'].+>/isU", $this->html, $matches);
        foreach($matches[1] as $url){
            $this->stylesheets["links"][] = URL::rel2abs($url, $this->url);
        }
        $url = new URL($this->stylesheets["links"]);
        $this->stylesheets["markup"] = $url->go();
        
    }

}