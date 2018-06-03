<?php
use \zhangv\wechat\MessageListener;
use \zhangv\wechat\MessageHandler;

class MessageListenerTest extends \PHPUnit\Framework\TestCase {
	/** @var MessageListener */
	private $messageListener = null;
	private $token = 'token';

	public function setUp(){
		$this->messageListener = new MessageListener($this->token,'appid','aeskey');
		$this->messageListener->addMessageHandler(new TestMessageHandler());
	}
	/** @test */
	function start(){
		$postStr = "
		<xml>
			<ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[FromUser]]></FromUserName>
			<FriendUserName><![CDATA[FriendUser]]></FriendUserName>
			<CreateTime>123456789</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[user_get_card]]></Event>
			<CardId><![CDATA[cardid]]></CardId>
			<IsGiveByFriend>1</IsGiveByFriend>
			<UserCardCode><![CDATA[12312312]]></UserCardCode>
			<OuterId>1</OuterId>
		</xml>
		";
		$params = [
			'timestamp' => 1,'nonce' => 2, 'token' => 't'
		];
		$tmpArr = array($this->token, $params['timestamp'], $params['nonce']);
		sort($tmpArr,SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$sign = sha1( $tmpStr );
		$params['signature'] = $sign;
		$r = $this->messageListener->start($postStr,$params,false);
		$this->assertEquals('event',$r);
	}
}

class TestMessageHandler extends MessageHandler{
	public function onMessage($messageObj){
		return $messageObj->MsgType;
	}
}