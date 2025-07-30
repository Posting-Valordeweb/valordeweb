<?php
class FrontController extends BaseController {
	protected $_end = 'front';

	public $newMessages=0;
	public $senders;

	public function init() {
		parent::init();
		if(!Yii::app()->user->isGuest AND !Yii::app()->request->isPostRequest) {
			$this->setUpcomingData();
		}
		$this->checkAuthorizationAllowance();
	}

    public function beforeAction($action)
    {
        $this->registerCookieDisclaimer();
        return parent::beforeAction($action);
    }

	protected function setUpcomingData() {
		$box=Yii::app()->innerMail->box(Yii::app()->user->loadModel());
		$this->newMessages=$box->getNewMessageCount();
	}

    protected function registerCookieDisclaimer() {
        if(Yii::app()->request->isAjaxRequest OR !Helper::can_show_cookie_consent()) {
            return true;
        }

        /**
         * @var $cs CClientScript
         */
        $cs = Yii::app()->clientScript;
        $cs->registerScriptFile(Yii::app()->request->getBaseUrl(true)."/js/cookieconsent.latest.min.js", CClientScript::POS_END);
        $path = Yii::app()->params['app.base_url'].";SameSite=".Yii::app()->params['cookie.same_site'];
        if(Yii::app()->params['cookie.secure']) {
            $path .= ";secure";
        }
        $cs->registerScript("cookieconsent", "
			window.cookieconsent_options = {
				learnMore: ".CJavaScript::encode(Yii::t("site", "Learn more")).",
				dismiss: ". CJavaScript::encode(Yii::t("site", "OK")).",
				message: ". CJavaScript::encode(Yii::t("site", "This website uses cookies to ensure you get the best experience on our website.")).",
				theme:". CJavaScript::encode(Yii::app()->params['cookie_law.theme']).",
				link: ". CJavaScript::encode(Helper::url_privacy()).",
				path: ". CJavaScript::encode($path).",
				expiryDays: ". CJavaScript::encode(Yii::app()->params['cookie_law.expiry_days'])."
			};
		", CClientScript::POS_HEAD);
        $cs->registerCss("hide_cookie_law_logo", "
            .cc_logo { display: none !important; }
        ");
        return true;
    }

	protected function setUserLoginUrl() {
		Yii::app()->user->loginUrl = $this->createUrl('user/sign-in');
	}

	protected function setErrorHandlerAction() {
		Yii::app()->errorHandler->errorAction='site/error';
	}

	private function checkAuthorizationAllowance()
    {
        if(Yii::app()->params['app.allow_user_auth'] OR Yii::app()->user->isGuest) {
            return;
        }
        if(Yii::app()->user->loadModel()->isSimpleUser() AND Yii::app()->urlManager->parseUrl(Yii::app()->request) !== "user/logout") {
            $this->redirect(array("user/logout"));
        }
    }

    protected function simplePage($p) {
        $params=array(
            "{app_name}"=>Yii::app()->params['app.name'],
            '{privacy_url}'=>Helper::url_privacy(),
            '{terms_url}'=>$this->createUrl('site/terms'),
            '{login_url}'=>Yii::app()->user->loginUrl,
        );
        $this -> title = Yii::t("page_$p", "page_{$p}_meta_title", $params);

        $cs=Yii::app()->clientScript;
        $cs->registerMetaTag(Yii::t("page_$p", "page_{$p}_meta_keywords", $params), 'keywords');
        $cs->registerMetaTag(Yii::t("page_$p", "page_{$p}_meta_description", $params), 'description');

        $this->render("simple", array(
            "heading" => Yii::t("page_$p", "page_{$p}_title", $params),
            "text" => Yii::t("page_$p", "page_{$p}_text", $params),
        ));
    }
}