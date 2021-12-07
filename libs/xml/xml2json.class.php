<?php

namespace libs\xml;

/**
 * $p = new xml2json;
 * $json = $p->setXml($str)->convert()->getJson()
 */

class xml2json {
	
	private $xml = false;
	private $json = '{}';
	
	public function __construct($xml=false) {
		$this->xml = $xml;
	}
	
	public function getFromURL($url) {
		$this->xml = file_get_contents($url);
		return $this;
	}
	
	public function setXml($xml,$url=false) {
		if($url) {
			$this->getFromURL($xml);
		} else {
			$this->xml = $xml;
		}
		return $this;
	}
	
	public function convert() {
		if($this->xml) {
			$doc = simplexml_load_string($this->xml);
			$this->json = json_encode($doc);
		}
		return $this;
	}
	
	public function getJSON() {
		return $this->json;
	}
}
