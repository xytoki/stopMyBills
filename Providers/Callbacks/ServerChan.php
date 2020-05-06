<?php
namespace xyToki\StopMyBills\Providers\Callbacks;
use Curl\Curl;
class ServerChan{
    function __construct($config){
		$this->sckey = $config->sckey;
	}
	function call($config,$amount,$low,$high){
		if(!$low&&!$high)return;
		$title = ("StopMyBills通知: {$config->name}已").($low?"耗尽":'').($high?"超额":'');
		$text = "服务{$config->name}当前用量{$amount}，";
		if($low){
			$text.="低于限额{$config->minAmount}，";
		}
		if($high){
			$text.="超过上限{$config->maxAmount}，";
		}
		$text.="已被自动停止。";
		$data = [
			"text"=>$title,
			"desp"=>$text
		];
		l()->debug("[ServerChan > Send] ".print_r($data,true));
		$curl = new Curl();
		$curl->post('https://sc.ftqq.com/'.$this->sckey.'.send',$data);
		l()->error("[ServerChan > Response] ".print_r($curl->response,true));
	}
}