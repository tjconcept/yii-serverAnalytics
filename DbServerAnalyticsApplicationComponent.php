<?php

Yii::setPathOfAlias('ServerAnalytics', __DIR__);
Yii::import('ServerAnalytics.ServerAnalyticsApplicationComponent');

class DbServerAnalyticsApplicationComponent extends ServerAnalyticsApplicationComponent {

	/**
	 * @var string the ID of a {@link CDbConnection} application component.
	 */
	public $connectionID = 'db';

	/**
	 * @var string the name of the DB table to store analytics data.
	 */
	public $tableName = 'YiiSa_logs';

	public function init() {
		if (YII_DEBUG && !(Yii::app()->getComponent($this->connectionID) instanceof CDbConnection))
			throw new CException('The component id "'.$this->connectionID.'" is not an instance of CDbConnection.');

		return parent::init();
	}

	protected function save( array $data = array() ) {
		$db = Yii::app()->getComponent($this->connectionID);
		try {
			$this->insertData($db, $data);
		} catch (Exception $e) {
			try {
				$this->createTable($db);
				$this->insertData($db, $data);
			} catch(Exception $e) {
				return false;
			}
			return false;
		}
		return true;
	}

	protected function insertData( $db, array $data = array() ) {
		unset($data['timestamp']); // use built in db functionality instead
		return $db->createCommand()->insert($this->tableName,$data);
	}

	protected function createTable( $db ) {
		return $db->createCommand()->createTable($this->tableName,array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'uri' => 'varchar(255) NOT NULL',
			'route' => 'varchar(127) NOT NULL',
			'requestType' => 'varchar(31) NOT NULL',
			'realMemoryPeakUsage' => 'int(11) NOT NULL',
			'memoryPeakUsage' => 'int(11) NOT NULL',
			'executionTime' => 'float NOT NULL',
			'created' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'PRIMARY KEY (`id`)',
			'KEY `route` (`route`)',
			'KEY `executionTime` (`executionTime`)',
			'KEY `created` (`created`)'
		));
	}

}

?>
