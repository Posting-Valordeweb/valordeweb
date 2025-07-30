<?php
class WebdataCatalog extends CActiveRecord {
	/**
	* @param string $className
	* @return WebdataCatalog
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{webdata_catalog}}';
	}

	public function rules() {
		return array(
			array('wid, dmoz, yahoo', 'required'),
		);
	}

	public function attributeLabels() {
		return array(
			"wid"=>Yii::t("misc", "ID"),
			"dmoz"=>Yii::t("website", "Dmoz"),
			"yahoo"=>Yii::t("websites", "Yahoo"),
		);
	}
}