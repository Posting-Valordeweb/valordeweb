<?php
class BindWebsite extends CActiveRecord {
	/**
	* @param string $className
	* @return Sale
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{bind_website}}';
	}

	public function relations() {
		return array(
			'sale'=>array(self::BELONGS_TO, 'Sale', 'website_id'),
		);
	}
}