<?php
namespace zhangv\wechat\messagelistener\message;
/**
 * 链接消息
 */
class Link extends Message{
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