<?php
namespace xyToki\StopMyBills\Providers;
use AlibabaCloud\Client\AlibabaCloud;
class Aliyun{
	public $ak;
	public $sk;
	function __construct($creds){
		$this->ak=$creds['ak'];
		$this->sk=$creds['sk'];
		AlibabaCloud::accessKeyClient($this->ak,$this->sk)
			->regionId('cn-hangzhou')
			->asDefaultClient();
	}
	public function getAmount(){
		$result = AlibabaCloud::rpc()
			->product('BssOpenApi')
			->scheme('https')
			->version('2017-12-14')
			->action('QueryAccountBalance')
			->method('POST')
			->host('business.aliyuncs.com')
			->request();
		$amount=(float) $result['Data.AvailableAmount'];
		l()->info("[Aliyun\getAmount] ".$amount);
		return $amount;
	}
	public function stopService(){
		$this->stopCDN();
	}
	public function stopCDN(){
		$this->cdnStopDomains(
			$this->cdnGetDomains()
		);
	}
	public function cdnGetDomains(){
		$result = AlibabaCloud::rpc()
			->product('Cdn')
			->version('2018-05-10')
			->action('DescribeUserDomains')
			->method('POST')
			->host('cdn.aliyuncs.com')
			->options([
				'query' => [
					'RegionId' => "cn-hangzhou",
					'PageSize' => "500",
					'PageNumber' => "1",
					'DomainStatus' => "online",
					'CheckDomainShow' => "false",
					],
				])
			->request();
		$ds=[];
		foreach ($result['Domains.PageData'] as $d){
			$ds[]=$d['DomainName'];
		}
		l()->info("[Aliyun\cdnGetDomains] ".implode(" ",$ds));
		return $ds;
	}
	public function cdnStopDomains($domains){
		$result = AlibabaCloud::rpc()
            ->product('Cdn')
            ->version('2018-05-10')
            ->action('BatchStopCdnDomain')
            ->method('POST')
            ->host('cdn.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => "cn-hangzhou",
                    'DomainNames' => implode(",",$domains),
                ],
            ])
            ->request();
		l()->info("[Aliyun\cdnStopDomains] ".implode(" ",$domains));
	}
}