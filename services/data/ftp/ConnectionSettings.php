<?php
namespace services\data\ftp;

class ConnectionSettings
{

	/**
	 *
	 * @var string $host The FTP host 
	 */
	private $host = '';

	/**
	 *
	 * @var int $port The FTP port number
	 */
	private $port = 21;

	/**
	 *
	 * @var string $username The FTP Username 
	 */
	private $username = '';

	/**
	 *
	 * @var string $password The FTP user's password 
	 */
	private $password = '';

	/**
	 *
	 * @var string $defaultFolder The folder to change to upon successful connection and login
	 */
	private $defaultFolder = '';

	/**
	 *
	 * @var int $timeout FTP timeout limit, in seconds
	 */
	private $timeout = 90;

	/**
	 * 
	 * @param mixed $options an array or object: key value pairs to match properties. matching properies will be set
	 */
	public function __construct($options = false)
	{
		if ($options) {
			$r = new \ReflectionObject($this);
			$properties = $r->getProperties();
			foreach ($properties as $property) {

				if (is_object($options)) {
					if (isset($options->{$property->name})) {
						$this->{"set" . $property->name}($options->{$property->name});
					}
				}
				if (is_array($options)) {
					if (isset($options[$property->name])) {
						$this->{"set" . $property->name}($options[$property->name]);
					}
				}
			}
		}
	}

	/**
	 * @desc Get all set settings
	 * @return array
	 */
	public function getSettings()
	{
		$r = new ReflectionObject($this);
		$properties = $r->getProperties();

		$data = [];

		foreach ($properties as $property) {
			$data[$property->name] = $this->{"get" . $property->name}();
		}

		return $data;
	}

	/**
	 * @desc Returns the host property
	 * @return string
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * @desc Returns the port property
	 * @return int
	 */
	public function getPort()
	{
		return (int) $this->port;
	}

	/**
	 * @desc Returns the username property
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @desc Returns the Password property
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @desc Returns the DefaultFolder property
	 * @return string
	 */
	public function getDefaultFolder()
	{
		return $this->defaultFolder;
	}

	/**
	 * @desc Returns the Timeout property
	 * @return int
	 */
	public function getTimeout()
	{
		return (int) $this->timeout;
	}

	/**
	 * 
	 * @param string $host
	 * @see $host
	 * @return \FTP_ConnectionSettings
	 */
	public function setHost($host)
	{
		$this->host = $host;
		return $this;
	}

	/**
	 * 
	 * @param int $port
	 * @see $port
	 * @return \FTP_ConnectionSettings
	 */
	public function setPort($port)
	{
		$port = (int) $port;

		if ($port > 0) {
			$this->port = $port;
		}

		return $this;
	}

	/**
	 * 
	 * @param string $username
	 * @see $username
	 * @return \FTP_ConnectionSettings
	 */
	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

	/**
	 * 
	 * @param string $password
	 * @see $password
	 * @return \FTP_ConnectionSettings
	 */
	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}

	/**
	 * 
	 * @param string $defaultFolder
	 * @see $defaultFolder
	 * @return \FTP_ConnectionSettings
	 */
	public function setDefaultFolder($defaultFolder)
	{
		$this->defaultFolder = $defaultFolder;
		return $this;
	}

	/**
	 * 
	 * @param int $timeout
	 * @see $timeout
	 * @return \FTP_ConnectionSettings
	 */
	public function setTimeout($timeout)
	{
		$timeout = (int) $timeout;

		if ($timeout > 0) {
			$this->timeout = $timeout;
		}

		return $this;
	}

}
