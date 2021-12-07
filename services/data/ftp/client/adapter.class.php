<?php

namespace services\data\ftp\client;

class adapter
{

	/**
	 *
	 * @var FTP_ConnectionSettings $settings
	 * @desc an instance of FTP_ConnectionSettings containing the FTP credentials
	 */
	private $settings;

	/**
	 *
	 * @var resource $connection
	 * @desc Hold the connection resource for use
	 */
	private $connection;

	/**
	 * 
	 * @param FTP_ConnectionSettings $settings
	 * @throws Exception
	 * @desc Connection and login on instance creation, throw \exceptions on errors
	 */
	public function __construct(\services\data\ftp\ConnectionSettings $settings)
	{
		if (trim($settings->getHost()) == '') {
			throw new \Exception("FTP Host cannot be empty");
		}

		$this->connection = ftp_ssl_connect($settings->getHost(), $settings->getPort(), $settings->getTimeout());

		if ($this->connection) {
			$login = ftp_login($this->connection, $settings->getUsername(), $settings->getPassword());
			if ($login) {
				$this->settings = $settings;
				if (trim($this->settings->getDefaultFolder())) {
					$this->changeDir($this->settings->getDefaultFolder());
				}
			} else {
				throw new \Exception("Connected to FTP Host, but could not log in with supplied credentials");
			}
		} else {
			throw new \Exception("Cannot connect to FTP Host");
		}
	}

	/**
	 * @desc close connection if open
	 */
	public function __destruct()
	{
		if ($this->connection) {
			ftp_close($this->connection);
		}
	}

	/**
	 * 
	 * @param string $directory
	 * @return boolean true on success
	 * @throws Exception on error
	 */
	public function changeDir($directory)
	{
		if (!@ftp_chdir($this->connection, $directory)) {
			throw new \Exception("Cannot change to " . $directory);
		}
		return true;
	}

	public function makePassive(){
		if( $this->connection ) {
			ftp_pasv( $this->connection, TRUE );
		}
	}

	/**
	 * 
	 * @param string $directory The directory to read from
	 * @return array of items
	 * @desc either changes directory or gets current directory
	 */
	public function readDir($directory = false)
	{
		if ($directory) {
			$this->changeDir($directory);
		} else {
			$directory = ftp_pwd($this->connection);
		}

		return ftp_nlist($this->connection, $directory);
	}

	/**
	 * 
	 * @param string $directory the remote directory to create
	 * @return boolean true on success
	 * @throws Exception on error
	 */
	public function makeDir($directory)
	{
		if (!ftp_mkdir($this->connection, $directory)) {
			throw new \Exception("Cannot make remote directory, " . $directory);
		}
		return true;
	}

	/**
	 * 
	 * @param string $localFileName path to local file to upload
	 * @param string $remoteFileName the name to give the file on upload
	 * @param int $mode File transfer mode (FTP_ASCII (1) or FTP_BINARY (2))
	 * @param boolean $validate - true to validate upload
	 * @return boolean true on success
	 * @throws Exception on error
	 */
	public function uploadFile($localFileName, $remoteFileName, $mode = FTP_ASCII, $validate = false)
	{

		$remotePath = explode("/", $remoteFileName);

		switch (true) {
			case(count($remotePath) == 0) :
				throw new \Exception("Remote File Name mist be set");
				break;
			case(count($remotePath) == 1) :
				$directory = ftp_pwd($this->connection);
				$remoteFileName = implode("/", [$directory, $remoteFileName]);
		}

		$fp = fopen($localFileName, "r");

		if ($fp) {
			if (ftp_fput($this->connection, $remoteFileName, $fp, $mode)) {
				fclose($fp);
				if ($validate) {
					return $this->validateUpload($localFileName, $remoteFileName);
				}
				return true;
			} else {
				fclose($fp);
				throw new \Exception("could not upload file");
			}
		} else {
			throw new \Exception("could not open local file for reading");
		}
	}

	/**
	 * 
	 * @param string $remoteFileName name of remote file
	 * @param string $localFileName path to local file
	 * @param int $mode File transfer mode (FTP_ASCII (1) or FTP_BINARY (2))
	 * @param boolean $forceOverwrite if true over writes local file, else does not overwrite local file if it exists
	 * @return boolean true on success
	 * @throws Exception on error
	 */
	public function downloadFile($remoteFileName, $localFileName, $mode = FTP_ASCII, $forceOverwrite = false)
	{
		$remotePath = explode("/", $remoteFileName);

		switch (true) {
			case(count($remotePath) == 0) :
				throw new \Exception("Remote File Name must be set");
				break;
			case(count($remotePath) == 1) :
				$directory = ftp_pwd($this->connection);
				$remoteFileName = implode("/", [$directory, $remoteFileName]);
		}

		if ($forceOverwrite) {
			$fp = @fopen($localFileName, 'w+');
		} else {
			$fp = @fopen($localFileName, 'x');
		}

		if ($fp) {
			if (ftp_fget($this->connection, $fp, $remoteFileName, $mode)) {
				fclose($fp);
				return true;
			} else {
				fclose($fp);
				throw new \Exception("could not download file");
			}
		} else {
			throw new \Exception("could not open local file for writing");
		}
	}

