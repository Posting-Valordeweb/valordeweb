<?php
class WebdataAlexa extends CActiveRecord {
	/**
	* @param string $className
	* @return Sale
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{webdata_alexa}}';
	}
	public function rules() {
		return array(
			array('wid, rank, linksin, review_count, review_avg, country_code, country_rank, speed_time, pct, version, data', 'required'),
		);
	}
	public function attributeLabels() {
		return array(
			"wid"=>Yii::t("misc", "ID"),
			"rank"=>Yii::t("website", "Global Rank"),
			"linksin"=>Yii::t("website", "Links in"),
			"review_count"=>Yii::t("website", "Review count"),
			"review_avg"=>Yii::t("website", "Review average"),
			"country_code"=>Yii::t("website", "Country"),
			"country_rank"=>Yii::t("website", "Local Rank"),
			"speed_time"=>Yii::t("website", "Load speed"),
			"pct"=>"%",
		);
	}
}