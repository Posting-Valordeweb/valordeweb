<?php
class WebdataSearchEngine extends CActiveRecord {
	/**
	* @param string $className
	* @return WebdataSearchEngine
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{webdata_searchengine}}';
	}

	public function rules() {
		return array(
			array('wid, google_index, bing_index, google_backlinks, page_rank, yahoo_index', 'required'),
		);
	}

	public $prTotal;

	public function scopes() {
		return array(
			'prGroup'=>array(
				'select'=>'count(*) as prTotal, page_rank',
				'group'=>'page_rank'
			),
		);
	}

	public function attributeLabels() {
		return array(
			"wid"=>Yii::t("misc", "ID"),
			"google_index"=>Yii::t("website", "Google Index"),
			"bing_index"=>Yii::t("website", "Bing Index"),
			"google_backlinks"=>Yii::t("website", "Google Backlinks"),
			"page_rank"=>Yii::t("website", "Page Rank"),
			"yahoo_index"=>Yii::t("website", "Yahoo Index"),
		);
	}

}