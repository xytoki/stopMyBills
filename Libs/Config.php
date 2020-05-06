<?php
namespace xyToki\StopMyBills\Libs;
class Config{
    public $name="";
    public $minAmount=0;
    public $maxAmount=PHP_INT_MAX;
    public $provider="";
    public $providerCreds;
    public $callback="";
    public $callbackCreds="";
	protected $handler = false;
	function getInstance($pfx,$params){
		if($this->handler){
			return $this->handler;
		}
		$provider=$this->provider;
		if(!strstr($provider,"\\")){
			$provider = $pfx.$provider;
		}
		$this->handler = new $provider($params);
		return $this->handler;
	}
}