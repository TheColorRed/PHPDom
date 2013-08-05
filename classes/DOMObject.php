<?php

class DOMObject{

    protected $html     = "";
    protected $selfCose = array("area", "base", "br", "col", "command", "embed", "hr", "img", "input", "keygen", "link", "meta", "param", "source", "track", "wbr");

    public function __construct($html){
        $this->html = $html;
        $this->parse();
    }

    public function parse(){
        $length = strlen($this->html);
        $string = "";
        $cTag   = "";
        $cAttr  = "";
        $cVal   = "";

        $isOpenTag  = false;
        $isCloseTag = false;
        $isTag      = false;
        $isJS       = false;
        $isCSS      = false;
        $isComment  = false;
        $added      = false;

        $tagSpaces = 0;
        $depth     = 0;
        $tokens    = array();
        $path      = array();


        for($i = 0; $i < $length; $i++){
            $char = $this->html[$i];
            switch($char){
                case "<":
                    if(strtolower(substr($this->html, $i + 1, 1)) == "/"){
                        $isCloseTag = true;
                        $isOpenTag  = false;
                        $i++;
                    }elseif(strtolower(substr($this->html, $i + 1, 3)) == "!--"){
                        $isComment = true;
                        $isTag     = false;
                    }else{
                        $isCloseTag = false;
                        $isOpenTag  = true;
                    }
                    $string = "";
                    break;
                case ">":
                    if($isOpenTag){
                        echo implode(" -> ", $path) . "\n";
                    }
                    if(strtolower(substr($this->html, $i - 2, 2)) == "--" && $isComment){
                        $isComment = false;
                        $string    = "";
                    }
                    if($isCloseTag){
                        $cTag = strtolower($string);
                        if($cTag == "script"){
                            $isJS = false;
                        }elseif($cTag == "style"){
                            $isCSS = false;
                        }
                    }
                    if($isCloseTag || in_array($cTag, $this->selfCose)){
                        $lastItem = strtolower(end($path));
                        if($cTag == $lastItem){
                            array_pop($path);
                        }
                    }
                case " ":
                    if($isOpenTag && !$added && !$isCSS && !$isJS){
                        $cTag            = trim($string);
                        $tokens[][$cTag] = array();
                        $string          = "";
                        $added           = true;
                        if(strtolower($cTag) !== "!doctype" && !$isCloseTag)
                            $path[]          = $cTag;
                        if(strtolower(substr($this->html, $i + 1, 6)) == "script"){
                            $isJS = true;
                        }elseif(strtolower(substr($this->html, $i + 1, 5)) == "style"){
                            $isCSS = true;
                        }
                    }
                    if($char == ">"){
                        $isOpenTag  = false;
                        $isCloseTag = false;
                        $isTag      = false;
                        $added      = false;
                    }
                    break;
                case "=":
                    if($isTag){
                        
                    }
                    break;
                default:
                    $string .= $char;
                    break;
            }
        }

        //print_r($tokens);
    }

}