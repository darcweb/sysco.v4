<?php

class Produtos {
        
    public $sysco = null;
    public $config = array();
    public $fields = array();

    function __construct($sysco){
        
        $this->sysco = $sysco;
        
        $this->config['table'] = "produtos";
        $this->config['engine'] = "MyISAM";
        $this->config['charset'] = "utf8";
        $this->config['collation'] = "utf8_general_ci";
        
        $this->fields['id'] = array('type'=>'int(11)','default'=>'','key'=>'pk');
        $this->fields['account_id'] = array('type'=>'int(32)','null'=>'yes');
        $this->fields['name'] = array('type'=>'varchar(256)','default'=>'Meu nome','null'=>'yes');
        $this->fields['description'] = array('type'=>'text','null'=>'yes');
        $this->fields['price'] = array('type'=>'float','null'=>'yes');
        $this->fields['datetime'] = array('type'=>'datetime','null'=>'yes');
        $this->fields['status'] = array('type'=>'int(11)','null'=>'yes');
        
    }
    
    function insert($param) {
        
        $result = "Nada foi feito!";
        
        $query = "INSERT INTO ".$this->config['table']." (name,description,price,datetime,status)VALUE("
                . "'".$param['name']."',"
                . "'".$param['description']."',"
                . "'".$this->sysco->functions->formatMoney($param['value'],'clean')."',"
                . "'".$this->sysco->functions->datetimeSet()."',"
                . "'1')";
        if($this->sysco->objectQuery($query)){
            $result = "Inserido com sucesso!";
        }else{
            $result = "Falha ao inserir!";
        }

        return $result;
        
    }
    
    function getitem(){
        
        $item = array(
            'name'=>'nome teste',
            'description'=>'descricao teste',
            'price'=>'10,00',
        );
        $result = $item;
        
        return $result;
        
    }
    
}
