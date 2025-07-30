<?php
class SiteController extends FrontController {
	public function actionIndex() {
		$cs=Yii::app()->clientScript;
		$this -> title = Yii::t("site", "Index page title");
		$params=array(
			"{Portal}"=>Helper::getInstalledUrl(),
		);
		$cs -> registerMetaTag(Yii::t("site", "Index page keywords", $params), 'keywords');
		$cs -> registerMetaTag(Yii::t("site", "Index page description", $params), 'description');

		$widget = $this->widget('application.widgets.WebsiteList', array(
			"config"=>array(
                "totalItemCount"=>Yii::app()->params['site_cost.websites_on_index_page'],
                "pagination"=>array(
                    "pageSize"=>Yii::app()->params['site_cost.websites_on_index_page']
                )
            ),
		), true);

		$requestForm = $this->widget('application.widgets.RequestFormWidget', array(
		), true);

		$this->render('index', array(
			"widget"=>$widget,
			"requestForm"=>$requestForm,
		));
	}

	public function actionContact() {
		$form = new ContactForm;
		$this -> title = Yii::t("contact", "Contact us");
		$params=array(
			"{Portal}"=>Helper::getInstalledUrl(),
		);
		$cs=Yii::app()->clientScript;
		$cs->registerMetaTag(Yii::t("contact", "Contact page keywords", $params), 'keywords');
		$cs->registerMetaTag(Yii::t("contact", "Contact page description", $params), 'description');

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['ContactForm'])) {
			$form->attributes=$_POST['ContactForm'];
			if($form->validate()) {
				try {
                    $mail = new YiiMailer();

                    $mail->clearLayout();
                    $mail->clearAddresses();
                    $mail->setSubject($form->subject);
                    $mail->setFrom(Yii::app()->params['notification.email'], Yii::app()->params['notification.name']);
                    $mail->clearReplyTos();
                    $mail->addReplyTo($form->email, $form->name);
                    $mail->setTo(Yii::app()->params['admin.email']);
                    $mail->setBody(nl2br($form->body));
                    $mail->setAltText($form->body);

                    $mail->send();
					Yii::app() -> user -> setFlash('success', Yii::t("notification", "Email has been sent"));
				} catch (Exception $e) {
					Yii::log($e->getMessage(), 'error', 'application.site.contact');
					Yii::app() -> user -> setFlash('danger', Yii::t("notification", "An error occurred while sending email"));
				}
				$this -> refresh();
			}
		}

		$this -> render("contact", array(
			'form' => $form,
		));
	}

    public function actionPrivacy() {
        $this->simplePage("privacy");
    }

    public function actionTerms() {
        $this->simplePage("terms");
    }
}