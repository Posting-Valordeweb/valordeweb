<?php
class LanguageSelector extends Widget {
	public function run() {
		$languages = Language::model()->getList();
		if(!$languages OR count($languages) < 2 OR !Yii::app()->params['url.multi_language_links'] OR Yii::app()->errorHandler->error) {
			return null;
		}
		$this->render("language_selector", array(
			"languages"=>$languages,
		));
	}
}