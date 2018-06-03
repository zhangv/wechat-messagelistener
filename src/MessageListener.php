<?php
/**
 * @author ZhangV
 * @copyright Copyright (c) 2012
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * ref: 消息加解密:https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419318479&token=&lang=zh_CN
 * TODO: 暂未支持小程序json格式消息
 */
namespace zhangv\wechat;
use \Exception;
require_once __DIR__ . "/wxcrypt/wxBizMsgCrypt.php";

class MessageListener{
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
	private $logger = null;
	private $messageHandlers = [];
	private $eventMessageHandlers = [];

	public function __construct($token = null,$appId = null,$encodingAesKey = null){
		$this->token = $token;
		$this->appId = $appId;
		$this->encodingAesKey = $encodingAesKey;
	}

	public function setLogger($logger){
		$this->logger = $logger;
	}

	private function log($msg){
		if($this->logger) $this->logger->info($msg);
		else error_log($msg);
	}

	public function start($postStr,$requestParams,$print = true){
		if($this->isValidRequest()){ //验证微信接口
			echo $_GET['echostr'];
			return;
		}
		if(!$this->checkSignature($requestParams)) throw new Exception('check signature fail');

		if (!empty($postStr)) {
			list($messageObj,$encrypted,$format) = $this->parse($postStr);
			if ($messageObj && !empty($messageObj->MsgType)) {
				$response = null;

				foreach($this->messageHandlers as $handler) {
					$response = $handler->onMessage($messageObj);
					$response = (string)$response;
				}
				if ($response) {
					if ($print === true) {
						if($encrypted == true){
							$errcode = $this->encryptResponse($response,$encrypted);
							if($errcode == 0){
								echo $encrypted;
							}else{
								$this->log('encrypt error,code='.$errcode);
								return;
							}
						}else
							echo $response;
					}
					else return $response;
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
			if (empty($obj->MsgType) || ($obj->MsgType == 'event' && $obj->Event == 'View')){//view event won't be encrypted
				return;
			}
			$errcode = $this->decryptRequest($raw,$obj);
			if($errcode == 0) {
				$encrypted = true;
				if(is_null(json_decode($decryptedMsg))) {
					$obj = new \SimpleXMLElement($decryptedMsg,  LIBXML_NOCDATA);
				}else
					$obj = json_decode($decryptedMsg);
			}else{
				throw new Exception('decrypt error,code='.$errcode);
			}
		}
		return [$obj,$encrypted,$format];
	}

	public function encryptResponse($repxml,&$encrypted){
		$pc = new WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);
		$timestamp = time();
		$nonce = 'aabbccs';
		$errCode = $pc->encryptMsg($repxml, $timestamp, $nonce, $encrypted);
		return $errCode;
	}

	public function decryptRequest($reqxml,&$decrypted){
		$msgSignature = empty($_REQUEST["msg_signature"])?'':$_REQUEST["msg_signature"];
		$timestamp = empty($_REQUEST["timestamp"])?'':$_REQUEST["timestamp"];
		$nonce = empty($_REQUEST["nonce"])?'':$_REQUEST["nonce"];
		$encrytType = empty($_REQUEST["encrypt_type"])?'':$_REQUEST["encrypt_type"];
		if($encrytType == 'aes'){
			$pc = new WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);
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

	private function isValidRequest(){
		return isset($_GET['echostr']);
	}

	public function addMessageHandler($msgType,MessageHandler $handler){
		$this->messageHandlers[$msgType] = $handler;
	}

	public function addEventMessageHandler($eventType,MessageHandler $handler){
		$this->eventMessageHandlers[$eventType] = $handler;
	}
}