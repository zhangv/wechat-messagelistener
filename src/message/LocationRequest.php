<?php
namespace wechatclient\request;

/**
 * 地理位置消息
 */
class LocationRequest extends WechatRequest{
	public $msgType = 'location';
	public $location_X,$location_Y,$scale,$label;
	public function parse($xml){
		$this->fromUserName = (string)$xml->FromUserName;
		$this->toUserName = (string)$xml->ToUserName;
		$this->createTime = (string)$xml->CreateTime;
		$this->msgType = (string)$xml->MsgType;
		$this->location_X = (string)$xml->Location_X;
		$this->location_Y = (string)$xml->Location_Y;
		$this->label = (string)$xml->Label;
		$this->scale = (string)$xml->Scale;
		$this->msgId = (string)$xml->MsgId;
		return $this;
	}
}
