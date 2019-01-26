<?php
namespace zhangv\wechat\messagelistener;

abstract class MessageHandler{
	function onMessage($messageObj){}
}