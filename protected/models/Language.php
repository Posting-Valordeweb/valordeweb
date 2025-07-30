<?php
class Language extends CActiveRecord {
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;

	const DEFAULT_LANG = 1;
	const NOTDEFAULT_LANG = 0;

	/**
	* @param string $className
	* @return Language instance
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{language_list}}';
	}

	public function beforeSave() {
		if(parent::beforeSave()) {
			$now = date("Y-m-d H:i:s");
			if($this->isNewRecord) {
				$this->created_at = $now;
                $this->updated_at = $now;
			} else {
				$this->updated_at = $now;
			}
			return true;
		} else {
			return false;
		}
	}

	public function scopes() {
		return array(
			'sortable' => array(
				'order' => 'language ASC',
			),
			'enabled' => array(
				'condition' => 'enabled=:enabled',
				'params' => array(':enabled' => self::STATUS_ENABLED),
			),
			'defaultLang' => array(
				'condition' => 'is_default=:is_default AND enabled=:enabled',
				'params' => array(':is_default' => self::DEFAULT_LANG, ':enabled' => self::STATUS_ENABLED),
			),
		);
	}

	public function isEnabled() {
		return $this->enabled == self::STATUS_ENABLED;
	}

	public function isDefault() {
		return $this->is_default == self::DEFAULT_LANG;
	}

	public $copy;
	public function rules() {
		return array(
			array('language, enabled, is_default', 'required'),

			array('copy', 'safe', 'on' => 'create'),
			array('copy', 'existLanguage', 'on' => 'create'),

			array('id', 'required', 'on' => 'create'),
			array('id', 'length', 'min' => 2, 'on' => 'create'),
			array('id', 'match', 'pattern' => '#^([a-z]{2}|[a-z]{2}_[a-z]{2})$#', 'message' => Yii::t('language', 'Incorrect Language ID format'), 'on'=>'create'),
			array('id', 'unique', 'attributeName' => 'id', 'className' => 'Language', 'on'=>'create'),

			array('is_default', 'checkDefault', 'on'=>'update'),
			array('enabled', 'allowedDisabledState'),

			array('language', 'length', 'max' => 50),
			array('enabled', 'in', 'range'=>array(self::STATUS_ENABLED, self::STATUS_DISABLED)),
			array('is_default', 'in', 'range'=>array(self::DEFAULT_LANG, self::NOTDEFAULT_LANG)),
		);
	}

	public function attributeLabels() {
		return array(
			"id"=>Yii::t("language", "Language ID"),
			"language"=>Yii::t("language", "Language"),
			"enabled"=>Yii::t("language", "Is Enabled"),
			"is_default"=>Yii::t("language", "Is Default"),
			"copy"=>Yii::t("language", "Make copy"),
		);
	}

	public function existLanguage() {
		if(!empty($this->copy) AND !$this->issetLang($this->copy)) {
			$this->addError("copy", Yii::t("language", "The language doesn't exists"));
		}
	}

	public function allowedDisabledState() {
		if($this->isDefault() AND !$this->isEnabled()) {
			$this->addError("enabled", Yii::t("language", "Default language can't be disabled"));
		}
	}

	public function checkDefault() {
		if($this->hasErrors() OR $this->isDefault()) {
			return false;
		}
		$criteria = new CDbCriteria();
		$criteria -> condition = 'is_default=:is_default AND id!=:id';
		$criteria -> params = array(':is_default'=>self::DEFAULT_LANG, ':id'=>$this->id);
		if($this->count($criteria) == 0) {
			$this->addError("is_default", Yii::t("language", "Default language must be specified"));
		}
	}

	public function getDefault() {
		static $lang;
		if(!empty($lang)) { return $lang; }
		$year = 60 * 60 * 24 * 365;
		return $lang = $this->cache($year)->defaultLang()->find(array("select"=>"id, language"));
	}

	public function getList($enabled = true) {
		$year = 60 * 60 * 24 * 365;
		if($enabled) {
			return $this->cache($year)->enabled()->sortable()->findAll(array("index"=>"id", "select"=>"id, language"));
		} else {
			return $this->cache($year)->sortable()->findAll(array("index"=>"id", "select"=>"id, language"));
		}
	}

	public function issetLang($lang, $enabled=false) {
		$list=$this->getList($enabled);
		return isset($list[$lang]);
	}

	public static function formatLanguage($language) {
		return CHtml::encode($language->language. ' ('. $language->id.')');
	}
}