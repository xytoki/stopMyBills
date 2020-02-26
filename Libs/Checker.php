<?php
namespace xyToki\StopMyBills\Libs;
class Checker{
    static function check(Config $config){
        $provider=$config->provider;
        if(!strstr($provider,"\\")){
            $provider = "\\xyToki\\StopMyBills\\Providers\\".$provider;
        }
        $client=new $provider($config->providerCreds);
        $amount = $client->getAmount();
        if( $amount > $config->minAmount ){
            $low=false;
            l()->debug("[Checker > $config->name] $amount > $config->minAmount. Pass.");
        }else{
            $low=true;
            l()->error("[Checker > $config->name] $amount < $config->minAmount.");
            l()->error("[Checker > $config->name] Calling to stop services.");
            $client->stopService();
        }
        if( $config->callback && !empty($config->callback) ){
            $callback=$config->callback;
            if(!strstr($callback,"\\")){
                $callback = "\\xyToki\\StopMyBills\\Providers\\Callbacks\\".$callback;
            }
            $handler = new $callback($config);
            $handler->call($amount,$low);
        }
    }
}