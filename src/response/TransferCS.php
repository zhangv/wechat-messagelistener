<?php

namespace zhangv\wechat\messagelistener\response;
class TransferCS{//消息转发到多客服
	private $template = "
		<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[transfer_customer_service]]></MsgType>
		</xml>";
	public function __construct($toUserName,$fromUserName){
		$this->toUserName = $toUserName;
		$this->fromUserName = $fromUserName;
		$this->createTime = time();
	}
	public function __toString(){
		$responseStr = sprintf($this->template,$this->toUserName,$this->fromUserName,$this->createTime);
		return $responseStr;
	}
}