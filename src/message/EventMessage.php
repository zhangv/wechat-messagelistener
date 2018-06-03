<?php
namespace zhangv\wechat\message;
/**
 * 事件推送消息
 */
class EventMessage extends Message {
	public $msgType = 'event';
	public $event,$eventKey;
	public $scanType,$scanResult;
	public $count,$picList;
	public $locationX,$locationY,$scale,$label,$poiname;
	public $sessionFrom;//小程序用户进入session
	public function parse($xml){
		$this->fromUserName = (string)$xml->FromUserName;
		$this->toUserName = (string)$xml->ToUserName;
		$this->createTime = (string)$xml->CreateTime;
		$this->msgType = (string)$xml->MsgType;
		$this->event = (string)$xml->Event;
		$this->eventKey = (string)$xml->EventKey;
		if(in_array($this->event,['scancode_push','scancode_waitmsg'])){
			$this->scanType = (string)$xml->ScanCodeInfo->ScanType;
			$this->scanResult = (string)$xml->ScanCodeInfo->ScanResult;
		}elseif(in_array($this->event,['pic_sysphoto','pic_photo_or_album','pic_weixin'])){
			$this->count = (string)$xml->SendPicsInfo->Count;
			$this->picList = (string)$xml->SendPicsInfo->PicList;
		}elseif($this->event == 'location_select'){
			$this->locationX = (string)$xml->SendLocationInfo->Location_X;
			$this->locationY = (string)$xml->SendLocationInfo->Location_Y;
			$this->scale = (string)$xml->SendLocationInfo->Scale;
			$this->label = (string)$xml->SendLocationInfo->Label;
			$this->poiname = (string)$xml->SendLocationInfo->Poiname;
		}elseif($this->event == 'user_enter_tempsession'){
			$this->sessionFrom = (string)$xml->SessionFrom;
		}
		return $this;
	}
}
