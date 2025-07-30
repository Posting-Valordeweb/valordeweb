<?php
class SaleChecker extends CActiveRecord {
	/**
	* @param string $className
	* @return Sale
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{onsale_checker}}';
	}
}