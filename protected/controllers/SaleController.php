<?php
class SaleController extends AdminEditableController {
	public function actionIndex() {
		$this->title=Yii::t("sale", "My Websites/Domains on Sale");
		$dataProvider=new CActiveDataProvider("Sale", array(
			"countCriteria"=>array(
				"condition"=>"t.user_id=:user_id",
				"params"=>array(":user_id"=>$this->owner->id),
			),
			"criteria"=>array(
				"with"=>array(
					"category"=>array(
						"with"=>array("translations"),
					),
					"website"=>array(
						"select"=>"domain, idn, price",
					),
				),
				"condition"=>"t.user_id=:user_id",
				"params"=>array(":user_id"=>$this->owner->id),
				"order"=>"t.added_at DESC",
			),
			"pagination"=>array(
				"pageVar"=>"page",
				"pageSize"=>Yii::app()->params['site_cost.on_sale_per_page'],
			),
		));
		$this->render("index", array(
			"dataProvider"=>$dataProvider,
		));
	}

	public function catName($category) {
		return CHtml::encode($category->getTranslation());
	}

	public function actionAdd($id) {
		$user_id=$this->owner->id;
		if(!$website=Website::model()->with("sale")->findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		if($website->sale) {
			if($website->sale->user_id != $user_id) {
				Yii::app()->user->setFlash("warning", Yii::t("sale", "The website is already on sale"));
				$this->redirect(array("sale/index"));
			} else {
				$this->redirect(array("sale/view", "id"=>$id));
			}
		}

		$onSale=new Sale;
		$onSale->scenario="add";
		$catList=CHtml::listData(Category::model()->with("translations")->findAll(), 'id', array($this, 'catName'));

		$this->title=Yii::t("sale", "Sell Website/Domain"). " | ". $website->idn;

		if(isset($_POST['Sale']) AND is_array($_POST['Sale'])) {
			$onSale->attributes=$_POST['Sale'];
			$onSale->website_id=$id;
			$onSale->user_id=$user_id;
			if($onSale->save()) {
				Yii::app()->user->setFlash("success", Yii::t("sale", "Congratulations. Your website/domain is on sale"));
				$this->redirect(array("sale/view", "id"=>$id));
			}
		}

		$thumbnail=WebsiteThumbnail::getThumbData(array(
			'url'=>$website->domain,
		));

		$widget=$this->renderPartial("/{$this->_end}/website/widget", array(
			"url"=>$this->createAbsoluteUrl("website/show", array("domain"=>$website->domain)),
			"domain"=>$website->idn,
			"price"=>Helper::p($website->price),
		), true);

		$this->render("add", array(
			"onSale"=>$onSale,
			"catList"=>$catList,
			"website"=>$website,
			"thumbnail"=>$thumbnail,
			"widget"=>$widget,
		));
	}

	public function actionEdit($id) {
		$onSale=$this->loadModel($id);
		$onSale->scenario="edit";
		$this->title = Yii::t("misc", "Edit") . " | " . $onSale->website->domain;
		$catList = CHtml::listData(Category::model()->with("translations")->findAll(), 'id', array($this, 'catName'));
		$thumbnail=WebsiteThumbnail::getThumbData(array(
			'url'=>$onSale->website->domain,
		));

		if(isset($_POST['Sale']) AND is_array($_POST['Sale'])) {
			$onSale->attributes=$_POST['Sale'];
			if($onSale->save()) {
				Yii::app()->user->setFlash("success", Yii::t("sale", "Information has been updated"));
				$this->redirect(array("sale/view", "id"=>$id));
			}
		}

		$this->render("edit", array(
			"onSale"=>$onSale,
			"catList"=>$catList,
			"thumbnail"=>$thumbnail,
		));
	}

	public function actionView($id) {
		$onSale=$this->loadModel($id);
		$this->title=$onSale->website->idn;
		$thumbnail=WebsiteThumbnail::getThumbData(array(
			'url'=>$onSale->website->domain,
		));
		$this->render("view", array(
			"onSale"=>$onSale,
			"thumbnail"=>$thumbnail,
		));
	}

	public function actionRemove($id) {
		$model = $this->loadModel($id);
		if(SaleEvent::removeFromSale($id)) {
			Yii::app() -> user -> setFlash('success', Yii::t("sale", "Website {Domain} has been removed from sale", array(
				"{Domain}"=>$model->website->idn,
			)));
		} else {
			Yii::app() -> user -> setFlash('error', Yii::t('notification', 'Server temporarily unavailable. Try again later.'));
		}
		$this->redirect(array("sale/index"));
	}

	public function actionVerify() {
		$domain=Yii::app()->request->getQuery('d');
		if(Helper::checkDoFollowLink($domain)) {
			$response=array(
				"message"=>Yii::t("sale", "The widget has been found"),
				"class"=>"alert alert-success",
			);
		} else {
			$response=array(
				"message"=>Yii::t("sale", "The widget has not been found"),
				"class"=>"alert alert-danger",
			);
		}
		$this->jsonResponse($response);
	}

	protected function loadModel($id) {
		$user_id=$this->owner->id;
		$onSale=Sale::model()->with(array(
			"category"=>array(
				"with"=>array("translations"),
			),
			"website"=>array(
				"select"=>"domain, idn, price",
			),
		))->findByAttributes(array("user_id"=>$user_id, "website_id"=>$id));
		if(!$onSale) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		return $onSale;
	}
}