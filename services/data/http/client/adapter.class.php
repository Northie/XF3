<?php

namespace services\data\http\client;

class adapter extends \services\data\adapter
{

	private $client;
	private $options = [
		\CURLOPT_RETURNTRANSFER => true
	];

	private $payloadMode = 0;
	private $responseHeaders = [];
	public static $data = [];
	public static $requestHeaders = [];

	static $initHeaders = [];

	const PAYLOAD_URL_ENCODED = 0;
	const PAYLOAD_JSON_ENCODED = 1;
	const PAYLOAD_XML = 2;

	public function __construct($payloadMode = 0)
	{
		$this->client = curl_init();
		$this->payloadMode = $payloadMode;

		curl_setopt($this->client, \CURLOPT_SSL_VERIFYPEER, false);
	}

	public function setOption($option,$value) {
		$this->options[$option] = $value;
		return $this;
	}

	public static function encodePayload($payloadMode, $data, $client = null)
	{
		self::$data['app'] = $data;
		$return = null;

		switch ($payloadMode) {
			case self::PAYLOAD_URL_ENCODED:
				$return = http_build_query($data);
				break;
			case self::PAYLOAD_JSON_ENCODED:
				$return = json_encode($data, JSON_PRETTY_PRINT);

				self::$initHeaders = [
					'Content-Type: application/json'
				];

				curl_setopt($client, \CURLOPT_HTTPHEADER, self::$initHeaders);
				break;
			case self::PAYLOAD_XML:
				\utils\Tools::array2xml($data, $return);
			default:
				$return = '';
		}
		self::$data['sent'] = $return;
		return $return;
	}

	public static function normaliseHeaders($headers)
	{
		self::$requestHeaders['app'] = $headers;
		if (\utils\validators::is_assoc($headers)) {
			$return = [];
			foreach ($headers as $key => $val) {
				$return[] = implode(": ", [$key, $val]);
			}
		} else {
			$return = $headers;
		}
		self::$requestHeaders['sent'] = $return;
		return $return;
	}


	public function post($url, $data = false, $headers = false)
	{

		curl_setopt($this->client, \CURLOPT_URL, $url);

		if (is_array($data)) {
			$query = self::encodePayload($this->payloadMode, $data, $this->client);
			curl_setopt($this->client, \CURLOPT_POST, count($data));
			curl_setopt($this->client, \CURLOPT_POSTFIELDS, $query);
		}

		if (is_array($headers)) {
			$headers = self::normaliseHeaders($headers);
			$headers = array_merge(static::$initHeaders, $headers);
			curl_setopt($this->client, \CURLOPT_HTTPHEADER, $headers);
		}

		curl_setopt($this->client, \CURLOPT_HEADERFUNCTION, function($curl, $header)
		{	
			$len = strlen($header);
			$header = explode(':', $header, 2);
			if (count($header) < 2) // ignore invalid headers
				return $len;
			
			$this->responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);

			return $len;
		});


		foreach ($this->options as $opt => $value) {
			curl_setopt($this->client, $opt, $value);
		}

