# stopMyBills  
自动关闭按流量计费业务，防止一夜之间倾家荡产。  
只需配置各服务商的ApiKey，设置最小余额，定时运行此脚本，一旦余额低于设定值，脚本将关闭支持的所有按量计费服务，保护你的资产，并支持发送通知。

### 支持的服务
 - 腾讯云 qcloud tencentcloud
     - 余额检测
     - CDN日流量检测
     - 停止CDN
 - 阿里云 aliyun alibabacloud
     - 余额检测
     - 停止CDN

### 支持的通知方式
 - Server酱 微信提醒

### 运行环境
 - 任意服务器或者电脑
 - 支持命令行的虚拟主机
 - 腾讯云函数（SCF）

### 配置方式
1. 使用`composer`安装依赖
2. 新建`.env`文件或配置环境变量。
```bash
#提供商，目前支持TencentCloud或Aliyun
XYBILL_<name>=TencentCloud 
#Secret ID
XYBILL_<name>_providerCreds_ak= 
#Secret Key
XYBILL_<name>_providerCreds_sk= 
#最小余额
XYBILL_<name>_minAmount=5 
#日志详细度
LOG_LEVEL=INFO

#配置腾讯CDM日流量上限：
XYBILL_<name>=TencentCloudCDNFlow
#Secret ID
XYBILL_<name>_providerCreds_ak= 
#Secret Key
XYBILL_<name>_providerCreds_sk= 
#最大日流量，MiB
XYBILL_<name>_maxAmount=10240 #10GiB
```

### 云服务权限
 - 如使用子账号，腾讯云需要启用`QcloudCDNFullAccess`和`QCloudFinanceFullAccess`策略。
 - 如使用子账号，阿里云需要启用`AliyunCDNFullAccess`和`AliyunBSSFullAccess`策略。