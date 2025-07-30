<?php
class WebdataLocation extends CActiveRecord {
	/**
	* @param string $className
	* @return WebdataLocation
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{webdata_location}}';
	}

	public function rules() {
		return array(
			array('wid, city, region_name, ip, longitude, latitude, country_code', 'required'),
		);
	}

	public $countryTotal;
	public function scopes() {
		return array(
			'countryGroup'=>array(
				'select'=>'count(*) as countryTotal, country_code',
				'condition'=>'country_code!=:country_code',
				'params'=>array(':country_code'=>'XX'),
				'group'=>'country_code',
				'order'=>'countryTotal DESC',
			),
		);
	}

	public function attributeLabels() {
		return array(
			"wid"=>Yii::t("misc", "ID"),
			"city"=>Yii::t("website", "City"),
			"region_name"=>Yii::t("website", "Region"),
			"ip"=>Yii::t("website", "IP Address"),
			"longitude"=>Yii::t("website", "Longitude"),
			"latitude"=>Yii::t("website", "Latitude"),
			"country_code"=>Yii::t("website", "Country"),
		);
	}
}