<?php
namespace wechatclient\response;
/**
 * 回复视频消息
 */
class VideoResponse extends WechatResponse{
	private $template = "
		<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[video]]></MsgType>
			<Video>
				<MediaId><![CDATA[%s]]></MediaId>
				<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
			</Video>
		</xml>";
	public $mediaId,$thumbMediaId;
	public function __construct($toUserName,$fromUserName,$mediaId,$thumbMediaId){
		$this->toUserName = $toUserName;
		$this->fromUserName = $fromUserName;
		$this->createTime = time();
		$this->mediaId = $mediaId;
		$this->thumbMediaId = $thumbMediaId;
	}
	public function __toString(){
		$responseStr = sprintf($this->template,$this->toUserName,$this->fromUserName,$this->createTime,$this->$mediaId,$this->$thumbMediaId);
		return $responseStr;
	}
}
