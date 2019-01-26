<?php

require_once __DIR__ .'/../demo/autoload.php';

use \zhangv\wechat\messagelistener\WechatMessageListener;
use \zhangv\wechat\messagelistener\MessageHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class WechatMessageListenerTest extends \PHPUnit\Framework\TestCase {
	/** @var WechatMessageListener */
	private $messageListener = null;
	private $token = 'token';
	private $appid = 'appid';
	private $aeskey = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG';

	public function setUp(){
		$this->messageListener = new WechatMessageListener($this->token,$this->appid,$this->aeskey);
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
			'timestamp' => 1,'nonce' => 2
		];
		$tmpArr = array($this->token, $params['timestamp'], $params['nonce']);
		sort($tmpArr,SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$sign = sha1( $tmpStr );
		$params['signature'] = $sign;
		$this->messageListener->addMessageHandler('event',new TestMessageHandler());
		$r = $this->messageListener->start($postStr,$params,false);
		$this->assertEquals('event',$r);
	}

	/**
	 * @test
	 * @group tmp
	 * /weixin/
	 * ?signature=5f715f039e4e65ab4333d5088d0d3a8e2fd7b81d
	 * &timestamp=1528651479
	 * &nonce=2048797141
	 * &openid=oadCGjhAzavyOgipBVm2NajSkSeY
	 * &encrypt_type=aes
	 * &msg_signature=a7ea5171baf6470f07aef909d8955f6f150db97d
	 * //测试数据不完全 - requestParam
	 */
	function encryptMode(){//安全模式
		$text = "<xml><ToUserName><![CDATA[oia2Tj我是中文jewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";
		$timeStamp = "1409304348";
		$nonce = "xxxxxx";
		$pc = new WXBizMsgCrypt($this->token, $this->aeskey, $this->appid);
		$encryptMsg = '';
		$errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $encryptMsg);

		$xml_tree = new DOMDocument();
		$xml_tree->loadXML($encryptMsg);
		$array_e = $xml_tree->getElementsByTagName('Encrypt');
		$array_s = $xml_tree->getElementsByTagName('MsgSignature');
		$encrypt = $array_e->item(0)->nodeValue;
		$msg_sign = $array_s->item(0)->nodeValue;

		$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
		$from_xml = sprintf($format, $encrypt);

		$params = [
			'timestamp' => $timeStamp,'nonce' => $nonce
		];
		$tmpArr = array($this->token, $params['timestamp'], $params['nonce']);
		sort($tmpArr,SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$sign = sha1( $tmpStr );
		$params['signature'] = $sign;
		$this->messageListener->addMessageHandler('video',new TestMessageHandler());
		$r = $this->messageListener->start($from_xml,$params,false);
		var_dump($r);

	}


}

class TestMessageHandler extends MessageHandler{
	public function onMessage($messageObj){
		return $messageObj->MsgType;
	}
}