<?php

namespace Sysco\Proccess;

/**
 * @system Sysco Framework
 * @version 4.0.1
 * 
 * @class Modeling - Classe de modelagem do banco de dados
 * 
 * @copyright (c) 2015, DARC WEB - SOLUÇÕES WEB
 * @author Dárcio Gomes :: <darcio@darcweb.com.br>
 */

use Sysco\Engine\Work\Connect;
use PDO;

class Modeling {
    
    public $system = null;
    public $model = null;
    
    function __construct($build,$model){
        
        $this->system = $build->system;
        $this->model = $model;
        
        $this->init();
        
    }
    
    private function charSet($string=""){
        if($string){
            $result = $string;
            $result = preg_replace("/[^a-zA-Z0-9_.]/", "", $result);
        }
        return $result;
    }

    private function adjustTable($table,$string,$log=true){
       
        $result = NULL;
        $executeQuery=true;
        
        $checktable = $this->system->objectCount("SHOW TABLES LIKE '".$table."'");
        if($checktable>0){
            
            $currentfields = count($this->model->fields);
            $showcolumns = $this->system->objectCount("SHOW COLUMNS FROM ".$table);
            $newlines = array();
            
            if($showcolumns > $currentfields){
                
                
                
            }else if($showcolumns < $currentfields){
                
                
                
            }
            
            $result = $this->system->objectList("SHOW COLUMNS FROM ".$table,false);//"SELECT ".$getCol." FROM ".$tablealter);
            while($data = $result->fetch(PDO::FETCH_OBJ)){
                
                $tableactualfield[] = $this->charSet($data->Field);

            }

        }else{
            
            $this->system->objectQuery($string);
            
        }
        
    }
    
