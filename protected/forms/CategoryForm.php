<?php
class CategoryForm extends CFormModel {
	public $oldName, $newName;

	public function rules() {
		return array(
			array('newName, oldName', 'filter', 'filter'=>'strtolower'),
			array('newName', 'required'),
			array('newName', 'length', 'max'=>32),
			array('newName', 'match', 'pattern' => '#^[a-z0-9\_\.]+$#i', 'message' => Yii::t("language", "Invalid category name")),
			array('newName', 'isUnique'),
		);
	}

	public function isUnique() {
		if($this->hasErrors()) {
			return false;
		}
		if(Yii::app()->messages->allowToUpdCategory($this->oldName,$this->newName)) {
			$this->addError("message", Yii::t("language", "The category {Category} has already been taken", array(
				"{Category}" => "<strong>".$this->newName."</strong>",
			)));
		}
	}

	public function attributeLabels() {
		return array(
			"newName"=>Yii::t("language", "Category name")
		);
	}
}