	/**
	 * 
	 * @param string $localFileName path to local file that you think has been uploaded
	 * @param string $remoteFileName path to remote file where you think  the file has been uploaded to
	 * @param int $mode File transfer mode (FTP_ASCII (1) or FTP_BINARY (2))
	 * @return boolean true if file has been uploaded, false if not
	 * @desc downloads the previously uploded file to a temp location and then does an mD% checksum on both versions
	 */
	public function validateUpload($localFileName, $remoteFileName, $mode = FTP_ASCII)
	{
		$tmpFile = implode(DIRECTORY_SEPARATOR, [APPLICATION_PATH, 'data', 'tmp', uniqid() . ".ftp.check.tmp"]);

		try {
			$this->downloadFile($remoteFileName, $tmpFile);

			$checkSumLocal = md5_file($localFileName);
			$checkSumRemote = md5_file($tmpFile);

			unlink($tmpFile);

			$control = md5('');

			if ($checkSumLocal != $control && $checkSumRemote != $control) {
				if ($checkSumLocal == $checkSumRemote) {
					return true;
				}
			}

			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * 
	 * @param string $localDir path to local directory to upload
	 * @param string $remoteDir path to remote directory to upload to
	 * @param boolean $createRemoteSub if true, creates a remote directory inside $remoteDir with the name of $localDir's least signigicant part
	 * @throws Exception on error
	 * @desc interates over $localDir. Does not recurse
	 */
	public function uploadDirectory($localDir, $remoteDir, $createRemoteSub = false)
	{
		$localSystem = new DirectoryIterator($localDir);

		if ($createRemoteSub) {
			$localPath = explode(DIRECTORY_SEPARATOR, $localDir);
			$localDirName = array_pop($localPath);
			$newRemoteDir = $remoteDir . "/" . $localDirName;
			try {
				if ($this->makeDir($newRemoteDir)) {
					$remoteDir = $newRemoteDir;
				}
			} catch (Exception $e) {
				throw $e;
			}
		}

		$this->changeDir($remoteDir);

		foreach ($localSystem as $fileInfo) {
			if ($fileInfo->isDot() || $fileInfo->isDir()) {
				continue;
			}

			if ($fileInfo->isFile()) {
				if ($fileInfo->isReadable()) {
					$localFileName = $localDir . DIRECTORY_SEPARATOR . $fileInfo->getFilename();
					$remoteFileName = $remoteDir . "/" . $fileInfo->getFilename();
					try {
						$this->uploadFile($localFileName, $remoteFileName, $this->getModeForFile($localFileName), true);
					} catch (Exception $e) {
						$this->log($e->getMessage(), 'error');
					}
				} else {
					$this->log($localFileName . " is not readable, " . __METHOD__ . ", " . __LINE__ . ".", 'error');
				}
			}
		}
	}

	/**
	 * 
	 * @param string $remoteDir path to remote directory to download from
	 * @param string $localDir path to local directory to download to
	 * @param boolean $createLocalSub if true, creates a local directory inside $localDir with the name of $remoteDir's least signigicant part
	 * @throws Exception on error
	 */
	public function downloadDirectory($remoteDir, $localDir, $createLocalSub = false)
	{

		$remoteSystem = $this->readDir($remoteDir);

		if ($createLocalSub) {
			$remotePath = explode("/", $remoteDir);
			$remoteDirName = array_pop($remotePath);
			$newLocalDir = implode(DIRECTORY_SEPARATOR, [$localDir, $remoteDirName]);
			if (mkdir($newLocalDir, 0777)) {
				$localDir = $newLocalDir;
			} else {
				throw new \Exception("Cannot create " . $remoteDirName . " as a subdirectory of " . $localDir . " locally");
			}
		}

		foreach ($remoteSystem as $item) {
			$remoteFileName = $item;
			$remoteFilePath = explode("/", $remoteFileName);
			$localFileName = $localDir . DIRECTORY_SEPARATOR . array_pop($remoteFilePath);

			try {
				if ($this->changeDir($remoteFileName)) {
					//$this->log("$remoteFileName is a directory, ignoring");
					continue;
				}
			} catch (Exception $e) {
				//$this->log("$remoteFileName is a file, processing");
				;
			}

			try {
				$this->downloadFile($remoteFileName, $localFileName);
			} catch (Exception $e) {
				$this->log($e->getMessage(), 'error');
			}
		}
	}

	/**
	 * 
	 * @param string $localFileName
	 * @return int  File transfer mode (FTP_ASCII (1) or FTP_BINARY (2))
	 * @desc helper to test for file type
	 */
	public function getModeForFile($localFileName)
	{
		$finfo = finfo_open(FILEINFO_MIME);
		if (substr(finfo_file($finfo, $localFileName), 0, 4) == 'text') {
			return FTP_ASCII;
		} else {
			return FTP_BINARY;
		}
	}

	/**
	 * 
	 * @param string $message log message
	 * @param string $type lof type
	 * @desc local log method
	 */
	private function log($message, $type = 'info') { 
		\utils\debug::printComment([$type=>$message]);
	}

}
