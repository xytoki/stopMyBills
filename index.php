<?php
use mattmezza\logger\LevelLogger;
use xyToki\StopMyBills\Libs\Loader;
use xyToki\StopMyBills\Libs\Checker;
define("_LOCAL",dirname(__FILE__));
require __DIR__ . '/vendor/autoload.php';
function l(){
    global $logger;
    if(!$logger){
        $logger = new LevelLogger();
    }
    return $logger;
}
function main_handler($event=[],$context=[]){
    list($config,$callbacks) = Loader::load();
    foreach( $config as $one ){
        Checker::check($one,$callbacks);
    }
    return true;
}
if(!isset($_ENV['TENCENTCLOUD_RUNENV'])){
    main_handler();
}