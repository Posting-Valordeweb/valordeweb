<?php
class CategoryTranslation extends CActiveRecord {
	/**
	* @param string $className
	* @return Category
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{category_translation}}';
	}

	public function relations() {
		return array(
			'category' => array(self::BELONGS_TO, 'Category', 'category_id'),
		);
	}

	public function scopes() {
		return array(
			'current_lang'=>array(
				'condition'=>'translations.lang_id=:lang_id',
				'params'=>array(':lang_id'=>Yii::app()->language),
			),
		);
	}

	public function rules() {
		return array(
			array('category_id, lang_id, translation', 'required'),
			array('translation', 'length', 'max'=>50),
			array('lang_id', 'isUnique'),
		);
	}

	public function attributeLabels() {
		return array(
			"category_id"=>Yii::t("misc", "ID"),
			"lang_id"=>Yii::t("language", "Language"),
			"translation"=>Yii::t("language", "Translation"),
		);
	}

	public function isUnique() {
		if(!$this->hasErrors()) {
			if($this->isNewRecord) {
				$row = $this->findByAttributes(array(
					"category_id" => $this->category_id,
					"lang_id"=>$this->lang_id,
				));
			} else {
				$row = $this->findByAttributes(array(
					"category_id" => $this->category_id,
					"lang_id"=>$this->lang_id,
				), "id!=:id", array(
					":id"=>$this->id
				));
			}
			if($row) {
				$link = CHtml::link(Yii::t("category", "Update translation"), Yii::app()->getController()->createAbsoluteUrl("admin/category/updatetranslation", array("id"=>$row->id)));
				$this->addError("lang_id", Yii::t("category", "Translation for this language already exists. {Update translation}", array(
					"{Update translation}"=>$link,
				)));
			}
		}
	}
}