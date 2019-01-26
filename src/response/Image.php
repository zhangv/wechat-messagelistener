<?php
namespace zhangv\wechat\messagelistener\response;
/**
 * 回复图片消息
 */
class Image extends Response {
	private $template = "
		<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[image]]></MsgType>
			<Image>
				<MediaId><![CDATA[%s]]></MediaId>
			</Image>
		</xml>";
	public $mediaId;
	public function __construct($toUserName,$fromUserName,$mediaId){
		$this->toUserName = $toUserName;
		$this->fromUserName = $fromUserName;
		$this->createTime = time();
		$this->mediaId = $mediaId;
	}
	public function __toString(){
		$responseStr = sprintf($this->template,$this->toUserName,$this->fromUserName,$this->createTime,$this->mediaId);
		return $responseStr;
	}
}