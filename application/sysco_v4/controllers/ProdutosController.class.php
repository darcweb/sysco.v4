<?php

class ProdutosController {
    
    public $sysco = null;
     
    function __construct($sysco){
        
        $this->sysco = $sysco;
        
    }
    
    function produtos(){
         
        print_r($this);
        echo "<br><br>";
        var_dump($this->sysco->params);
        var_dump($this->sysco->request->a);
        print_r($this->sysco);
        
        $text = "Controller produtos <br/>";
        
        return $text;
        
    }

}
