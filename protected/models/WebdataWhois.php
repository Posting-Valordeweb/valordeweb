<?php
class WebdataWhois extends CActiveRecord {
	/**
	* @param string $className
	* @return WebdataWhois
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{webdata_whois}}';
	}
	public function rules() {
		return array(
			array('wid, text', 'required'),
		);
	}
	public function attributeLabels() {
		return array(
			"wid"=>Yii::t("misc", "ID"),
			"text"=>Yii::t("website", "WHOIS"),
		);
	}
}