<?php
namespace wechatclient\request;
/**
 * 视频消息
 */
class VideoRequest extends WechatRequest{
	public $msgType = 'video';
	public $mediaId,$thumbMediaId;
	public function parse($xml){
		$this->fromUserName = (string)$xml->FromUserName;
		$this->toUserName = (string)$xml->ToUserName;
		$this->createTime = (string)$xml->CreateTime;
		$this->msgType = (string)$xml->MsgType;
		$this->mediaId = (string)$xml->MediaId;
		$this->thumbMediaId = (string)$xml->ThumbMediaId;
		$this->msgId = (string)$xml->MsgId;
		return $this;
	}
}