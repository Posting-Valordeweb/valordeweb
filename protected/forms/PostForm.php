<?php
class PostForm extends CFormModel {
	public $message, $subject, $companion_id;
	public function rules() {
		return array(
			array('message', 'filter', 'filter'=>'trim'),
            array('subject', 'filter', 'filter'=>'trim', 'on'=>'new'),
			array('message, companion_id', 'required'),
			array('subject', 'required', 'on'=>'new'),
			array('companion_id', 'canSendMessage')
		);
	}

	public function canSendMessage() {
		if($this->hasErrors()) {
			return false;
		}
		if(Yii::app()->user->isGuest) {
			$this->addError("companion_id", Yii::t("post", "Unauthorized users can not send messages"));
			return false;
		}
		if(Yii::app()->user->id==$this->companion_id) {
			$this->addError("companion_id", Yii::t("post", "You can't send message to yourself"));
			return false;
		}
		$box = Yii::app()->innerMail->box(Yii::app()->user->loadModel());
		$block=$box->getBlock($this->companion_id);

		if(!Yii::app()->user->loadModel()->canSendMessage()) {
			$this->addError("companion_id", Yii::t("post", "You are forbidden to send messages"));
			return false;
		}

		if($block['external']) {
			$this->addError("companion_id", Yii::t("post", "This user has blocked you"));
			return false;
		}
		return true;
	}

	public function attributeLabels() {
		return array(
			"message"=>Yii::t("post", "Message"),
			"subject"=>Yii::t("contact", "Subject"),
		);
	}

}