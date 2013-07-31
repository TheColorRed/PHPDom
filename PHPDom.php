<?php

require_once __DIR__ . "/classes/URL.php";
require_once __DIR__ . "/classes/Stylesheet.php";

class PHPDom{

    public static $version     = "1.0.0-alpha";
    protected $url         = "";
    protected $html        = "";
    protected $httpCode    = "";
    protected $stylesheets = array("links"  => array(), "markup" => array());

    /**
     * Loads a web pages html and css
     * 
     * @param String $url The website to get the data from
     * @param Array $other_params An array of extra curl parameters
     */
    public function loadURL($url, $other_params = array()){
        $this->url  = $url;
        $u          = new URL($url);
        $this->html = $u->go($other_params)["data"];
        $this->loadStylesheets();
    }

    public function getHttpCode(){
        return $this->httpCode;
    }

    protected function loadStylesheets(){
        $matches = array();
        preg_match_all("/<link.+href.+[\"'](.+\.css)[\"'].+>/isU", $this->html, $matches);
        foreach($matches[1] as $url){
            $this->stylesheets["links"][$url] = URL::rel2abs($url, $this->url);
        }
        $url         = new URL($this->stylesheets["links"]);
        $stylesheets = $url->go();
        foreach($stylesheets as $markup){
            $this->stylesheets["markup"][$markup["url"]] = (new Stylesheet())->loadStylesheet($markup["data"]);
        }
    }

}