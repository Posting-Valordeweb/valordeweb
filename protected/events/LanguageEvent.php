<?php
class LanguageEvent {
	public static function alterDefaultLanguage($event) {
		$model = $event->params["model"];
		if($model->isDefault()) {
			Language::model()->updateAll(array(
				'is_default'=>Language::NOTDEFAULT_LANG,
			), 'id!=:id AND is_default=:is_default', array(
				':is_default'=>Language::DEFAULT_LANG,
				':id'=>$model->id,
			));
		}
	}

	public static function createCopyOfLanguage($event) {
		$model = $event->params["model"];
		if(!empty($model->copy)) {
			Yii::app()->messages->copyLanguage($model->copy, $model->id);
		}
	}

	public static function onLanguageHasBeenRemoved($event) {
		Yii::app()->messages->removeTranslationByLangID($event->params["model"]->id);
		CategoryTranslation::model()->deleteAllByAttributes(array(
			'lang_id'=>$event->params["model"]->id,
		));
	}

    public static function missingTranslation($event) {
        if(!Yii::app()->params['app.log_missing_translations']) {
            return false;
        }
        $sql = "INSERT IGNORE sc_trans_missing (`category`, `key`, `lang_id`, `inserted_at`) VALUES (:category, :key, :lang_id, :datetime)";
        $datetime = date("Y-m-d H:i:s");
        $command = Yii::app()->db->createCommand($sql);
        $command->bindParam(":category", $event->category, PDO::PARAM_STR);
        $command->bindParam(":key", $event->message, PDO::PARAM_STR);
        $command->bindParam(":lang_id", $event->language, PDO::PARAM_STR);
        $command->bindParam(":datetime", $datetime, PDO::PARAM_STR);
        $command->execute();
    }
}