<?php
namespace zhangv\wechat\messagelistener\message;
/**
 * 图片消息
 */
class Image extends Message{
	public $msgType = 'image';
	public $picUrl;

	public function parse($xml){
		$this->fromUserName = (string)$xml->FromUserName;
		$this->toUserName = (string)$xml->ToUserName;
		$this->createTime = (string)$xml->CreateTime;
		$this->msgType = (string)$xml->MsgType;
		$this->picUrl = (string)$xml->PicUrl;
		$this->msgId = (string)$xml->MsgId;
		return $this;
	}
}
