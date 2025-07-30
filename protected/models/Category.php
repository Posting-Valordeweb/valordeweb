<?php
class Category extends CActiveRecord {
	/**
	* @param string $className
	* @return Category
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{category}}';
	}

	public function relations() {
		return array(
			'translations' => array(self::HAS_MANY, 'CategoryTranslation', 'category_id'),
			'onsaleCount' => array(self::STAT, 'Sale', 'category_id'),
			'websites' => array(self::HAS_MANY, 'Sale', 'category_id'),
		);
	}

	public function scopes() {
		return array(
			'sorted'=> array(
				'order' => 'name ASC',
			),
		);
	}

	public function getTranslationObject($lang_id=null) {
		$lang_id = empty($lang_id) ? Yii::app()->language : $lang_id;
		if($this->translations == null) {
			return null;
		}
		foreach($this->translations as $translation) {
			if($translation->lang_id == $lang_id) {
				return $translation;
			}
		}
		return null;
	}

	public function getTranslation($lang_id=null) {
		if($translation=$this->getTranslationObject($lang_id)) {
			return $translation->translation;
		}
		return $this->name;
	}

	public function hasTranslation($lang_id=null) {
		$lang_id = empty($lang_id) ? Yii::app()->language : $lang_id;
		if($this->translations === null) {
			return false;
		}
		foreach($this->translations as $translation) {
			if($translation->lang_id == $lang_id) {
				return true;
			}
		}
		return false;
	}

	public function rules() {
		return array(
			array('slug', 'filter', 'filter'=>array('Helper', 'slug')),
			array('name, slug', 'required'),
			array('name, slug', 'length', 'max'=>50),
			array('slug', 'unique'),
		);
	}

	public function attributeLabels() {
		return array(
			"name"=>Yii::t("category", "Category"),
			"slug"=>Yii::t("category", "Slug"),
		);
	}
}