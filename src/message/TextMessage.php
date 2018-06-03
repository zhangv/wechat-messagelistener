<?php
namespace zhangv\wechat\message;
/**
 * 文本消息
 */
class TextMessage extends Message{
	public $msgType = 'text';
	public $content;
	public $fields = [];
	public function parse($xml){
		$this->fromUserName = (string)$xml->FromUserName;
		$this->toUserName = (string)$xml->ToUserName;
		$this->createTime = (string)$xml->CreateTime;
		$this->msgType = (string)$xml->MsgType;
		$this->content = (string)$xml->Content;
		$this->msgId = (string)$xml->MsgId;
		return $this;
	}
}
