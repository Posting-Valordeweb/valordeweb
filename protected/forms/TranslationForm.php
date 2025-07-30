<?php
class TranslationForm extends CFormModel {
	public $id, $language, $translation;

	public function rules() {
		return array(
			array('id, language, translation', 'required'),
			array('id', 'existsSourceMessage'),
			array('language', 'existsLanguage'),
		);
	}

	public function existsSourceMessage() {
		if($this -> hasErrors()) {
			return false;
		}
		if(!Yii::app()->messages->issetSourceMessageByPk($this->id)) {
			$this->addError("language", Yii::t("language", "The phrase with ID {ID} doesn't exists", array(
				"{ID}" => $this->id,
			)));
		}
	}

	public function existsLanguage() {
		if($this -> hasErrors()) {
			return false;
		}
		if(!Language::model()->issetLang($this->language)) {
			$this->addError("language", Yii::t("language", "The language doesn't exists"));
		}
	}

	public function attributeLabels() {
		return array(
			"language"=>Yii::t("language", "Language"),
			"translation"=>Yii::t("language", "Translation"),
		);
	}
}