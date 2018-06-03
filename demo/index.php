<?php
/**
 * 入口
 */
require_once 'autoload.php';
use zhangv\wechat\MessageHandler;
use zhangv\wechat\MessageListener;
class DemoMessageHandler extends MessageHandler{
	public function onMessage($messageObj){
		return $messageObj->MsgType;
	}
}
$listener = new MessageListener();
$listener->addMessageHandler(new DemoMessageHandler());
$listener->start($GLOBALS["HTTP_RAW_POST_DATA"],$_REQUEST);