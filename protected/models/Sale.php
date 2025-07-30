<?php
class Sale extends CActiveRecord {
	/**
	* @param string $className
	* @return Sale
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{website_sale}}';
	}

	public function relations() {
		return array(
			'category'=>array(self::BELONGS_TO, 'Category', 'category_id'),
			'website'=>array(self::BELONGS_TO, 'Website', 'website_id'),
			'user'=>array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	public function rules() {
		return array(
			array('website_id, category_id, user_id, price, monthly_visitors, monthly_revenue, monthly_views, description', 'required'),
			array('price', 'numerical', 'min'=>1),
			array('monthly_visitors', 'numerical', 'min'=>1),
			array('monthly_revenue', 'numerical', 'min'=>1),
			array('monthly_views', 'numerical', 'min'=>1),
			array('description', 'length', 'min'=>50),
			array('website_id', 'unique'),
			array('category_id', 'exist', 'className'=>'Category', 'attributeName'=>'id'),
			array('user_id', 'exist', 'className'=>'User', 'attributeName'=>'id'),
			array('website_id', 'exist', 'className'=>'Website', 'attributeName'=>'id'),
			array('website_id', 'checkDoFollow', 'on'=>'add'),
		);
	}

	public function beforeSave() {
		if(parent::beforeSave()) {
			$now = date("Y-m-d H:i:s");
			if($this->isNewRecord) {
				$this -> added_at = $now;
				$this -> modified_at = $now;
			} else {
				$this -> modified_at = $now;
			}
			return true;
		} else {
			return false;
		}
	}

	public function attributeLabels() {
		return array(
			"website_id"=>Yii::t("sale", "Domain/Website"),
			"category_id"=>Yii::t("sale", "Category"),
			"user_id"=>Yii::t("user", "User"),
			"price"=>Yii::t("website", "Selling price"),
			"monthly_visitors"=>Yii::t("website", "Unique monthly visitors"),
			"monthly_revenue"=>Yii::t("website", "Monthly revenue"),
			"monthly_views"=>Yii::t("website", "Monthly page view"),
			"description"=>Yii::t("website", "Notes"),
		);
	}


	public function checkDoFollow() {
		if(!$this -> hasErrors()) {
			if(!Helper::checkDoFollowLink($this->website->domain)) {
				$this->addError("website_id", Yii::t("sale", "The widget has not been found"));
			}
		}
	}
}