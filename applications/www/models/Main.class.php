<?php

class Main {
        
    public $sysco = null;
    public $config = array();
    public $fields = array();

    function __construct($sysco){
        
        $this->sysco = $sysco;
        
        $this->config['table'] = "sysco_accounts_users";
        $this->config['engine'] = "InnoDB";
        $this->config['charset'] = "utf8";
        $this->config['collation'] = "utf8_general_ci";
        
        $this->fields['id'] = array('type'=>'int(11)','default'=>'','key'=>'pk');
        $this->fields['account_id'] = array('type'=>'int(32)','null'=>'no');
        $this->fields['login'] = array('type'=>'varchar(64)','null'=>'no');
        $this->fields['password'] = array('type'=>'varchar(64)','null'=>'no');
        $this->fields['token'] = array('type'=>'varchar(128)','null'=>'yes');
        $this->fields['lastupdade'] = array('type'=>'datetime','null'=>'yes');
        $this->fields['datetime'] = array('type'=>'datetime','null'=>'yes');
        $this->fields['status'] = array('type'=>'int(11)','default'=>'1','null'=>'no');
        
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
