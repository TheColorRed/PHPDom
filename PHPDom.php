<?php

class PHPDom{

    protected $version     = "1.0.0-alpha";
    protected $url         = "";
    protected $html        = "";
    protected $httpCode    = "";
    protected $stylesheets = array();

    public function loadURL($url, $other_params = array()){
        $this->url = $url;
        try{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if(!empty($this->post)){
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post);
            }
            if(isset($_SERVER['HTTP_USER_AGENT']))
                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            else
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatable; phpLiveBot/$this->version; +https://github.com/TheColorRed/PHPDom)");
            if(is_array($other_params)){
                foreach($other_params as $key => $val){
                    curl_setopt($ch, $key, $val);
                }
            }
            $this->html     = curl_exec($ch);
            $info           = (object)curl_getinfo($ch);
            $this->httpCode = $info->http_code;
        }catch(Exception $e){
            try{
                if(($this->html = @file_get_contents($url)) === false){
                    $this->httpCode = 404;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
        $this->loadStylesheets();
    }

    public function getHttpCode(){
        return $this->httpCode;
    }

    protected function loadStylesheets(){
        $matches = array();
        preg_match_all("/<link.+href.+[\"'](.+\.css)[\"'].+>/isU", $this->html, $matches);
        foreach($matches[1] as $url){
            $this->stylesheets[] = $this->rel2abs($url, $this->url);
        }
    }

    protected function rel2abs($rel, $base){
        if(preg_match("/^\/\//", $rel))
            return "http:$rel";
        
        /* return if already absolute URL */
        if(parse_url($rel, PHP_URL_SCHEME) != '')
            return $rel;

        /* queries and anchors */
        if($rel[0] == '#' || $rel[0] == '?')
            return $base . $rel;

        /* parse base URL and convert to local variables:
          $scheme, $host, $path */
        $scheme = "";
        $host   = "";
        $path   = "";
        $pu     = parse_url($base);
        if(isset($pu["scheme"]))
            $scheme = $pu["scheme"];
        if(isset($pu["host"]))
            $host   = $pu["host"];
        if(isset($pu["path"]))
            $path   = $pu["path"];

        /* remove non-directory element from path */
        $path = preg_replace('#/[^/]*$#', '', $path);

        /* destroy path if relative url points to root */
        if($rel[0] == '/')
            $path = '';

        /* dirty absolute URL */
        $abs = "$host$path/$rel";

        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n)){
            
        }

        /* absolute URL is ready! */
        return $scheme . '://' . $abs;
    }

}

?>
