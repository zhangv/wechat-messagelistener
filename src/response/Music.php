<?php
namespace zhangv\wechat\messagelistener\response;
/**
 * 回复音乐消息
 */
class Music extends Response{
	private $template = "
		<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[music]]></MsgType>
			<Music>
				<Title><![CDATA[%s]]></Title>
				<Description><![CDATA[%s]]></Description>
				<MusicUrl><![CDATA[%s]]></MusicUrl>
				<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
				<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
			</Music>
		</xml>";
	public $title,$description,$musicUrl,$hqMusicUrl,$thumbMediaId;
	public function __construct($toUserName,$fromUserName,$title,$description,$musicUrl,$hqMusicUrl,$thumbMediaId){
		$this->toUserName = $toUserName;
		$this->fromUserName = $fromUserName;
		$this->createTime = time();
		$this->title = $title;
		$this->description = $description;
		$this->musicUrl = $musicUrl;
		$this->hqMusicUrl = $hqMusicUrl;
		$this->thumbMediaId = $thumbMediaId;
	}
	public function __toString(){
		$responseStr = sprintf($this->template,$this->toUserName,$this->fromUserName,$this->createTime,$this->title,$this->description,$this->musicUrl,$this->hqMusicUrl,$this->thumbMediaId);
		return $responseStr;
	}
}