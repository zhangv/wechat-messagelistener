<?php
require_once 'autoload.php';
use zhangv\wechat\messagelistener\MessageHandler;
use zhangv\wechat\messagelistener\WechatMessageListener;
class DemoMessageHandler extends MessageHandler{
	public function onMessage($messageObj){
		return $messageObj->MsgType;
	}
}
$listener = new WechatMessageListener();
$listener->addMessageHandler('text',new DemoMessageHandler());
$listener->start($GLOBALS["HTTP_RAW_POST_DATA"],$_REQUEST);