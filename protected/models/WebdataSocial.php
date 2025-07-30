<?php
class WebdataSocial extends CActiveRecord {
	/**
	* @param string $className
	* @return WebdataSocial
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{webdata_social}}';
	}

	public function rules() {
		return array(
			array('wid, gplus, facebook_share_count, facebook_like_count, facebook_comment_count, facebook_total_count,
			facebook_click_count, twitter, pins, linkedin, stumbleupon, facebook_comment_plugin_count, facebook_reaction_count', 'required'),
		);
	}

	public function attributeLabels() {
		return array(
			"wid"=>Yii::t("misc", "ID"),
			"gplus"=>Yii::t("website", "Gplus+"),
			"twitter"=>Yii::t("website", "Twitter"),
			"pins"=>Yii::t("website", "Pins"),
			"linkedin"=>Yii::t("website", "LinkedIn"),
			"stumbleupon"=>Yii::t("website", "Stumbleupon"),
			"facebook_share_count"=>Yii::t("website", "Share count"),
			"facebook_like_count"=>Yii::t("website", "Like count"),
			"facebook_comment_count"=>Yii::t("website", "Comment count"),
			"facebook_total_count"=>Yii::t("website", "Total count"),
			"facebook_click_count"=>Yii::t("website", "Click count"),
		);
	}
}