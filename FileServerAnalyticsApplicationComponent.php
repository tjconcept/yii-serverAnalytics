<?php

class FileServerAnalyticsApplicationComponent extends ServerAnalyticsApplicationComponent {

	public $logFile;
	
	public function init() {
		if (YII_DEBUG)
			$this->getLogFile();	// test for log file writability

		return parent::init();
	}

	protected function save( array $data = array() ) {
		return file_put_contents($this->getLogFile(), json_encode($data)."\r", FILE_APPEND|LOCK_EX);
	}

	protected function getLogFile() {
		if ($this->logFile === null)
			$this->logFile = $this->app->runtimePath.DIRECTORY_SEPARATOR.'yii_sa_logs'.DIRECTORY_SEPARATOR.date('Y_m_d').'.json';
		elseif (is_callable($this->logFile))
			$this->logFile = $this->logFile();

		$dir = dirname($this->logFile);
		if ((!is_dir($dir) && !mkdir($dir, 0777, true)) || (!is_writable($dir) && !is_writable($this->logFile)))
			throw new CException('Unable to write to server analytics log file: "'.$this->logFile.'". Make sure the directory containing the file exists and is writable or the log file exists and is writable.');

		return $this->logFile;
	}

}

?>