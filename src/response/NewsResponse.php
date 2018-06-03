<?php
namespace wechatclient\response;
/**
 * 回复图文消息
 */
class NewsResponse extends WechatResponse{
	private $template = "
		<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<Content><![CDATA[]]></Content>
			%s
			<FuncFlag>0<FuncFlag>
		</xml>";
	private $itemTemplate = "
		 <item>
		 <Title><![CDATA[%s]]></Title>
		 <Description><![CDATA[%s]]></Description>
		 <PicUrl><![CDATA[%s]]></PicUrl>
		 <Url><![CDATA[%s]]></Url>
		 </item>
	";
	private $items = array();
	public function __construct($toUserName,$fromUserName,$items){
		$this->toUserName = $toUserName;
		$this->fromUserName = $fromUserName;
		$this->createTime = time();
		$this->items = $items;
	}
	public function __toString(){
		$itemCount = count($this->items);
		$str = "<ArticleCount>$itemCount</ArticleCount><Articles>";
		foreach($this->items as $item){
			$itemStr = sprintf($this->itemTemplate,$item->title,$item->description,$item->picUrl,$item->url);
			$str .= $itemStr;
		}
		$str .= "</Articles>";
		$responseStr = sprintf($this->template,$this->toUserName,$this->fromUserName,$this->createTime,$str);
		return $responseStr;
	}
}