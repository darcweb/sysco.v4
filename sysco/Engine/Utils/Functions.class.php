<?php
namespace Sysco\Engine\Utils;

class Functions {

        public $saslog;
        public $sysco;

        function __construct($sysco){

            $this->sysco = $sysco;

        }

        function delTree($dir){
            $files = @array_diff(scandir($dir), array('.','..')); 
            if(is_array($files)){
                foreach (@$files as $file) { 
                    (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file"); 
                } 
                return @rmdir($dir); 
            }else{
                return false; 
            }
        }

        public function onlineCheck(){
            if(strpos($this->sysco->params[$_SERVER['SYSTEM']]['domain'],$this->sysco->params[$_SERVER['SYSTEM']]['baseurl']) !== false){
                return true;
            }else{
                return false;
            }
        }

        function thisToken(){
            return $_COOKIE['PHPSESSID'];
        }

        function thisIP(){
                $ip = (isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'unknown'); // pegando o endereço remoto ou definindo-o como desconhecido
                $forward = ( isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:false);  // pegando o endereço que foi repassado (se houver)
                $ip=(($ip=='unknown'&&$foward&&$forward!='unknown')?$forward:$ip); // verifica se existe um redirecionado e o retorna, caso contrário mantém o remoto.
                return $ip;
        }

        function thisDevice(){
                $deviceid = $this->thisToken();
                return $deviceid;
        }

        public function charSet($string=""){
                if($string){
                        $result = $string;
                        $result = preg_replace("/[^a-zA-Z0-9_.]/", "", $result);
                }
                return $result;
        }

        public function percent($value=0,$discount=0,$type='value'){
                $percent = ($value/100)*$discount;
                if($type == 'discount'){
                        $result = $percent;
                }else{
                        $result = $value-$percent;
                }
                $result = round($result, 2);
                return $result;
        }

        function generateAccountCode($userID){
                $number = rand(0,9).rand(0,9).rand(0,9).$userID.rand(0,9);
                $rowcod = $this->sysco->objectQuery("SELECT * FROM sysco_accounts WHERE token='".$number."' and token!=''");
                if($rowcod->id!=""){
                        $codRestore = $this->restoreAccountCode($userID);
                } else {
                        $codRestore = $number;
                }
                return $codRestore;
        }

        function formatNumber($pregNumber){
                $number = (string)$pregNumber;
                $number = substr('0000000000', 0, -strlen($number)).$number;
                return $number;
        }

        function sizeFileLabel($size){
                if($size >= 1073741824){
                        $sizeB = $size/1073741824;
                        $sizB = explode(".", $sizeB);
                        $sizeArq = $sizB[0].",".substr($sizB[1], 0, 1)." Gb";
                }else if($size >= 1048576){
                        $sizeB = $size/1048576;
                        $sizB = explode(".", $sizeB);
                        $sizeArq = $sizB[0].",".substr($sizB[1], 0, 1)." Mb";
                }else if($size >= 1024){
                        $sizeB = $size/1024;
                        $sizB = explode(".", $sizeB);
                        $sizeArq = $sizB[0].",".substr($sizB[1], 0, 1)." Kb";
                }else{
                        $sizeArq = $size." Bytes";
                }
                return $sizeArq;
        }

        public function formatMoney($valueSet=0, $typeSet=''){
                if($typeSet == 'milhar'){
                        $result = @number_format($valueSet, 0,',','.');
                        if($result == ""){
                                $result = "0";
                        }
                }else if($typeSet == 'clean'){
                        $result = @str_replace('.', '', $valueSet);
                        $result = @str_replace(',', '.', $result);
                        if($result == ""){
                                $result = "0";
                        }
                }else{
                        if($valueSet){
                                $result = @number_format($valueSet, 2,',','.');
                                if($result == ""){
                                        $result = "0,00";
                                }
                        }else{
                                $result = "";
                        }
                }
                return $result;
        }

        function addslashes_array($a){
        if(is_array($a)){
            foreach($a as $n=>$v){
                $b[$n]=addslashes_array($v);
            }
            return $b;
        }else{
            return addslashes($a);
        }
    }
        public function secure($string) {
                $string = @htmlspecialchars($string);
                $string = @trim($string);
                $string = @stripcslashes($string);
                $string = @mysql_escape_string($string);
                return $string;
        }

        public function datetimeSet($format='',$settime='',$acceptNull=false){
                if(strpos($settime, "/") !== false){
                        if(strpos($settime, " ") !== false){
                                $setExBar = explode(" ", $settime);
                                $setDateEx = explode("/", $setExBar[0]);
                                $settime = $setDateEx[2]."-".$setDateEx[1]."-".$setDateEx[0]." ".$setExBar[1]; 
                        }else{
                                $setDateEx = explode("/", $settime);
                                $settime = $setDateEx[2]."-".$setDateEx[1]."-".$setDateEx[0]; 
                        }			
                }
                if($settime){
                        if($format=='timestamp'){
                                $return = @strtotime($settime);
                        }else{
                                if(is_numeric($settime)){
                                        $return = @date($format, $settime);
                                }else{
                                        $return = @date($format, strtotime($settime));
                                }
                        }
                }else if($format){
                        if($format=='timestamp'){
                                $return = @mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'));
                        }else{
                                $return = @date($format);
                        }
                }else{
                        $return = @date('Y-m-d H:i:s');
                }
                if($return=='31/12/1969 21:00:00'||$return=='31/12/1969'||$return=='30/11/-0001 00:00:00'||$return=='30/11/-0001'||
                  ($acceptNull==true&&($settime==''||$settime=='0000-00-00 00:00:00'))){
                        $return = '';
                }
                return $return;
        }

        public function caseDate($data='',$type='',$abreviation=false){
                $data = $data?$data:@date('Y-m-d');
                $ano =  substr("$data", 0, 4);
                $mes =  substr("$data", 5, -3);
                $dia =  substr("$data", 8, 9);

                $diasemana = @date("w", mktime(0,0,0,$mes,$dia,$ano) );

                switch($diasemana) {
                        case"0": $diasemana = ($abreviation?"Dom":"Domingo");		break;
                        case"1": $diasemana = ($abreviation?"Seg":"Segunda-Feira");	break;
                        case"2": $diasemana = ($abreviation?"Ter":"Terça-Feira");	break;
                        case"3": $diasemana = ($abreviation?"Qua":"Quarta-Feira");	break;
                        case"4": $diasemana = ($abreviation?"Qui":"Quinta-Feira");	break;
                        case"5": $diasemana = ($abreviation?"Sex":"Sexta-Feira");	break;
                        case"6": $diasemana = ($abreviation?"Sáb":"Sábado");			break;
                }

                switch($mes) {
                        case"1": $mesdoano = ($abreviation?"Jan":"Janeiro");		break;
                        case"2": $mesdoano = ($abreviation?"Fev":"Fevereiro");	break;
                        case"3": $mesdoano = ($abreviation?"Mar":"Março");		break;
                        case"4": $mesdoano = ($abreviation?"Abr":"Abril");		break;
                        case"5": $mesdoano = ($abreviation?"Mai":"Maio");		break;
                        case"6": $mesdoano = ($abreviation?"Jun":"Junho");		break;
                        case"7": $mesdoano = ($abreviation?"Jul":"Julho");		break;
                        case"8": $mesdoano = ($abreviation?"Ago":"Agosto");		break;
                        case"9": $mesdoano = ($abreviation?"Set":"Setembro");	break;
                        case"10": $mesdoano = ($abreviation?"Out":"Outubro");	break;
                        case"11": $mesdoano = ($abreviation?"Nov":"Novembro");	break;
                        case"12": $mesdoano = ($abreviation?"Dez":"Dezembro");	break;
                }
                if($type == 'weekday'){
                        $result = $diasemana;
                }else if($type == 'month'){
                        $result = $mesdoano;
                }else{
                        $result = $diasemana.', '.$dia.' de '.$mesdoano.' de '.$ano;
                }
                return $result;
        }

        function jsonPrepare($type,$data){
                $char = array();

                $charRep = explode('-', 'u00e1-u00e0-u00e2-u00e3-u00e4-u00c1-u00c0-u00c2-u00c3-u00c4-u00e9-u00e8-u00ea-u00eb-u00c9-u00c8-u00ca-u00cb-u00ed-u00ec-u00ee-u00ef-u00cd-u00cc-u00ce-u00cf-u00f3-u00f2-u00f4-u00f5-u00f6-u00d3-u00d2-u00d4-u00d5-u00d6-u00fa-u00f9-u00fb-u00fc-u00da-u00d9-u00db-u00dc-u00e7-u00c7-u00f1-u00d1-\\');
                $charSet = explode('-', 'á-à-â-ã-ä-Á-À-Â-Ã-Ä-é-è-ê-ë-É-È-Ê-Ë-í-ì-î-ï-Í-Ì-Î-Ï-ó-ò-ô-õ-ö-Ó-Ò-Ô-Õ-Ö-ú-ù-û-ü-Ú-Ù-Û-Ü-ç-Ç-ñ-Ñ-');

                foreach($charSet as $index=>$value){
                        $char['encode'][] = $charRep[$index];
                        $char['decode'][] = $charSet[$index];
                }
                if($type == 'encode'){
                        $result = str_replace($char['decode'], $char['encode'], $result);
                }else{
                        $result = str_replace($char['encode'], $char['decode'], $result);
                }
                return $result;
        }

        function json($type,$data,$act=false){
                $char = array();

                $charRep = explode('-', 'u00e1-u00e0-u00e2-u00e3-u00e4-u00c1-u00c0-u00c2-u00c3-u00c4-u00e9-u00e8-u00ea-u00eb-u00c9-u00c8-u00ca-u00cb-u00ed-u00ec-u00ee-u00ef-u00cd-u00cc-u00ce-u00cf-u00f3-u00f2-u00f4-u00f5-u00f6-u00d3-u00d2-u00d4-u00d5-u00d6-u00fa-u00f9-u00fb-u00fc-u00da-u00d9-u00db-u00dc-u00e7-u00c7-u00f1-u00d1-\\');
                $charSet = explode('-', 'á-à-â-ã-ä-Á-À-Â-Ã-Ä-é-è-ê-ë-É-È-Ê-Ë-í-ì-î-ï-Í-Ì-Î-Ï-ó-ò-ô-õ-ö-Ó-Ò-Ô-Õ-Ö-ú-ù-û-ü-Ú-Ù-Û-Ü-ç-Ç-ñ-Ñ-');

                foreach($charSet as $index=>$value){
                        $char['encode'][] = $charRep[$index];
                        $char['decode'][] = $charSet[$index];
                }

                if($type == 'encode'){
                        $result = json_encode($data);
                        $result = str_replace($char['encode'], $char['decode'], $result);
                }else if($type == 'decode'){
                        $result = json_decode($data,$act);
                }else{
                        $result = '{"error":"Tipo de ação indefinida!"}';
                }
                return $result;
        }

        public function googleLatLong($strAddress){
                $address = $strAddress.","."Brasil";
                $request_url = "http://maps.googleapis.com/maps/api/geocode/xml?address=".$address."&sensor=true"; // A URL que vc manda pro google para pegar o XML
                $xml = simplexml_load_file($request_url);// request do XML
                if($xml){
                        $status = $xml->status;// pega o status do request, já qe a API da google pode retornar vários tipos de respostas
                        if ($status=="OK") {
                                $lat = $xml->result->geometry->location->lat;
                                $long = $xml->result->geometry->location->lng;
                                $result = $lat.",".$long; 
                        }
                        if ($status=="ZERO_RESULTS") {
                          $result = "Não Foi possível encontrar o local";
                        }
                        if ($status=="OVER_QUERY_LIMIT") {
                          $result = "A cota do GoogleMaps excedeu o limite diário";
                        }
                        if ($status=="REQUEST_DENIED") {
                          $result = "Acesso Negado";
                        }
                        if ($status=="INVALID_REQUEST") {
                          $result = "Endereço não está preenchido corretamente";
                        }
                }else{
                  $result = "Falha no carregamento da API Google Maps";
                }
                return $result;
        }

        function cropString($str='',$amount=25){
                $countChar = strlen($str)-1;
                $showTitle = $str;
                if($countChar > $amount){
                        $showTitle = substr($showTitle, 0, ($amount-3)).'...';
                }else{
                        $showTitle = $str;
                }
                return $showTitle;
        }

        function getCURL($urlSET){
                $port = str_replace(":", "", $this->setport());
                $url = $urlSET;
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent());
                curl_setopt($curl, CURLOPT_PORT, $port);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                $result = curl_exec($curl);
                curl_close($curl);
                return $result;
        }

