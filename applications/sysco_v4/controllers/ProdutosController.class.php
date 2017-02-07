<?php

class ProdutosController {
    
    public $sysco = null;
    public $produtos = null;
     
    function __construct($sysco,$model){
        
        $this->sysco = $sysco;
        $this->produtos = $model;
                
    }
    
    function produtos(){
        
        $data = array(
            'name'=>'Teste',
            'description'=>'Teste description',
            'value'=>'5,60',
        );
        
        $result = $this->produtos->insert($data)."<br><br>";
        
        echo $this->sysco->objectCount("SELECT * FROM produtos")."<br><br>";
        
        return $result;
        
    }

}
