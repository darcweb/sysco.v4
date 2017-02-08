<?php

namespace Sysco\Engine\Work;

/**
 * @system Sysco Framework
 * @version 4.0.1
 * 
 * @class Connect - Classe responsável pela conexão com o banco de dados
 * 
 * @copyright (c) 2015, DARC WEB - SOLUÇÕES WEB
 * @author Dárcio Gomes :: <darcio@darcweb.com.br>
*/

use PDO;

class Connect {

    private $connect = null;
    private $config = array();

    function __construct($getconfig){
        
        $this->config = $getconfig;
        
    }
    
    private function conn(){
        try{
            if($this->connect == null){
                
                $options = array(
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                        );
                
                if($this->config['driver'] == 'firebird'){
                    $dsn = 'firebird:dbname='.$this->config['database'].';host='.$this->config['host'].';port='.$this->config['port'];
                }else if($this->config['driver'] == 'postgresql'){
                    $dsn = 'pgsql:dbname='.$this->config['database'].';host='.$this->config['host'].';port='.$this->config['port'];
                }else if($this->config['driver'] == 'mysql'){
                    $dsn = 'mysql:host='.$this->config['host'].';port='.$this->config['port'].';dbname='.$this->config['database'];
                }
                
                $this->connect = new PDO($dsn,$this->config['user'],$this->config['password'],$options);
            }
        }catch(PDOException $e){
            echo "<hr/>ERRO NA CONEXÃO COM O BANCO DE DADOS<br /><br />Código de erro: {$e->getCode()}<br />Mensagem: {$e->getMessage()}<hr/>";
            die;
        }
        return $this->connect;
    }

    public function getConnect(){
        return $this->conn();
    }

}