        function saslog($dataGet=''){
                $showTable = "sysco_sas_auth";
                $this->query("CREATE TABLE IF NOT EXISTS `".$showTable."` (
                                `id` int(11) NOT NULL AUTO_INCREMENT, 
                                `father` int(11), 
                                `type` varchar(256), 
                                `name` varchar(1024), 
                                `login` varchar(256), 
                                `password` varchar(256), 
                                `email` varchar(256), 
                                `phone` varchar(256), 
                                `cellular` varchar(256), 
                                `address` varchar(1024), 
                                `city` varchar(512), 
                                `state` varchar(256), 
                                `country` varchar(256), 
                                `token` varchar(512), 
                                `lastlogin` datetime, 
                                `tag` text, 
                                `datetime` datetime, 
                                `status` int(11), 
                                PRIMARY KEY(id) 
                         ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;",false);

                $queryRoot = "SELECT * FROM `".$showTable."` WHERE id='1'";
                $checkRoot = $this->sysco->objectCount($queryRoot);
                if(!$checkRoot){
                        $this->sysco->objectConsult("INSERT INTO `".$showTable."` VALUES('0','0','root','Darc Web','root','sysco123pass','contato@darcweb.com.br','(66) 3531-4112','(66) 9603-9842','','Sinop','MT','BR','','','','".$this->datetimeSet()."','1')",false);
                        $this->sysco->objectConsult("INSERT INTO `".$showTable."` VALUES('0','1','admin','Administrador','admin','admin','email@email.com.br','(66) 3531-4112','(66) 9603-9842','','Sinop','MT','BR','','','','".$this->datetimeSet()."','1')",false);
                }

                $queryUser = "SELECT * FROM `".$showTable."` WHERE token='".$this->thisToken()."' and token!=''";
                $getUser = $this->sysco->objectQuery($queryUser);

                if($dataGet==''){
                        $result = $getUser->id;
                }else{
                        if($dataGet=='full'){
                                $result = $getUser;
                        }else{
                                $result = $getUser->$dataGet;
                        }
                }
                return $result;
        }


        function userlog($dataGet=''){
                $showTable = "sysco_accounts";
                $this->query("CREATE TABLE IF NOT EXISTS `".$showTable."` (
                                `id` int(11) NOT NULL AUTO_INCREMENT, 
                                `category` int(11), 
                                `type` varchar(32), 
                                `father` int(11), 
                                `sponsor` int(11), 
                                `name` varchar(1024), 
                                `nickname` varchar(256), 
                                `dateofbirth` varchar(32), 
                                `taxida` varchar(64), 
                                `taxidb` varchar(64), 
                                `token` varchar(512), 
                                `tag` text, 
                                `lastupdate` datetime, 
                                `datetime` datetime, 
                                `status` int(11), 
                                PRIMARY KEY(id) 
                         ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;",false);

                $this->query("CREATE TABLE IF NOT EXISTS `".$showTable."_auth` (
                                `id` int(11) NOT NULL AUTO_INCREMENT, 
                                `user` int(11), 
                                `login` varchar(256), 
                                `password` varchar(256), 
                                `token` varchar(512), 
                                `lastlogin` datetime, 
                                `datetime` datetime, 
                                `status` int(11), 
                                PRIMARY KEY(id) 
                         ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;",false);

                $this->query("CREATE TABLE IF NOT EXISTS `".$showTable."_devices` (
                                `id` int(11) NOT NULL AUTO_INCREMENT, 
                                `user` int(11), 
                                `device` varchar(256), 
                                `deviceid` varchar(256), 
                                `token` varchar(256), 
                                `lastupdate` datetime, 
                                `datetime` datetime, 
                                `status` int(11), 
                                PRIMARY KEY(id) 
                         ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;",false);

                $this->query("CREATE TABLE IF NOT EXISTS `".$showTable."_emails` (
                                `id` int(11) NOT NULL AUTO_INCREMENT, 
                                `user` int(11), 
                                `type` varchar(32), 
                                `email` varchar(256), 
                                `lastupdate` datetime, 
                                `datetime` datetime, 
                                `status` int(11), 
                                PRIMARY KEY(id) 
                         ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;",false);

                $this->query("CREATE TABLE IF NOT EXISTS `".$showTable."_phones` (
                                `id` int(11) NOT NULL AUTO_INCREMENT, 
                                `user` int(11), 
                                `type` varchar(32), 
                                `phone` varchar(32), 
                                `lastupdate` datetime, 
                                `datetime` datetime, 
                                `status` int(11), 
                                PRIMARY KEY(id) 
                         ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;",false);

                $this->query("CREATE TABLE IF NOT EXISTS `".$showTable."_address` (
                                `id` int(11) NOT NULL AUTO_INCREMENT, 
                                `user` int(11), 
                                `type` varchar(32), 
                                `address` varchar(512), 
                                `number` varchar(32), 
                                `complement` varchar(256), 
                                `neighborhood` varchar(256), 
                                `zipcode` varchar(32), 
                                `city` varchar(128), 
                                `state` varchar(32), 
                                `country` varchar(64), 
                                `lastupdate` datetime, 
                                `datetime` datetime, 
                                `status` int(11), 
                                PRIMARY KEY(id) 
                         ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;",false);

                $querySess = "SELECT * FROM `".$showTable."_devices` 
                                                WHERE device='".$this->userAgent()."' and deviceid='".$this->thisDevice()."' and token='".$this->thisToken()."' and token!=''";
                $getSess = $this->sysco->objectQuery($querySess);

                $queryUser = "SELECT acc.*, 
                                                   accmail.email AS email, 
                                                   acccellular.phone AS cellular, 
                                                   accphone.phone AS phone, 
                                                   accauth.password AS password, 
                                                   accauth.lastlogin AS lastlogin, 
                                                   accaddress.address AS address, 
                                                   accaddress.number AS number, 
                                                   accaddress.complement AS complement, 
                                                   accaddress.neighborhood AS neighborhood, 
                                                   accaddress.zipcode AS zipcode, 
                                                   accaddress.city AS city, 
                                                   accaddress.state AS state, 
                                                   accaddress.country AS country, 
                                                   accmanager.content AS contentmanager,
                                                   acccontent.id AS content 
                                        FROM ".$showTable." acc
                                        LEFT JOIN (SELECT emailGet.* FROM ".$showTable."_emails emailGet WHERE emailGet.user='".$getSess->user."' ORDER BY emailGet.id DESC) accmail ON acc.id=accmail.user
                                        LEFT JOIN (
                                                        SELECT getcellular.user, getcellular.type, getcellular.phone 
                                                        FROM ".$showTable."_phones getcellular WHERE getcellular.type='mobile'
                                                        ORDER BY getcellular.id DESC
                                                ) acccellular ON acc.id=acccellular.user
                                        LEFT JOIN (
                                                        SELECT getphone.user, getphone.type, getphone.phone 
                                                        FROM ".$showTable."_phones getphone WHERE getphone.type='fixed'
                                                        ORDER BY getphone.id DESC
                                                ) accphone ON acc.id=accphone.user
                                        LEFT JOIN (
                                                        SELECT authGet.* FROM ".$showTable."_auth authGet WHERE authGet.user='".$getSess->user."' ORDER BY authGet.id DESC
                                                ) accauth ON acc.id=accauth.user
                                        LEFT JOIN (
                                                        SELECT addressGet.* FROM ".$showTable."_address addressGet WHERE addressGet.user='".$getSess->user."' ORDER BY addressGet.id DESC
                                                ) accaddress ON acc.id=accaddress.user
                                        LEFT JOIN (
                                                        SELECT managerGet.* FROM ".$showTable."_content_manager managerGet WHERE managerGet.user='".$getSess->user."' ORDER BY managerGet.id DESC
                                                ) accmanager ON acc.id=accmanager.user
                                        LEFT JOIN (
                                                        SELECT contentGet.* FROM ".$showTable."_content contentGet WHERE contentGet.user='".$getSess->user."' ORDER BY contentGet.id DESC
                                                ) acccontent ON acc.id=acccontent.user
                                        WHERE acc.id='".$getSess->user."'
                                        ORDER BY acc.id DESC";//"SELECT * FROM `".$showTable."` WHERE id='".$getSess->user."'";

                $getUser = $this->sysco->objectQuery($queryUser);
                if($dataGet==''){
                        $result = $getSess;
                }else{
                        if($dataGet=='full'){
                                $result = $getUser;
                        }else if($dataGet=='firstname'){
                                $nameEx = explode(' ',$getUser->name);
                                $result = $nameEx[0];
                        }else{
                                $result = $getUser->$dataGet;
                        }
                }
                return $result;
        }

        function userinfo($userGet=''){
                $showTable = "sysco_accounts";

                $queryUser = "SELECT acc.*, 
                                                   accmail.email AS email, 
                                                   acccellular.phone AS cellular, 
                                                   accphone.phone AS phone, 
                                                   accauth.password AS password, 
                                                   accauth.lastlogin AS lastlogin, 
                                                   accaddress.address AS address, 
                                                   accaddress.number AS number, 
                                                   accaddress.complement AS complement, 
                                                   accaddress.neighborhood AS neighborhood, 
                                                   accaddress.zipcode AS zipcode, 
                                                   accaddress.city AS city, 
                                                   accaddress.state AS state, 
                                                   accaddress.country AS country, 
                                                   accmanager.content AS contentmanager, 
                                                   acccontent.id AS content 
                                        FROM ".$showTable." acc
                                        LEFT JOIN (SELECT emailGet.* FROM ".$showTable."_emails emailGet WHERE emailGet.user='".$userGet."' ORDER BY emailGet.id DESC) accmail ON acc.id=accmail.user
                                        LEFT JOIN (
                                                        SELECT getcellular.user, getcellular.type, getcellular.phone 
                                                        FROM ".$showTable."_phones getcellular WHERE getcellular.type='mobile'
                                                        ORDER BY getcellular.id DESC
                                                ) acccellular ON acc.id=acccellular.user
                                        LEFT JOIN (
                                                        SELECT getphone.user, getphone.type, getphone.phone 
                                                        FROM ".$showTable."_phones getphone WHERE getphone.type='fixed'
                                                        ORDER BY getphone.id DESC
                                                ) accphone ON acc.id=accphone.user
                                        LEFT JOIN (
                                                        SELECT authGet.* FROM ".$showTable."_auth authGet WHERE authGet.user='".$userGet."' ORDER BY authGet.id DESC
                                                ) accauth ON acc.id=accauth.user
                                        LEFT JOIN (
                                                        SELECT addressGet.* FROM ".$showTable."_address addressGet WHERE addressGet.user='".$userGet."' ORDER BY addressGet.id DESC
                                                ) accaddress ON acc.id=accaddress.user
                                        LEFT JOIN (
                                                        SELECT managerGet.* FROM ".$showTable."_content_manager managerGet WHERE managerGet.user='".$userGet."' ORDER BY managerGet.id DESC
                                                ) accmanager ON acc.id=accmanager.user
                                        LEFT JOIN (
                                                        SELECT contentGet.* FROM ".$showTable."_content contentGet WHERE contentGet.user='".$userGet."' ORDER BY contentGet.id DESC
                                                ) acccontent ON acc.id=acccontent.user
                                        WHERE acc.id='".$userGet."'
                                        ORDER BY acc.id DESC";//"SELECT * FROM `".$showTable."` WHERE id='".$getSess->user."'";

                $getUser = $this->sysco->objectQuery($queryUser);

                return $getUser;
        }

        public function sysLog($userSet='',$tableSet='',$titleSet='',$logSet=''){
                $showTable = "sysco_log_".$this->datetimeSet('Y_m');

                $createLog = "CREATE TABLE IF NOT EXISTS `".$showTable."` (
                                `id` int(11) NOT NULL AUTO_INCREMENT, 
                                `user` int(11), 
                                `type` varchar(256), 
                                `title` varchar(1024), 
                                `table` varchar(256), 
                                `log` text, 
                                `datetime` datetime, 
                                `status` int(11), 
                                PRIMARY KEY(id) 
                         ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";

                $createTableLog = $this->sysco->objectConsult($createLog,false);

                $showLog = $this->sysco->objectConsult("INSERT INTO `".$showTable."` VALUES(
                        '0',
                        '".$this->saslog->id."',
                        'log',
                        '".$titleSet."',
                        '".$tableSet."',
                        '".$logSet."',
                        '".$this->datetimeSet()."',
                        '1'
                )",false);
                //$showLog = $this->sysco->objectConsult("INSERT INTO `".$showTable."` (id)VALUES('')",false);
        }

        function dbLiberty($string){
                $result = false;
                if(strpos($this->local(),'sas_') === false || strpos($string,'SELECT') !== false ||  strpos($string,'CREATE') !== false || 
                  (strpos($this->local(),'sas_') !== false && $this->saslog->id!='') || (strpos($this->local(),'sas_') !== false && strpos($string,'sas_auth') !== false)){
                        $result = true;
                }
                return $result;
        }

        public function configuration($type, $config, $item=''){
                $object = $this->sysco->objectQuery("SELECT * FROM sysco_configurations WHERE type='".$type."' and config='".$config."' ");
                if($object->id != ""){
                        if($object->value == 'noedit'){
                                $return = $object->status;
                        }else{
                                if($object->status == 1){
                                        if($item != ''){
                                                if(($item == 'data' || $item == 'tag')){
                                                        $return = htmlspecialchars_decode($object->$item);
                                                }else{
                                                        $return = $object->$item;
                                                }
                                        }else{
                                                if(strpos($config,'title') !== false){
                                                        $return = $object->title;
                                                }else if($object->value){
                                                        $return = $object->value;
                                                }else{
                                                        $return = htmlspecialchars_decode($object->data);
                                                }
                                        }
                                }
                        }
                }else{
                        $return = "";
                }
                return $return;
        }

        public function linkTitle($string,$case=''){
                $special = array('Á','À','Ã','Ä','Â','á','à','ã','ä','â','É','È','Ë','Ê','é','è','ë','ê','Í','Ì','Ï','Î','í','ì','ï','î','Ó','Ò','Õ','Ö','Ô','ó','ò','õ','ö','ô','Ú','Ù','Ü','Û','ú','ù','ü','û','Ç','ç','=','/','"',':','?','{','}','{}','(',')','()',' ','%');
                $normal = array('A','A','A','A','A','a','a','a','a','a','E','E','E','E','e','e','e','e','I','I','I','I','i','i','i','i','O','O','O','O','O','o','o','o','o','o','U','U','U','U','u','u','u','u','C','c','','','','','','','','','','','','+','percent');
                $string = str_replace($special, $normal, $string);
                if($case == 'caseup'){
                        $string = strtoupper($string);
                }else if($case == 'caselower'){
                        $string = strtolower($string);
                }
                return $string;
        }

        public static function strCase($string="",$action='lower'){
            if($action == 'lower'){
                $result = mb_convert_case($string, MB_CASE_LOWER, "UTF-8");
            }else if($action == 'upper'){
                $result = mb_convert_case($string, MB_CASE_UPPER, "UTF-8");
            }
            return $result;
        }

        public static function onlyChar($string=""){
            if($string){
                $result = $string;
                $result = preg_replace("/[^a-zA-Z]/", "", $result);
            }
            return $result;
        }

        public static function onlyNumber($string=""){
            $result = "";
            if($string){
                $result = $string;
                $result = preg_replace("/[^0-9]/", "", $result);
            }
            return $result;
        }

        function getProtocoll(){
                $result = ($_SERVER['HTTPS']=='on')?'https':'http';
                $result = $this->onlyChar($this->strCase($result)).'://';
                return $result;
        }

        function setport(){
                if($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443'){
                        $port = ':'.$_SERVER['SERVER_PORT'];
                }else{
                        $port = '';
                }
                return $port;
        }

        function local(){
                $server = $_SERVER['SERVER_NAME'];
                $uri = $_SERVER['REQUEST_URI'];
                $local = $this->getProtocoll().$server.$this->setport().$uri;
                return $local;
        }

        function checkURL($link){         
                $partes_url = @parse_url( $link ); 

                if ( empty( $partes_url["host"] ) ) return( false ); 

                if ( !empty( $partes_url["path"] ) ){ 
                        $path_documento = $partes_url["path"]; 
                } else { 
                        $path_documento = "/"; 
                } 
                if ( !empty( $partes_url["query"] ) ) { 
                        $path_documento .= "?" . $partes_url["query"]; 
                } 

                $host = $partes_url["host"]; 
                $porta = $partes_url["port"]; 
                // faz um (HTTP-)GET $path_documento em $host"; 

                if (empty( $porta ) ) $porta = "80"; 
                $socket = @fsockopen( $host, $porta, $errno, $errstr, 30 ); 
                if (!$socket){ 
                        return(false); 
                } else { 
                        fwrite ($socket, "HEAD ".$path_documento." HTTP/1.0\r\nHost: $host\r\n\r\n"); 
                        $http_response = fgets( $socket, 22 ); 

                        $pos = null;        
                        $pos = strpos($http_response, "200 OK"); 
                        if ( !empty($pos) ) { 
                                fclose( $socket );        
                                return(true); 
                        } else { 
                                //echo "HTTP-Response: $http_response<br>"; 
                                return(false); 
                        } 
                } 
        } 

        public function userAgent(){
                $browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
                //echo $browser;
                if(strpos($browser, 'Opera') !== false){
                         //echo 'Funções para o navegadore navegador Opera';
                }else if(strpos($browser, 'Chrome') !== false){
                         //echo 'Funções para o navegadore Chrome';
                }else if(strpos($browser, 'Firefox') !== false){
                         //echo 'Funções para o navegadore Mozilla/Firefox';
                }else if((strpos($browser, 'MSIE 7') !== false && strpos($browser, 'Trident/4.0') !== false) || strpos($browser, 'MSIE 8') !== false){
                        if(strpos($browser, 'Win64') !== false){
                                echo '<script>
                                                alert("Seu navegador está desatualizado, clique em ok para atualizar!");
                                                window.location="http://download.microsoft.com/download/E/8/7/E872ACDB-EE4E-4B4B-A2AA-7A1CB6E5D248/IE9-Windows7-x64-ptb.exe";
                                          </script>';
                        }else{
                                echo '<script>
                                                alert("Seu navegador está desatualizado, clique em ok para atualizar!");
                                                window.location="http://download.microsoft.com/download/7/B/D/7BD95543-D8A7-474F-8A79-34DE266AAC27/IE9-Windows7-x86-ptb.exe";
                                          </script>';
                        }
                }else{
                         //echo 'Funções para outros navegadores';
                }
                return $browser;
        }

    function prepareTextRev($baseText) {
        $result = $baseText;
        $result = str_replace("_ARR_","@",$result);
        $result = str_replace("_PAL_","(",$result);
        $result = str_replace("_PAR_",")",$result);
        $result = str_replace("_HIF_","-",$result);
        $result = str_replace("_DOT_",".",$result);
        return $result;
    }

    function prepareText($baseText) {
        $result = $baseText;
        $result = str_replace("@","_ARR_",$result);
        $result = str_replace("(","_PAL_",$result);
        $result = str_replace(")","_PAR_",$result);
        $result = str_replace("-","_HIF_",$result);
        $result = str_replace(".","_DOT_",$result);
        return $result;
    }

        function prepareBase($baseText){
                $resultSet = $baseText;

                $equals = "=";
                $bar = "/";
                $possibleEquals = array("cA4s","cI1s","cH1s","cF7s","c43s");
                $possibleBar = array("s4Ac","s1Ic","s1Hc","s7Fc","s34c");

                //shuffle($possibleEquals);
                //shuffle($possibleBar);

                $setCrypt = true;
                foreach($possibleEquals as $xEquals){
                        if(strpos($resultSet,$xEquals) !== false){
                                $setCrypt = false;
                        }
                }
                foreach($possibleBar as $xBar){
                        if(strpos($resultSet,$xBar) !== false){
                                $setCrypt = false;
                        }
                }

                if($setCrypt == true){
                        $resultPrepare = "";
                        $xEquals = explode("=",$resultSet);
                        $peStage = 0;
                        for($xfor=0; $xfor < count($xEquals); $xfor++){
                                $resultPrepare .= $xEquals[$xfor].($xfor < (count($xEquals)-1)?$possibleEquals[$peStage]:'');//.str_replace($equals, $possibleEquals[$peStage], $resultSet);
                                $peStage++;
                                if($peStage >= count($possibleEquals)){
                                        $peStage = 0;
                                }
                        }
                        $resultSet = ($resultPrepare==""?$resultSet:$resultPrepare);

                        $resultPrepare = "";
                        $xBars = explode("/",$resultSet);
                        $pbStage = 0;
                        for($xfor=0; $xfor < count($xBars); $xfor++){
                                $resultPrepare .= $xBars[$xfor].($xfor < (count($xBars)-1)?$possibleBar[$pbStage]:'');//.str_replace($bar, $possibleBar[$pbStage], $resultSet);
                                $pbStage++;
                                if($pbStage >= count($possibleBar)){
                                        $pbStage = 0;
                                }
                        }
                        $resultSet = ($resultPrepare==""?$resultSet:$resultPrepare);
                }else{
                        foreach($possibleEquals as $xEquals){
                                $resultSet = str_replace($xEquals, $equals, $resultSet);
                        }
                        foreach($possibleBar as $xBar){
                                $resultSet = str_replace($xBar, $bar, $resultSet);
                        }
                }
                return $resultSet;
        }

        function encodeCrypt($plainText=""){
                $plainText = $this->prepareText($plainText);
                $plainText = urldecode($plainText);
                $prepareCrypt = base64_encode($plainText);
                $prepareCrypt = $this->prepareBase($prepareCrypt);

                $showChar = $prepareCrypt;
                $amountChar = strlen($prepareCrypt);
                $codeCrypt = "";
                $splitCode = "";
                $charSet = 0;
                for ($i = 0; $i < $amountChar; $i++){
                        $splitCode = $splitCode.$showChar[$i];
                }
                $codeCrypt = $splitCode;
                return $codeCrypt;
        }

        function decodeCrypt($baseText=""){
                $prepareCrypt = urldecode($baseText);
                $amountChar = strlen($prepareCrypt);
                $showChar = $prepareCrypt;
                $codeCrypt = "";
                $splitCode = "";
                for ($i = 0; $i < $amountChar; $i++){
                        $splitCode = $splitCode.$showChar[$i];
                }
                $codeCrypt = $splitCode;
                $codeCrypt = $this->prepareBase($codeCrypt);
                $plainTextBytes = base64_decode($codeCrypt);
                $codeCrypt = $plainTextBytes;
                $codeCrypt = (mb_detect_encoding($codeCrypt.'x', 'UTF-8, ISO-8859-1') == 'UTF-8')?$codeCrypt:'';
                return $this->prepareTextRev($codeCrypt);
        }

        function preparePost($html){
                $return = "";
                #put all opened tags into an array
                preg_match_all("#<(?!img)([a-z]+)( .*)?(?!/)>#iU", $html, $result);
                $openedtags = $result[1];
                $len_opened = count($openedtags);

                #put all closed tags into an array
                preg_match_all("#<\/([a-z]+)>#iU", $html, $result);
                $closedtags = $result[1];
                $len_closed = count($closedtags);

                //print_r($openedtags);
                //print_r($closedtags);

                # all tags are closed
                if($len_closed == $len_opened){
                        $return = $html;
                }
                if($return == ""){
                        $openedtags = array_reverse($openedtags);
                        # close tags
                        for($i=0; $i<$len_opened; $i++){
                                if (!in_array($openedtags[$i], $closedtags)){
                                        $html .= "</".$openedtags[$i].">";
                                        unset($closedtags[array_search($openedtags[$i], $closedtags)]);
                                }else{
                                        unset($closedtags[array_search($openedtags[$i], $closedtags)]);
                                }
                        }
                        $return = $html;
                }
                $return = str_replace(array('</br></br>','</br></img>','</img></br>','</img>','</source></source>','</div></div>
'),'',$return);
                $return = preg_replace('/<iframe.*src="(.*?)".*?>/', '<iframe src="${1}" height="460" class="wid-max" frameborder="0">', $return);
                return $return;
        }


        function prepareImages($string,$type='get',$alt='',$title=''){
        $return = "";
                $string = htmlspecialchars_decode($string);
                if($type == 'prepare'){
                        $prepare = preg_replace('/<img.*src="(.*?)".*\/?>/', '<img src="${1}" '.($alt?'alt="'.$alt.'"':'').' '.($title?'title="'.$title.'"':'').' class="image-preview-post clearfix" />', $string);
                        $return = $prepare;
                }else if($type == 'get'){
                        $str = explode('src', $string);
                        $str = explode('"', $str[1]);
                        $str = explode('?', $str[1]);
                        $setimage = $str[0];
                        if(!$setimage){
                                $str = explode('src', $string);
                                $str = explode('&quot;', $str[1]);
                                $str = explode('&quot;', $str[1]);
                                $setimage = $str[0];
                        }
                        $return = $setimage;
                }
                return $return;
        }

        function getMobileLocation($iphone=true,$android=true,$opera=true,$blackberry=true,$palm=true,$windows=true,$mobileredirect=false,$desktopredirect=false){
                $mobileVerify = $this->mobile_device_detect($iphone,$android,$opera,$blackberry,$palm,$windows,$mobileredirect,$desktopredirect);
                return $mobileVerify;
        }

        function mobile_device_detect($iphone=true,$android=true,$opera=true,$blackberry=true,$palm=true,$windows=true,$mobileredirect=false,$desktopredirect=false){

          $mobile_browser   = false; // set mobile browser as false till we can prove otherwise
          $user_agent       = $_SERVER['HTTP_USER_AGENT']; // get the user agent value - this should be cleaned to ensure no nefarious input gets executed
          $accept           = $_SERVER['HTTP_ACCEPT']; // get the content accept value - this should be cleaned to ensure no nefarious input gets executed

          switch(true){ // using a switch against the following statements which could return true is more efficient than the previous method of using if statements

            case (eregi('ipod',$user_agent)||eregi('iphone',$user_agent)); // we find the words iphone or ipod in the user agent
              $mobile_browser = $iphone; // mobile browser is either true or false depending on the setting of iphone when calling the function
              if(substr($iphone,0,4)=='http'){ // does the value of iphone resemble a url
                $mobileredirect = $iphone; // set the mobile redirect url to the url value stored in the iphone value
              } // ends the if for iphone being a url
            break; // break out and skip the rest if we've had a match on the iphone or ipod

            case (eregi('android',$user_agent));  // we find android in the user agent
              $mobile_browser = $android; // mobile browser is either true or false depending on the setting of android when calling the function
              if(substr($android,0,4)=='http'){ // does the value of android resemble a url
                $mobileredirect = $android; // set the mobile redirect url to the url value stored in the android value
              } // ends the if for android being a url
            break; // break out and skip the rest if we've had a match on android

            case (eregi('opera mini',$user_agent)); // we find opera mini in the user agent
              $mobile_browser = $opera; // mobile browser is either true or false depending on the setting of opera when calling the function
              if(substr($opera,0,4)=='http'){ // does the value of opera resemble a rul
                $mobileredirect = $opera; // set the mobile redirect url to the url value stored in the opera value
              } // ends the if for opera being a url 
            break; // break out and skip the rest if we've had a match on opera

            case (eregi('blackberry',$user_agent)); // we find blackberry in the user agent
              $mobile_browser = $blackberry; // mobile browser is either true or false depending on the setting of blackberry when calling the function
              if(substr($blackberry,0,4)=='http'){ // does the value of blackberry resemble a rul
                $mobileredirect = $blackberry; // set the mobile redirect url to the url value stored in the blackberry value
              } // ends the if for blackberry being a url 
            break; // break out and skip the rest if we've had a match on blackberry

            case (preg_match('/(palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',$user_agent)); // we find palm os in the user agent - the i at the end makes it case insensitive
              $mobile_browser = $palm; // mobile browser is either true or false depending on the setting of palm when calling the function
              if(substr($palm,0,4)=='http'){ // does the value of palm resemble a rul
                $mobileredirect = $palm; // set the mobile redirect url to the url value stored in the palm value
              } // ends the if for palm being a url 
            break; // break out and skip the rest if we've had a match on palm os

            case (preg_match('/(windows ce; ppc;|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent)); // we find windows mobile in the user agent - the i at the end makes it case insensitive
              $mobile_browser = $windows; // mobile browser is either true or false depending on the setting of windows when calling the function
              if(substr($windows,0,4)=='http'){ // does the value of windows resemble a rul
                $mobileredirect = $windows; // set the mobile redirect url to the url value stored in the windows value
              } // ends the if for windows being a url 
            break; // break out and skip the rest if we've had a match on windows

            case (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|pda|psp|treo)/i',$user_agent)); // check if any of the values listed create a match on the user agent - these are some of the most common terms used in agents to identify them as being mobile devices - the i at the end makes it case insensitive
              $mobile_browser = true; // set mobile browser to true
            break; // break out and skip the rest if we've preg_match on the user agent returned true 

            case ((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0)); // is the device showing signs of support for text/vnd.wap.wml or application/vnd.wap.xhtml+xml
              $mobile_browser = true; // set mobile browser to true
            break; // break out and skip the rest if we've had a match on the content accept headers

            case (isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE'])); // is the device giving us a HTTP_X_WAP_PROFILE or HTTP_PROFILE header - only mobile devices would do this
              $mobile_browser = true; // set mobile browser to true
            break; // break out and skip the final step if we've had a return true on the mobile specfic headers

            case (in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','comp'=>'comp','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','tosh'=>'tosh','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',))); // check against a list of trimmed user agents to see if we find a match
              $mobile_browser = true; // set mobile browser to true
            break; // break even though it's the last statement in the switch so there's nothing to break away from but it seems better to include it than exclude it

          } // ends the switch 

          // tell adaptation services (transcoders and proxies) to not alter the content based on user agent as it's already being managed by this script
          /*header('Cache-Control: no-transform'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
          header('Vary: User-Agent, Accept'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies

          // if redirect (either the value of the mobile or desktop redirect depending on the value of $mobile_browser) is true redirect else we return the status of $mobile_browser
          if($redirect = ($mobile_browser==true) ? $mobileredirect : $desktopredirect){
            header('Location: '.$redirect); // redirect to the right url for this device
            exit;
          }else{ */
            return $mobile_browser; // will return either true or false 
          //}

        } // ends function mobile_device_detect

}
