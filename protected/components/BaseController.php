<?php
abstract class BaseController extends CController {
	public $title;
	protected $_end;

	public function init() {
		parent::init();
		$this -> setLanguage();
		$this -> attachEvenetHandlers();
		$this -> layout = '/'.$this->_end.'/layouts/column';
		$this -> setUserLoginUrl();
		$this -> setErrorHandlerAction();
		$this -> registerJsGlobalVars();
        CHtml::$errorCss = 'is-invalid';
	}

	public function setLanguage() {
	    if(!Yii::app()->params['url.multi_language_links']) {
	        $lang = Yii::app()->language;
        } else if (isset($_GET['language']) && Language::model()->issetLang($_GET['language'])) {
			$lang = $_GET['language'];
			$cookie = new CHttpCookie('language', $lang);
            $cookie->sameSite = Yii::app()->params['cookie.same_site'];
            $cookie->path = Yii::app()->params['app.base_url'];
            $cookie->secure = Yii::app()->params['cookie.secure'];
			$cookie->expire = time() + (60 * 60 * 24 * 365);
			Yii::app()->request->cookies['language'] = $cookie;
		}
		elseif (isset(Yii::app()->request->cookies['language']))
			$lang = Yii::app()->request->cookies['language']->value;
		else
			$lang = Yii::app()->getRequest()->getPreferredLanguage();

		if(!Language::model()->issetLang($lang)) {
			$lang = Language::model()->getDefault()->id;
		}
		Yii::app() -> language = $lang;
	}

	protected function registerJsGlobalVars() {
		$baseUrl = Yii::app()->request->getBaseUrl(true);
		Yii::app()->clientScript->registerScript('globalVars', "
			var _global = {
				baseUrl: '{$baseUrl}',
				proxyImage: ". (int) Yii::app()->params['thumbnail.proxy'] ."
			};
		", CClientScript::POS_HEAD);
	}

	public function attachEvenetHandlers() {
		foreach($this->events() as $event => $handlers) {
			if(method_exists($this, $event)) {
				foreach($handlers as $handler) {
					$this->attachEventHandler($event, $handler);
				}
			}
		}
	}

	abstract protected function setUserLoginUrl();
	abstract protected function setErrorHandlerAction();

	public function getViewPath($widget = false) {
		if(($module=$this->getModule())===null)
				$module=Yii::app();
		$id = str_replace("admin/", "", $this->getId());
		return $module->getViewPath().DIRECTORY_SEPARATOR.$this->_end.DIRECTORY_SEPARATOR. ($widget == true ? 'widgets' : $id);
	}

	public function actionError() {
		if($error = Yii::app() -> errorHandler -> error) {
			if(Yii::app() -> request -> isAjaxRequest) {
				echo $error['message'];
			} else {
				switch($error['code']) {
					case 404;
						$this->title=Yii::t("site", "Page not found");
					break;
					case 500;
						$this->title=Yii::t("site", "Internal server error");
					break;
					case 400;
						$this->title=Yii::t("site", "Access denied");
					break;
					default:
						$this->title=$error['code'];
					break;
				}
				$this -> render("/{$this->_end}/site/error", $error);
			}
		}
	}

	public function missingAction($action){
		$action = explode('-', $action);
		$action = array_map('strtolower', $action);
		$action = array_map('ucfirst', $action);
		$action = implode('',$action);
		if(method_exists($this,'action'.$action) || array_key_exists('action'.$action, $this->actions())){
			$this->setAction($action);
			$this->run($action);
		} else {
			throw new CHttpException(404, Yii::t('notification','Action "{action}" does not exist in "{controller}".', array(
				'{action}' => 'action'.$action,
				'{controller}' => get_class($this),
			)));
		}
	}

	public function events() {
		return array();
	}

	public function filters() {
		return array(
			'accessControl',
		);
	}

	public function accessRules() {
		return array(
			array(
				'allow',
				'controllers' => array('site', 'user', 'website', 'category', 'cron', 'pagepeekerproxy', 'feed'),
				'users' => array('*'),
			),
			array(
				'allow',
				'controllers' => array('post', 'sale', 'profile'),
				'users' => array('@'),
			),
			array(
				'allow',
				'actions'=>array('login', 'logout'),
				'controllers'=>array('admin/user'),
				'users'=>array('*'),
			),
            array(
                'allow',
                'actions'=>array('error'),
                'controllers' => array('admin/site'),
                'users' => array('*'),
            ),
			array(
				'allow',
				'controllers' => array('admin/site', 'admin/category', 'admin/website', 'admin/tools', 'admin/language', 'admin/scam'),
				'roles' => array('administrator', 'root'),
			),
			array(
				'allow',
				'controllers' => array('admin/user', 'admin/profile'),
				'roles' => array('root'),
			),
			array(
				'deny',
				'users' => array('*'),
			),
		);
	}

	public function jsonResponse($response) {
		header('Content-type: application/json');
		echo json_encode($response);
		Yii::app() -> end();
	}
}