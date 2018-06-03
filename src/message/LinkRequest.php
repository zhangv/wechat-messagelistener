<?php
namespace wechatclient\request;
/**
 * 链接消息
 */
class LinkRequest extends WechatRequest{
	public $msgType = 'link';
	public $title,$description,$url;
	public function parse($xml){
		$this->fromUserName = (string)$xml->FromUserName;
		$this->toUserName = (string)$xml->ToUserName;
		$this->createTime = (string)$xml->CreateTime;
		$this->msgType = (string)$xml->MsgType;
		$this->title = (string)$xml->Title;
		$this->description = (string)$xml->Description;
		$this->url = (string)$xml->Url;
		$this->msgId = (string)$xml->MsgId;
		return $this;
	}
}