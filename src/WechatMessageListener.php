<?php
/**
 * @author ZhangV
 * @copyright Copyright (c) 2012
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * ref: 消息加解密:https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419318479&token=&lang=zh_CN
 * TODO: 暂未支持小程序json格式消息
 */
namespace zhangv\wechat\messagelistener;
use \Exception;
use Monolog\Logger;
use Psr\Log\LogLevel;

require_once __DIR__ . "/wxcrypt/wxBizMsgCrypt.php";

class WechatMessageListener{
	/**
	 * Token
	 * @var string
	 */
	private $token = null;
	/**
	 * app ID
	 * @var string
	 */
	private $appId = null;
	/**
	 * Encoding AES key
	 * @var string
	 */
	private $encodingAesKey = null;
	/** @var Logger */
	private $logger = null;
	private $messageHandlers = [];
	private $eventMessageHandlers = [];
	private $requestParameters = [];
	public function __construct($token = null,$appId = null,$encodingAesKey = null){
		$this->token = $token;
		$this->appId = $appId;
		$this->encodingAesKey = $encodingAesKey;
	}

	public function setLogger(Logger $logger){
		$this->logger = $logger;
	}

	private function log($msg,$level = LogLevel::ERROR){
		if($this->logger) $this->logger->log($level,$msg);
		else {
			if($level == LogLevel::ERROR) error_log($msg);
		}
	}

	/**
	 * @param $postStr
	 * @param $requestParams
	 * @param bool $print
	 * @return
	 * @throws Exception
	 */
	public function start($postStr,$requestParams,$print = true){
		$this->requestParameters = $requestParams;
		if($this->isValidatingRequest($requestParams)){ //验证微信接口
			echo $requestParams['echostr'];
			return;
		}
		if(!$this->checkSignature($requestParams)) throw new Exception('Signature checking failed');
		if (!empty($postStr)) {
			list($messageObj,$encrypted,$format) = $this->parse($postStr);

			if ($messageObj && !empty($messageObj->MsgType)) {
				$response = null;
				$msgType = (string)$messageObj->MsgType;
				if(isset($this->messageHandlers[$msgType])) {
					$handler = $this->messageHandlers[$msgType];
					$response = $handler->onMessage($messageObj);
				}
				if($msgType === 'event'){
					$event = (string)$messageObj->Event;
					if(isset($this->eventMessageHandlers[$event])){
						$handler = $this->eventMessageHandlers[$event];
						$response = $handler->onMessage($messageObj);
					}
				}

				if ($response) {
					if ($print === true) {
						if($encrypted == true){
							$respxml = (string)$response;
							$errcode = $this->encryptResponse($respxml,$encrypted);
							if($errcode == 0){
								//echo $encrypted; 加密消息无法正常返回给用户，不确定是什么问题
								echo $response;
							}else{
								$this->log('encrypt error,code='.$errcode);
								return;
							}
						}else echo $response;
					} else return $response;
				}else{
					$this->log("No message handler is set for message type of [{$messageObj->MsgType}], ignore the message: {$postStr}",LogLevel::DEBUG);
				}
			}
		}
	}


	public function parse($raw){
		$obj = null;
		$format = null;
		$encrypted = false;
		libxml_use_internal_errors(true);
		try{
			$obj = new \SimpleXMLElement($raw,LIBXML_NOCDATA);
			$format = 'xml';
		}catch (Exception $e){
			$obj = json_decode($raw);
			$format = 'json';
			if(is_null(json_decode($raw))) {
				throw new Exception('The format is neither xml or json');
			}
		}

		$decryptedMsg = $obj;
		if($obj && !empty($obj->Encrypt)) {
			$errcode = $this->decryptMessage($raw,$decryptedMsg);
			if($errcode == 0) {
				var_dump($decryptedMsg);
				$encrypted = true;
				if(is_null(json_decode($decryptedMsg))) {
					$obj = new \SimpleXMLElement($decryptedMsg,  LIBXML_NOCDATA);
				}else
					$obj = json_decode($decryptedMsg);
			}else{
				throw new Exception('decrypt error,code='.$errcode);
			}
		}
		var_dump($obj);
		return [$obj,$encrypted,$format];
	}

	public function encryptResponse($repxml,&$encrypted){
		$pc = new \WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);
		$timestamp = time();
		$nonce = $this->getRandomStr();
		$errCode = $pc->encryptMsg($repxml, $timestamp, $nonce, $encrypted);
		return $errCode;
	}

	function getRandomStr() {
		$str = "";
		$str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($str_pol) - 1;
		for ($i = 0; $i < 16; $i++) {
			$str .= $str_pol[mt_rand(0, $max)];
		}
		return $str;
	}

	public function decryptMessage($reqxml,&$decrypted){
		$msgSignature = empty($this->requestParameters["msg_signature"])?'':$this->requestParameters["msg_signature"];
		$timestamp = empty($this->requestParameters["timestamp"])?'':$this->requestParameters["timestamp"];
		$nonce = empty($this->requestParameters["nonce"])?'':$this->requestParameters["nonce"];
		$encrytType = empty($this->requestParameters["encrypt_type"])?'':$this->requestParameters["encrypt_type"];
		if($encrytType == 'aes'){
			$pc = new \WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);
			$errCode = $pc->decryptMsg($msgSignature, $timestamp, $nonce, $reqxml, $decrypted);
			return $errCode;
		}else{
			$decrypted = $reqxml;
			return 0;
		}
	}

	/**
	 * 检查签名信息
	 */
	public function checkSignature($params){
		$signature = empty($params["signature"])?'':$params["signature"];
		$timestamp = empty($params["timestamp"])?'':$params["timestamp"];
		$nonce = empty($params["nonce"])?'':$params["nonce"];
		$tmpArr = array($this->token, $timestamp, $nonce);
		sort($tmpArr,SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

	private function isValidatingRequest($requestParams){
		return isset($requestParams['echostr']);
	}

	public function addMessageHandler($msgType,MessageHandler $handler){
		$this->messageHandlers[$msgType] = $handler;
	}

	public function addEventMessageHandler($eventType,MessageHandler $handler){
		$this->eventMessageHandlers[$eventType] = $handler;
	}
}