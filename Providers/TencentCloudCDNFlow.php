<?php
namespace xyToki\StopMyBills\Providers;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Cdn\V20180606\CdnClient;
use TencentCloud\Cdn\V20180606\Models\DescribeBillingDataRequest;
class TencentCloudCDNFlow extends TencentCloud{
	function __construct($creds){
		parent::__construct($creds);
	}
	public function getAmount(){
        $httpProfile = new HttpProfile();
		$httpProfile->setEndpoint("cdn.tencentcloudapi.com");
        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new CdnClient($this->cred, "", $clientProfile);
		
		$req = new DescribeBillingDataRequest();
		$req->EndTime = date("Y-m-d H:i:00",time()-120);
		$req->StartTime = date("Y-m-d H:i:00",time()-3600*24-60);
		$req->Interval = "min";
		$req->Metric = "flux";
		$resp = $client->DescribeBillingData($req);
		$bytes = $resp->Data[0]->BillingData[0]->SummarizedData->Value;
		$amount = round( $bytes / 1024 / 1024 * 100 ) / 100;
        l()->info("[TencentCloudCDNBandwidth\getAmount] ".$amount);
        return $amount;
    }
	public function stopService(){
		$this->stopCDN();
	}
}