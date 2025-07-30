<?php
class MessageForm extends CFormModel {
	public $message, $category, $id, $new_category;

	public function beforeValidate() {
		if(!parent::beforeValidate()) {
			return false;
		}
		$this->category = !empty($this->new_category) ? $this->new_category : $this->category;
		return true;
	}

	public function rules() {
		return array(
			array('category', 'filter', 'filter'=>'strtolower'),

			array('message', 'required'),
			array('message', 'length', 'max'=>255),
			array('message', 'uniqueMessage'),

			array('new_category', 'safe'),

			array('category', 'required'),
			array('category', 'length', 'max'=>32),
			array('category', 'match', 'pattern' => '#^[a-z0-9\_\.]+$#i', 'message' => Yii::t("language", "Invalid category name")),

			array('id', 'required', 'on' => 'update'),
		);
	}

	public function uniqueMessage() {
		if($this->hasErrors()) {
			return false;
		}
		if(Yii::app()->messages->issetSourceMessage($this->message, $this->category, $this->id)) {
			$this->addError("message", Yii::t("language", "This phrase already exists in {Category} category", array(
				"{Category}" => "<strong>".Helper::mb_ucfirst($this->category)."</strong>",
			)));
		}
	}

	public function attributeLabels() {
		return array(
			'message'=>Yii::t("language", "Phrase"),
			'category'=>Yii::t("language", "Category name"),
			'new_category'=>Yii::t("language", "New category"),
		);
	}

}