<?php
namespace xyToki\StopMyBills\Libs;
use Symfony\Component\Dotenv\Dotenv;
class Loader{
    static function load(){
        $dotenv = new Dotenv(true);
        $envList=[
            ".env",
            ".env.local",
            ".env.runtime"
        ];
        foreach($envList as $one){
            $tmpfile=_LOCAL."/".$one;
            if(is_file($tmpfile))$dotenv->load($tmpfile);
        }
        if(!getenv("LOG_STRATEGY"))putenv("LOG_STRATEGY=STDOUT");
        if(!getenv("LOG_LEVEL"))putenv("LOG_LEVEL=DEBUG");
        return self::parseEnv();
    }
    static function parseEnv(){
        ksort($_ENV);
        $configs=[];
        foreach($_ENV as $k=>$value){
            $k.="_[END]_[END]_[END]";
            list($prefix,$name,$var,        $subvar,$end)=explode("_",$k,5);
            //   XYBILL _app  _providerCreds_subconfig=value
            //   0      1     2             3
            $var=str_replace("_[END]","",$var);
            $var=str_replace("[END]","",$var);
            if($prefix!="XYBILL")continue;
            if(!isset($configs[$name])){
                $configs[$name]=new Config();
                $configs[$name]->name = $name;
            }
            if($subvar && $subvar!="[END]"){
                $configs[$name]->$var = is_array($configs[$name]->$var)?$configs[$name]->$var:[];
                $configs[$name]->$var[$subvar] = $value;
            }else{
                if($var=="")$var="provider";
                $configs[$name]->$var = $value;
            }
        }
        return $configs;
    }
}