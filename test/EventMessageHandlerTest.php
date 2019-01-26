<?php
use \zhangv\wechat\messagelistener\WechatMessageListener;
use \zhangv\wechat\messagelistener\MessageHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class EventMessageHandlerTest extends \PHPUnit\Framework\TestCase {
	/** @var WechatMessageListener */
	private $messageListener = null;
	private $token = 'token';

	public function setUp(){
		$this->messageListener = new WechatMessageListener($this->token,'appid','aeskey');
		$this->messageListener->addEventMessageHandler('user_get_card',new TestMessageHandler());
		$this->logger = new Logger(get_class($this) .'logger');
		$this->logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
		$this->messageListener->setLogger($this->logger);
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
		$this->assertEquals('user_get_card',$r->Event);
		$this->assertEquals('cardid',$r->CardId);
	}
}

class TestMessageHandler extends MessageHandler{
	public function onMessage($messageObj){
		return $messageObj;
	}
}