<?php
namespace xyToki\StopMyBills\Providers;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Cdn\V20180606\CdnClient;
use TencentCloud\Billing\V20180709\BillingClient;
use TencentCloud\Cdn\V20180606\Models\StopCdnDomainRequest;
use TencentCloud\Cdn\V20180606\Models\DescribeDomainsRequest;
use TencentCloud\Billing\V20180709\Models\DescribeAccountBalanceRequest;
class TencentCloud{
	public $ak;
	public $sk;
	function __construct($creds){
		$this->ak=$creds['ak'];
		$this->sk=$creds['sk'];
        $this->cred = new Credential($this->ak, $this->sk);
	}
	public function getAmount(){
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("billing.tencentcloudapi.com");
        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new BillingClient($this->cred, "", $clientProfile);
        $req = new DescribeAccountBalanceRequest();
        $resp = $client->DescribeAccountBalance($req);
        $amount = ( (float) $resp->Balance) /100;
        l()->info("[TencentCloud\getAmount] ".$amount);
        return $amount;
    }
	public function stopService(){
		$this->stopCDN();
	}
	public function stopCDN(){
        $cdnDomains=$this->cdnGetDomains();
        foreach( $cdnDomains as $one ){
            $this->cdnStopOne($one);
        }
	}
	public function cdnGetDomains(){
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("cdn.tencentcloudapi.com");
        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new CdnClient($this->cred, "", $clientProfile);
        $req = new DescribeDomainsRequest();
        $req->Limit=1000;
        $resp = $client->DescribeDomains($req);
        $ds=[];
        foreach($resp->Domains as $one){
            if($one->Disable=="normal"){
                $ds[]=$one->Domain;
            }
        }
		l()->info("[TencentCloud\cdnGetDomains] ".implode(" ",$ds));
        return $ds;
	}
	public function cdnStopOne($domain){
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("cdn.tencentcloudapi.com");
        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new CdnClient($this->cred, "", $clientProfile);
        $req = new StopCdnDomainRequest();
        $req->Domain=$domain;
        $resp = $client->StopCdnDomain($req);
		l()->info("[TencentCloud\cdnStopOne] ".$domain);
        return true;
	}
}