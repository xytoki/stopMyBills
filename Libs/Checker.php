<?php
namespace xyToki\StopMyBills\Libs;
class Checker{
    static function check(Config $config,array $callbackConf){
        $client=$config->getInstance("\\xyToki\\StopMyBills\\Providers\\",$config->providerCreds);
        $amount = $client->getAmount();
		
		$low=false;
		$high=false;
		
		if( $config->minAmount>0 ){
			if( $amount > $config->minAmount ){
				l()->debug("[Checker > $config->name] $amount > $config->minAmount. Pass.");
			}else{
				$low=true;
				l()->error("[Checker > $config->name] $amount < $config->minAmount.");
			}
		}
		
		if($config->maxAmount<PHP_INT_MAX){
			if( $amount < $config->maxAmount ){
				l()->debug("[Checker > $config->name] $amount < $config->maxAmount. Pass.");
			}else{
				$high=true;
				l()->error("[Checker > $config->name] $amount > $config->maxAmount.");
			}
		}
		
		if( $low || $high ){
            l()->error("[Checker > $config->name] Calling to stop services.");
            $client->stopService();
		}
        if( $config->callback && !empty($config->callback) ){
            $callbacks=explode(",",$config->callback);
			foreach($callbacks as $callbackName){
				$c = $callbackConf[$callbackName];
				$callback=$c->getInstance("\\xyToki\\StopMyBills\\Providers\\Callbacks\\",$c);
				$callback->call($config,$amount,$low,$high);
			}
        }
    }
}