		$exec = curl_exec($this->client);
		\Plugins\EventManager::Load()->ObserveEvent('http_curl_exec',$this);
		return $exec;
		
	}

	public function get($url, $data = false, $headers = false)
	{

		if (is_array($data)) {
			$query = http_build_query($data);
			if (strpos($url, '?') > -1) {
				$url .= "&" . $query;
			} else {
				$url .= "?" . $query;
			}
		}

		$this->data['app'] = $data ? $data : $url;
		$this->data['sent'] = $query ? $query : $url;

		curl_setopt($this->client, \CURLOPT_URL, $url);
		curl_setopt($this->client, \CURLOPT_HTTPGET, true);

		if (is_array($headers)) {
			$headers = self::normaliseHeaders($headers);
			$headers = array_merge(static::$initHeaders, $headers);
			curl_setopt($this->client, \CURLOPT_HTTPHEADER, $headers);
		}

		curl_setopt($this->client, \CURLOPT_HEADERFUNCTION, function($curl, $header)
		{	
			$len = strlen($header);
			$header = explode(':', $header, 2);
			if (count($header) < 2) // ignore invalid headers
				return $len;
			
			$this->responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);

			return $len;
		});

		foreach ($this->options as $opt => $value) {
			curl_setopt($this->client, $opt, $value);
		}

		$exec = curl_exec($this->client);
		\Plugins\EventManager::Load()->ObserveEvent('http_curl_exec',$this);
		return $exec;
	}

	public function put($url, $data = false, $headers = false)
	{
		curl_setopt($this->client, \CURLOPT_URL, $url);

		if (is_array($data)) {
			$query = self::encodePayload($this->payloadMode, $data, $this->client);
			curl_setopt($this->client, \CURLOPT_POST, count($data));
			curl_setopt($this->client, \CURLOPT_POSTFIELDS, $query);
		}

		if (is_array($headers)) {
			$headers = self::normaliseHeaders($headers);
			$headers = array_merge(static::$initHeaders, $headers);
			curl_setopt($this->client, \CURLOPT_HTTPHEADER, $headers);
		}

		curl_setopt($this->client, \CURLOPT_HEADERFUNCTION, function($curl, $header)
		{	
			$len = strlen($header);
			$header = explode(':', $header, 2);
			if (count($header) < 2) // ignore invalid headers
				return $len;
			
			$this->responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);

			return $len;
		});

		foreach ($this->options as $opt => $value) {
			curl_setopt($this->client, $opt, $value);
		}

		curl_setopt($this->client, \CURLOPT_HTTP_VERSION, \CURL_HTTP_VERSION_1_1);
		curl_setopt($this->client, \CURLOPT_CUSTOMREQUEST, "PUT");
		$exec = curl_exec($this->client);
		\Plugins\EventManager::Load()->ObserveEvent('http_curl_exec',$this);
		return $exec;
	}

	public function delete($url, $data = false, $headers = false)
	{

		curl_setopt($this->client, \CURLOPT_URL, $url);
		curl_setopt($this->client, \CURLOPT_CUSTOMREQUEST, "DELETE");

		if (is_array($data)) {
			$query = self::encodePayload($this->payloadMode, $data);

			curl_setopt($this->client, \CURLOPT_POST, count($data));
			curl_setopt($this->client, \CURLOPT_POSTFIELDS, $query);
		}

		if (is_array($headers)) {
			$headers = self::normaliseHeaders($headers);
			$headers = array_merge(static::$initHeaders, $headers);
			curl_setopt($this->client, \CURLOPT_HTTPHEADER, $headers);
		}

		curl_setopt($this->client, \CURLOPT_HEADERFUNCTION, function($curl, $header)
		{	
			$len = strlen($header);
			$header = explode(':', $header, 2);
			if (count($header) < 2) // ignore invalid headers
				return $len;
			
			$this->responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);

			return $len;
		});		

		foreach ($this->options as $opt => $value) {
			curl_setopt($this->client, $opt, $value);
		}

		$exec = curl_exec($this->client);
		\Plugins\EventManager::Load()->ObserveEvent('http_curl_exec',$this);
		return $exec;
	}

	//map required fucntions to http verb methods

	public function create($data, $id = false)
	{
		return call_user_func_array([$this, 'post'], func_get_args());
	}

	public function read($data)
	{
		return call_user_func_array([$this, 'get'], func_get_args());
	}

	public function update($data, $conditions = false)
	{
		return call_user_func_array([$this, 'put'], func_get_args());
	}

	/*
	  public function delete() {	//already defined - common name

	  }
	 */

	public function query($query, $parameters = false)
	{ }

	public function getAdapter()
	{
		return $this->client;
	}

	public function getResponseHeaders()
	{
		return $this->responseHeaders;
	}

	public function getHttpCode() {
		return curl_getinfo($this->client,CURLINFO_RESPONSE_CODE);
	}

	public function getIfo(int $curlInfoOpt) {
		return curl_getinfo($this->client,$curlInfoOpt);
	}

	public function getOptions() {
		return $this->options;
	}

	public function getData() {
		return self::$data;
	}

	public function getRequestHeaders() {
		return self::$requestHeaders;
	}

	public function __destruct()
	{
		curl_close($this->client);
	}
}
