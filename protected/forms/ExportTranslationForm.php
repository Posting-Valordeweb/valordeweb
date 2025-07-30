<?php
class ExportTranslationForm extends CFormModel {
	public $language, $zip, $force, $trans_language;

	public function rules() {
		return array(
			array('language', 'required'),
			array('trans_language', 'safe'),
			array('force', 'required', 'on'=>'import'),
			array('trans_language', 'existsLanguage', 'on'=>'export'),
			array('language', 'existsLanguage'),
			array('zip', 'file', 'types'=>'zip', 'on'=>'import'),
		);
	}

	public function existsLanguage($attribute, $params) {
		if(isset($params['on']) AND $params['on'] == 'export' AND empty($this->$attribute)) {
			return true;
		}
		if($this -> hasErrors()) {
			return false;
		}
		if(!Language::model()->issetLang($this->$attribute)) {
			$this->addError("language", Yii::t("language", "The language {Language} doesn't exists in the system", array(
				"{Language}" => $this->$attribute,
			)));
		}
	}

    public function attributeLabels() {
        return array(
            'language'=>Yii::t("language", "Language"),
            "force"=>Yii::t("language", "Forced import"),
            "zip"=>Yii::t("language", "Zip file (translations)"),
            "trans_language"=>Yii::t("language", "Translation") ." (". mb_strtolower(Yii::t("misc", "Optional")) .")",
        );
    }
}