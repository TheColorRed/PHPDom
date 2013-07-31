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
        $length     = strlen($this->stylesheet);
        $tokens     = array();
        $selector   = "";
        $property   = "";
        $isSelector = true;
        $string     = "";
        for($i = 0; $i < $length; $i++){
            $ch = $this->stylesheet[$i];
            switch($ch){
                case "{":
                    $selector          = trim($string);
                    $string            = "";
                    $tokens[$selector] = array();
                    $isSelector        = false;
                    break;
                case ":":
                    if(!$isSelector){
                        $property                     = trim($string);
                        $string                       = "";
                        $tokens[$selector][$property] = "";
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
        }
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