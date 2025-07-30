<?php
Yii::import("ext.ecountrylist.ECountryList");

class CountryValidator extends CValidator {
	public $enableClientValidation = false;
	public $message = "Invalid country name";
	public $allowEmpty = false;

	protected function validateAttribute($object, $attribute) {
		$country = ECountryList::getInstance(Yii::app() -> language);
		$value = $object -> $attribute;
		if(!$this -> allowEmpty AND !$country -> existsCountry($value)) {
			$this -> addError($object, $attribute, $this -> message);
		}
	}
}