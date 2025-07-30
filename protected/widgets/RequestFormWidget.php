<?php
class RequestFormWidget extends Widget {
	public $redirect;
	public $requestUrl;
	public $instant=false;
	public $hSize=1;

	public function init() {
		$this->requestUrl=$this->owner->createUrl("website/calculate");
		if(!$this->redirect) {
			$this->redirect=$this->owner->createUrl("website/show", array("domain"=>"__DOMAIN__"));
		}
	}

	public function run() {
		$form = new CalculationForm;
		$total=Website::model()->cache(3600)->count();
		$this->render("request_form", array(
			"form"=>$form,
			"total"=>$total,
		));
	}
}