<?php

class URL{

    protected $url = "";
    protected $httpCode;

    public function __construct($url){
        $this->url = $url;
    }

    public function go($extra_parameters = array()){
        return $this->getURL($extra_parameters);
    }

    public function getHttpCode(){
        return $this->httpCode;
    }

    protected function getURL($other_params = array()){
        $html     = "";
        $url      = $this->url;
        $finalURL = "";
        if(is_array($url)){
            return $this->getURLs($other_params);
        }
        try{
            $ch             = $this->curlInit($url, $other_params);
            $html           = curl_exec($ch);
            $info           = (object)curl_getinfo($ch);
            $finalURL       = $info->url;
            $this->httpCode = $info->http_code;
        }catch(Exception $e){
            try{
                if(($html = @file_get_contents($url)) === false){
                    $this->httpCode = 404;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
        return array("url"  => $finalURL, "data" => $html);
    }

    protected function getURLs($other_params = array()){
        $url      = $this->url;
        $url_list = array();
        if(!is_array($url)){
            return false;
        }
        $mh = curl_multi_init();
        foreach($url as $u){
            $url_list[] = $handle     = $this->curlInit($u, $other_params);
            curl_multi_add_handle($mh, $handle);
        }
        $active = null;
        do{
            $mrc = curl_multi_exec($mh, $active);
        }while($mrc === CURLM_CALL_MULTI_PERFORM);

        while($active && $mrc === CURLM_OK){
            $mrc = curl_multi_exec($mh, $active);
            if(curl_multi_select($mh) !== -1){
                do{
                    $mrc = curl_multi_exec($mh, $active);
                }while($mrc === CURLM_CALL_MULTI_PERFORM);
            }
        }
        $data = array();
        foreach($url_list as $handle){
            $info   = (object)curl_getinfo($handle);
            $data[] = array(
                "url"  => $info->url,
                "data" => curl_multi_getcontent($handle)
            );
            curl_multi_remove_handle($mh, $handle);
        }
        curl_multi_close($mh);
        return $data;
    }

    protected function curlInit($url, $other_params){
        $ch1 = curl_init();
        curl_setopt($ch1, CURLOPT_URL, $url);
        curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        if(isset($_SERVER['HTTP_USER_AGENT']))
            curl_setopt($ch1, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        else
            curl_setopt($ch1, CURLOPT_USERAGENT, "Mozilla/5.0 (compatable; phpLiveBot/" . PHPDom::$version . "; +https://github.com/TheColorRed/PHPDom)");
        if(is_array($other_params)){
            foreach($other_params as $key => $val){
                curl_setopt($ch1, $key, $val);
            }
        }
        return $ch1;
    }

    public static function rel2abs($rel, $base){
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