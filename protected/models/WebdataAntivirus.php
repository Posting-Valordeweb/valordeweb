<?php
class WebdataAntivirus extends CActiveRecord {
	/**
	* @param string $className
	* @return WebdataAntivirus
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{webdata_antivirus}}';
	}

	public function rules() {
		return array(
			array('wid, google, avg', 'required'),
		);
	}
	public function attributeLabels() {
		return array(
			"wid"=>Yii::t("misc", "ID"),
			"google"=>Yii::t("website", "Google"),
			"avg"=>Yii::t("website", "AVG"),
		);
	}
}