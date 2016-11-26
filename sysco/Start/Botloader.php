<?php

/**
 * @system Sysco Framework
 * @version 4.0.1
 * 
 * @package Autoloader - Inicia o funcionamento do sistema
 * 
 * @copyright (c) 2015, DARC WEB - SOLUÇÕES WEB
 * @author Dárcio Gomes :: <darcio@darcweb.com.br>
*/

spl_autoload_register(function($class_name) {
    
    $pathsys = dirname(__FILE__);
    $pathEx = explode('/'.$_SERVER['SYSTEM'].'/',$pathsys);
    $pathsys = $pathEx[0].'/';
    
    function scann($dir,$class_name){
        
        $files = array_diff(scandir($dir), array('.','..')); 
        
        foreach ($files as $file) { 
            
            if(is_dir("$dir/$file")){
                
                scann("$dir/$file",$class_name);
                
            }else{
            
                if(strpos($file,'.php') && 
                   (strpos($dir,'/sysco/') || 
                   strpos($dir,'/controllers/') || 
                   strpos($dir,'/helpers/') || 
                   strpos($dir,'/models/'))){
                    
                    $getWay = "$dir/$file";
                    $getWay = str_replace('//','/',$getWay);
                  
                    try{
                        
                        if(strpos($getWay,"Render/views") === false && strpos($class_name,"PDO") === false){
                            
                            include($getWay);
                            
                        }
                        
                    }
                    catch(Exception $e) { }

                }
                
            }
            
        }
        
    }

    scann($pathsys,$class_name);

});

