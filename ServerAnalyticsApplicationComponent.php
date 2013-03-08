<?php

abstract class ServerAnalyticsApplicationComponent extends CApplicationComponent {

	public $afterworksID = 'afterworks';
	
	public function init() {
		if (($afterworks = Yii::app()->getComponent($this->afterworksID)))
			$afterworks->addJob(array($this, 'log'));
		else
			Yii::app()->attachEventHandler('onEndRequest', array($this, 'log'));
		
		return parent::init();
	}

	public function log() {
		$app = Yii::app();
		return $this->save(array(
			'uri' => $app->request->requestUri,
			'route' => $app->urlManager->parseUrl($app->request),
			'requestType' => $app->request->requestType,
			'realMemoryPeakUsage' => memory_get_peak_usage(true),
			'memoryPeakUsage' => memory_get_peak_usage(),
			'executionTime' => Yii::getLogger()->executionTime,
			'timestamp' => time()
		));
	}

	abstract protected function save( array $data = array() );

}

?>