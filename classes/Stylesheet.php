<?php

class Stylesheet{

    protected $stylesheet = "";
    protected $markup     = array();

    public function loadStylesheet($string){
        $this->stylesheet = $string;
        $this->parse();
        return $this->markup;
    }

    public function parse(){
        $this->cleanup();
        $length        = strlen($this->stylesheet);
        $tokens        = array();
        $selector      = "";
        $subSelector   = "";
        $property      = "";
        $isSelector    = true;
        $isSubSelector = false;
        $isMedia       = false;
        $string        = "";
        $lastChar      = "";
        for($i = 0; $i < $length; $i++){
            $ch = $this->stylesheet[$i];
            switch($ch){
                case "@":
                    if(substr($this->stylesheet, $i, 6) == "@media"){
                        $isMedia       = true;
                        $isSubSelector = true;
                        $string .= "@";
                    }
                    break;
                case "{":
                    if($isSubSelector){
                        $subSelector                     = trim($string);
                        $string                          = "";
                        $tokens[$selector][$subSelector] = array();
                    }else{
                        $selector          = trim($string);
                        $string            = "";
                        $tokens[$selector] = array();
                    }
                        $isSelector        = false;
                    break;
                case ":":
                    if(!$isSelector){
                        $property = trim($string);
                        if($isSubSelector){
                            $tokens[$selector][$subSelector][$property] = "";
                        }else{
                            $string                       = "";
                            $tokens[$selector][$property] = "";
                        }
                    }
                    break;
                case ";":
                    $tokens[$selector][$property] = trim($string);
                    $string                       = "";
                    break;
                case "}":
                    $selector                     = "";
                    $string                       = "";
                    $isSelector                   = true;
                    break;
                default:
                    $string .= $ch;
                    break;
            }
            $lastChar = $ch;
        }
        print_r($tokens);
        $this->markup = $tokens;
        unset($tokens);
    }

    protected function cleanup(){
        $sheet            = $this->stylesheet;
        // Remove Comments
        $sheet            = preg_replace("/\/\*.+\*\//isU", "", $sheet);
        // Remove Tabs, New Lines, and Carriage Returns
        $sheet            = preg_replace("/\t|\r|\n/", "", $sheet);
        //echo $sheet."\n\n\n\n\n\n\n";
        $this->stylesheet = $sheet;
        unset($sheet);
    }

}