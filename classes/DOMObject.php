<?php

class DOMObject{

    protected $html = "";

    public function __construct($html){
        $this->html = $html;
        $this->parse();
    }

    public function parse(){
        $length = strlen($this->html);
        $string = "";
        $tag    = false;
        $depth  = 0;
        $tokens = array();

        $cTag  = "";
        $cAttr = "";
        $cVal  = "";

        for($i = 0; $i < $length; $i++){
            $char = $this->html[$i];
            switch($char){
                case "<":
                    $tag = true;
                    break;
                case " ":
                    $cTag = $string;
                    $string = "";
                    $tokens[][$cTag] = "";
                    break;
                default:
                    $string .= $char;
                    break;
            }
        }
        
        print_r($tokens);
    }

}