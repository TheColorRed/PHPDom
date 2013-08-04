<?php

class Stylesheet{

    protected $stylesheet = "";
    protected $markup     = array();

    public function loadStylesheet($string){
        $this->stylesheet = $string;
        return $this;
    }
    
    public function getMarkup(){
        if(empty($this->markup)){
            $this->parse();
        }
        return $this->markup;
    }

    protected function parse(){
        $this->cleanup();
        $length      = strlen($this->stylesheet);
        if($length == 0){
            return false;
        }
        $tokens      = array();
        $isMedia     = false;
        $depth       = 0;
        $lastChar    = "";
        $lastkeyChar = "";
        $property    = "";
        $subproperty = "";
        $string      = "";
        $selector    = "";
        $subselector = "";
        for($i = 0; $i < $length; $i++){
            $char = $this->stylesheet[$i];
            switch($char){
                case "@":
                    if(substr($this->stylesheet, $i, 6) == "@media"){
                        $isMedia = true;
                        $string .= "@";
                    }
                    $lastkeyChar = $char;
                    break;
                case "{":
                    $depth++;
                    if($depth == 1){
                        $selector          = trim($string);
                        $tokens[$selector] = array();
                    }
                    if($depth == 2){
                        $subselector                     = trim($string);
                        $tokens[$selector][$subselector] = array();
                    }
                    $string = "";
                    $lastkeyChar = $char;
                    break;
                case ":":
                    if($depth === 1 && !$isMedia && $lastkeyChar != $char){
                        $property                     = trim($string);
                        $string                       = "";
                        $tokens[$selector][$property] = "";
                    }elseif($depth === 2 && $lastkeyChar != $char){
                        $subproperty                                   = trim($string);
                        $string                                        = "";
                        $tokens[$selector][$subselector][$subproperty] = "";
                    }else{
                        $string .= ":";
                    }
                    $lastkeyChar = $char;
                    break;
                case ";":
                    if($depth === 1){
                        $tokens[$selector][$property] = trim($string);
                        $string                       = "";
                    }
                    if($depth == 2){
                        $tokens[$selector][$subselector][$subproperty] = trim($string);
                        $string                                        = "";
                    }
                    $lastkeyChar = $char;
                    break;
                case "}":
                    $depth--;
                    if($depth == 0 && $isMedia){
                        $isMedia = false;
                    }
                    $lastkeyChar = $char;
                    break;
                default:
                    $string .= $char;
                    break;
            }
            $lastChar = $char;
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