    private $execmodel = 0;
    private function changeTable(){
        
        $tablecreate = "";
        $primarykey = "";
        $queryprepare = "";
        
        $this->model->config['engine'] = (isset($this->model->config['engine']) && $this->model->config['engine'] != ""?$this->model->config['engine']:"MyISAM");
        $this->model->config['charset'] = (isset($this->model->config['charset']) && $this->model->config['charset'] != ""?$this->model->config['charset']:"utf8");
        $this->model->config['collation'] = (isset($this->model->config['collation']) && $this->model->config['collation'] != ""?$this->model->config['collation']:"utf8_general_ci");
        
        $table = $this->model->config['table'];
        $engine = $this->model->config['engine'];
        $charset = $this->model->config['charset'];
        $collation = $this->model->config['collation'];
        
        if($table != ""){
            
            $checktable = $this->system->objectCount("SHOW TABLES LIKE '".$table."'");
            if($checktable>0){

                $currentfields = count($this->model->fields);
                $showcolumns = $this->system->objectCount("SHOW COLUMNS FROM ".$table);
                $newlines = array();
                
                if($showcolumns > $currentfields){
                    
                    $matriztable = array();
                    $matrizremoval = array();
                    
                    $xcount = 0;
                    $result = $this->system->objectList("SHOW COLUMNS FROM ".$table,false);
                    while($data = $result->fetch(PDO::FETCH_OBJ)){

                        $matriztable[$xcount] = $data->Field;
                                
                        $addremove = true;
                        $xconfig = 0;
                        foreach($this->model->fields as $field => $config){
                            
                            if($field == $matriztable[$xcount]){
                                $addremove = false;
                            }
                            
                        }

                        if($addremove){
                            $matrizremoval[$xcount] = $matriztable[$xcount];
                        }

                        $xcount++;
                    }
                    
                    foreach($matrizremoval as $field){
                        $queryalter = "ALTER TABLE `".$table."` DROP ".$field;
                        $this->system->objectQuery($queryalter);
                    }
                    
                }

                if($showcolumns < $currentfields){
                    
                    $reference = array();
                    $matrizcheck = array();
                    $matriztable = array();
                    $matrizadd = array();
                            
                    $xcount = 0;
                    $result = $this->system->objectList("SHOW COLUMNS FROM ".$table,false);
                    while($data = $result->fetch(PDO::FETCH_OBJ)){
        
                        $matriztable[$xcount] = $data->Field;
                        
                        $xcount++;
                    }

                    $add = false;

                    $xconfig = 0;
                    foreach($this->model->fields as $field => $config){
                        
                        $matrizcheck[$xconfig] = $field;
                        
                        if(!in_array($field,$matriztable)){
                            $reference[$xconfig] = isset($matrizcheck[$xconfig-1])?$matrizcheck[$xconfig-1]:array();
                            $matrizadd[$xconfig] = $matrizcheck[$xconfig];
                        }

                        $xconfig++;
                    }

                    if($add){
                        $reference[$xcount] = isset($matriztable[$xcount-1])?$matriztable[$xcount-1]:array();
                        $matrizadd[$xcount] = $matriztable[$xcount];
                    }
                    
                    foreach($matrizadd as $index => $field){
                        
                        $config = $this->model->fields[$field];
                        
                        //print_r($config);
                        
                        $setreference = (isset($reference[$index])?$reference[$index]."":"");
                        $setcolum = $field." ";
                        $settype = (isset($config['type'])?$config['type']." ":"");
                        $setnull = (isset($config['null'])?(strtoupper($config['null'])=="YES"?"NULL ":"NOT NULL "):"");
                        $setkey = (isset($config['key'])?strtoupper($config['key'])." ":"");
                        $setdefault = (isset($config['default'])&&$config['default']!=""?"DEFAULT ".$config['default']." ":"");
                        $setextra = (isset($config['extra'])?$config['extra']." ":"");

                        $queryalter = "ALTER TABLE `".$table."` ADD ".$setcolum.$settype.$setdefault.$setnull.$setextra.($setreference?" AFTER `".$setreference."`":"").";";

                        $this->system->objectQuery($queryalter);
                        
                    }
                    
                }

                $fieldsquery = "";
                $primarykey = "";
                $queryalter = "";

                $xcurrent = 0;
                $result = $this->system->objectList("SHOW COLUMNS FROM ".$table,false);
                while($data = $result->fetch(PDO::FETCH_OBJ)){

                    $tablecurrentconfig = array();
                    $tablecurrentconfig['colum'] = $data->Field;
                    $tablecurrentconfig['type'] = $data->Type;
                    $tablecurrentconfig['null'] = $data->Null;
                    $tablecurrentconfig['key'] = $data->Key;
                    $tablecurrentconfig['default'] = $data->Default;
                    $tablecurrentconfig['extra'] = $data->Extra;

                    $setconfignull = "";
                    $xconfig = 0;
                    foreach($this->model->fields as $field => $config){

                        $config['colum'] = $field;
                        $checkalter = false;

                        foreach($tablecurrentconfig as $currentconfig => $current){

                            if(isset($config[$currentconfig]) && $xconfig == $xcurrent){

                                if(isset($config['key']) && strtoupper($config['key']) == "PK"){
                                    $config['key'] = "PRI";
                                }else{
                                    $config['key'] = isset($config['key'])?strtoupper($config['key']):"";
                                }

                                if($tablecurrentconfig['key'] && $tablecurrentconfig['key'] != $config['key']){

                                    $setconfignull = "NOT NULL";

                                    $sqlexec = "ALTER TABLE ".$table." CHANGE ".$config['colum']." ".$config['colum']." ".$config['type']." ".$setconfignull.";";
                                    $this->system->objectQuery($sqlexec);

                                    $sqlexec  = "ALTER TABLE ".$table." DROP PRIMARY KEY;";
                                    $this->system->objectQuery($sqlexec);

                                }else if(!$tablecurrentconfig['key'] && $config['key'] == 'PRI'){

                                    $setconfignull = "NOT NULL";

                                    $sqlexec = "ALTER TABLE ".$table." CHANGE ".$config['colum']." ".$config['colum']." ".$config['type']." ".$setconfignull." AUTO_INCREMENT;";
                                    $this->system->objectQuery($sqlexec);

                                    $sqlexec = "ALTER TABLE ".$table." ADD PRIMARY KEY (".$config['colum'].");";
                                    $this->system->objectQuery($sqlexec);

                                }

                                if($currentconfig == "key"){
                                    $config[$currentconfig] = strtoupper($config[$currentconfig]);
                                }

                                if($currentconfig == "null"){
                                    $config[$currentconfig] = strtoupper($config[$currentconfig]);
                                }

                                if($tablecurrentconfig['key'] == "PRI"){
                                    $config['key'] = "PRI";
                                }

                                if(isset($config['key'])){
                                    if($config['key'] == "PK"){
                                        $config['key'] = "PRI";
                                    }else if($config['key'] == "FK"){
                                        $config['key'] = "FK";
                                    }
                                }

                                if($config[$currentconfig] != $current && $checkalter == false && $setconfignull != ""){

                                    $config[$currentconfig] = $setconfignull;

                                    $config[$currentconfig] = $config[$currentconfig];
                                    $checkalter = true;
                                    echo $setconfignull." - p - ".$config[$currentconfig]." - ".$current." - ";
                                }
                            }

                            if(isset($config[$currentconfig]) && $config[$currentconfig] != $current && $xconfig == $xcurrent && $setconfignull == ""){

                                if($currentconfig == "null"){
                                    $config[$currentconfig] = ($config[$currentconfig]=="YES"&&$setconfignull == ""?"NULL":"NOT NULL");
                                }

                                $config[$currentconfig] = $config[$currentconfig];
                                $checkalter = true;

                            }

                        }

                        if($checkalter){

                            $colum = $tablecurrentconfig['colum']." ";
                            $setcolum = (isset($config['colum'])?$config['colum']." ":"");
                            $settype = (isset($config['type'])?$config['type']." ":"");
                            $setnull = (isset($config['null'])?(strtoupper($config['null'])=="NULL"&&$setconfignull==""?"NULL ":"NOT NULL "):"");
                            $setkey = (isset($config['key'])?strtoupper($config['key'])." ":"");
                            $setdefault = (isset($config['default'])&&$config['default']!=""?"DEFAULT '".$config['default']."' ":"");
                            $setextra = (isset($config['extra'])?$config['extra']." ":"");

                            $queryalter = "ALTER TABLE `".$table."` CHANGE ".$colum." ".$setcolum.$settype.$setdefault.$setnull.$setextra.";";

                            $this->system->objectQuery($queryalter);

                            $checkalter = false;

                        }

                        $xconfig++;

                    }

                    $xcurrent++;
                }

                $queryprepare = $queryalter;

            }else{
                
                $fieldsquery = "";
                $primarykey = "";

                foreach($this->model->fields as $field => $config){

                    $colum = $field;
                    $type = isset($config['type'])?$config['type']:'varchar(256)';
                    $null = isset($config['null'])?strtoupper($config['null']):'YES';
                    $key = isset($config['key'])?strtoupper($config['key']):'';
                    $default = isset($config['default'])?$config['default']:'';
                    $extra = isset($config['extra'])?$config['extra']:'';

                    $fieldsquery = "`".$colum."` ".$type." ".($default!=''?"DEFAULT '".$default."'":"")." ".($null=="YES"&&$key==""?"NULL":"NOT NULL")." ".($key=="PK"?'auto_increment':'').", ";

                    if($key == "PK"){
                        
                        $primarykey = "PRIMARY KEY (`".$colum."`)";
                        
                    }

                }
                
                $fieldsquery = ($primarykey == ""?substr($fieldsquery,0,-2):$fieldsquery);
                
                $queryprepare = "CREATE TABLE IF NOT EXISTS `".$table."` (".$fieldsquery.$primarykey.") ENGINE=".$engine." CHARACTER SET ".$charset." COLLATE ".$collation.";";

                $this->system->objectQuery($queryprepare);

            }
            
        }
        
        $this->execmodel++;
        if($this->execmodel < 3){
            
            $this->changeTable();
            
        }
        
    }
    
    private function prepareTables(){
        
        
        
    }
    
    private function proccess(){
        
        $this->changeTable();
        
    }
    
    public function init(){

        //print_r($this->model);
        //echo "<br><br><br>";
        //print_r($this->system);
        
        //$this->model($this);
        //call_user_func_array($this->model, func_get_args());
        
        //Func::fromObjectMethod($this->model, $this);
        

        $this->proccess();
        
    }
    
}
