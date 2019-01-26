<?php
namespace zhangv\wechat\messagelistener\response;
/**
 * 回复文本消息
 */
class Text extends Response {
	private $template = "
		<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>0<FuncFlag>
		</xml>";
	public $content;
	public function __construct($toUserName,$fromUserName,$content){
		$this->toUserName = $toUserName;
		$this->fromUserName = $fromUserName;
		$this->createTime = time();
		$this->content = $content;
	}
	public function __toString(){
		$responseStr = sprintf($this->template,$this->toUserName,$this->fromUserName,$this->createTime,$this->content);
		return $responseStr;
	}
}