<?php
class WebdataMetaTags extends CActiveRecord {
	/**
	* @param string $className
	* @return WebdataMetaTags
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{webdata_metatags}}';
	}

	public function rules() {
		return array(
			array('wid, title, keywords, description', 'required'),
		);
	}

	public function attributeLabels() {
		return array(
			"wid"=>Yii::t("misc", "ID"),
			"title"=>Yii::t("website", "Title"),
			"keywords"=>Yii::t("website", "Keywords"),
			"description"=>Yii::t("website", "Description"),
		);
	}
}