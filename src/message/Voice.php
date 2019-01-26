<?php
namespace zhangv\wechat\messagelistener\message;
/**
 * 语音消息
 */
class Voice extends Message{
	public $msgType = 'voice';
	public $mediaId,$format,$recognition;
	public function parse($xml){
		$this->fromUserName = (string)$xml->FromUserName;
		$this->toUserName = (string)$xml->ToUserName;
		$this->createTime = (string)$xml->CreateTime;
		$this->msgType = (string)$xml->MsgType;
		$this->mediaId = (string)$xml->MediaId;
		$this->format = (string)$xml->Format;
		if($xml->Recognition)//语音识别
			$this->recognition = (string)$xml->Recognition;
		$this->msgId = (string)$xml->MsgId;
		return $this;
	}
}
