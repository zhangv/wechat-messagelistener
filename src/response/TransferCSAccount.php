<?php
namespace zhangv\wechat\messagelistener\response;
class TransferCSAccount{//消息转发到指定客服
	private $template = "
		<xml>
		    <ToUserName><![CDATA[%s]]></ToUserName>
		    <FromUserName><![CDATA[%s]]></FromUserName>
		    <CreateTime>%s</CreateTime>
		    <MsgType><![CDATA[transfer_customer_service]]></MsgType>
		    <TransInfo>
		        <KfAccount>%s</KfAccount>
		    </TransInfo>
		</xml>";
	public function __construct($toUserName,$fromUserName,$kfAccount){
		$this->toUserName = $toUserName;
		$this->fromUserName = $fromUserName;
		$this->createTime = time();
		$this->kfAccount = $kfAccount;
	}
	public function __toString(){
		$responseStr = sprintf($this->template,$this->toUserName,$this->fromUserName,$this->createTime,$this->kfAccount);
		return $responseStr;
	}
}