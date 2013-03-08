<?php

class ApacheLogServerAnalyticsApplicationComponent extends ServerAnalyticsApplicationComponent {

	public function init() {
		if (YII_DEBUG && (php_sapi_name() == 'cli'))
			throw new CException('Unable to write to apache server logs because I run as CLI.');

		return parent::init();
	}

	protected function save( array $data = array() ) {
		foreach ($data as $key => $value)
			apache_note('yii_sa_'.$key, $value);
	}

}

?>