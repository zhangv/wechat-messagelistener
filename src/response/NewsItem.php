<?php

namespace zhangv\wechat\messagelistener\response;
class NewsItem {
	public $title,$description,$picUrl,$url;
	public function __construct($title,$description,$picUrl,$url){
		$this->title = $title;
		$this->description = $description;
		$this->picUrl = $picUrl;
		$this->url = $url;
	}
}