<?php
namespace zhangv\wechat\messagelistener\message;
class Message{
	protected $array = [];
	public $type = null;
	public $fromUserName,$toUserName,$createTime,$msgId;

	public function __construct($type,$obj){
	}

}