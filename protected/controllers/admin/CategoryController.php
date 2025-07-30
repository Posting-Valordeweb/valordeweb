<?php
class CategoryController extends BackController {
	public function actionIndex() {
		$categories = Category::model()->sorted()->findAll();
		$this->title=Yii::t("category", "Manage Categories");

		$this->render("index", array(
			"categories" => $categories,
		));
	}

	public function actionCreate() {
		$category = new Category;
		$this->title = Yii::t("category", "Create Category");
		if(Yii::app() -> request -> isPostRequest && !empty($_POST['Category'])) {
			$category -> attributes = $_POST['Category'];
			if($category -> save()) {
				Yii::app() -> user -> setFlash('success', Yii::t("category", "Category has been created"));
				$this->redirect(array("admin/category/index"));
			}
		}
		$this->render("create_update", array(
			"category" => $category,
		));
	}

	public function actionUpdate($id) {
		if(!$category=Category::model()->findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		if(Yii::app() -> request -> isPostRequest && !empty($_POST['Category'])) {
			$category -> attributes = $_POST['Category'];
			if($category -> save()) {
				Yii::app() -> user -> setFlash('success', Yii::t("notification", "Record has been successfully modified"));
				$this->redirect(array("admin/category/index"));
			}
		}
		$this->title = Yii::t("category", "Edit category"). " : " . $category->name;
		$this->render("create_update", array(
			"category" => $category,
		));
	}

	public function actionDelete($id) {
		if(!$category=Category::model()->findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$transaction = Yii::app() -> db -> beginTransaction();
		try {
			Category::model()->deleteAllByAttributes(array("id"=>$id));
			CategoryTranslation::model()->deleteAllByAttributes(array("category_id"=>$id));
			$onSale=Sale::model()->findAllByAttributes(array(
				"category_id"=>$id
			));
			foreach($onSale as $sale) {
				SaleEvent::removeFromSale($sale->website_id);
			}
			$transaction -> commit();
			Yii::app() -> user -> setFlash('success', Yii::t("notification", "Record has been deleted"));
		} catch (Exception $e) {
			$transaction -> rollback();
			Yii::log($e->getMessage(), 'error', 'application.admin.category.delete');
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
		}
		$this->redirect(array("admin/category/index"));
	}

	public function actionManageTranslations($id) {
		if(!$category=Category::model()->with('translations')->findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$this->title=Yii::t("language", "Manage Translations") . " : ". $category->name;

		$this->render("translation", array(
			"category" => $category,
		));
	}

	public function actionCreateTranslation($id) {
		if(!$category=Category::model()->findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$trans = new CategoryTranslation;
		$trans->lang_id = Yii::app()->request->getQuery('lang_id');
		$trans->category_id = $id;
		$this->title = Yii::t("category", "Category Translation"). " : ". $category->name;

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['CategoryTranslation'])) {
			$trans -> attributes = $_POST['CategoryTranslation'];
			if($trans -> save()) {
				Yii::app() -> user -> setFlash("success", Yii::t("category", "Translation has been created"));
				$this->redirect(array("admin/category/managetranslations", "id"=>$id));
			}
		}

		$this->render("translation_create_update", array(
			"trans"=>$trans,
		));
	}

	public function actionUpdateTranslation($id) {
		if(!$trans=CategoryTranslation::model()->with('category')->findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$this->title = Yii::t("category", "Update translation"). " : ". $trans->category->name;

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['CategoryTranslation'])) {
			$trans -> attributes = $_POST['CategoryTranslation'];
			if($trans -> save()) {
				Yii::app() -> user -> setFlash("success", Yii::t("category", "Translation has been updated"));
				$this->redirect(array("admin/category/managetranslations", "id"=>$trans->category->id));
			}
		}

		$this->render("translation_create_update", array(
			"trans"=>$trans,
		));
	}

	public function actionDeleteTranslation($id) {
		$cat_id = Yii::app()->request->getQuery('cat_id');
		if(CategoryTranslation::model()->deleteByPk($id)) {
			Yii::app() -> user -> setFlash('success', Yii::t("category", "Translation has been deleted"));
		} else {
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
		}
		$this->redirect(array("admin/category/managetranslations", "id"=>$cat_id));
	}
}
