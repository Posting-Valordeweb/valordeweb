<?php
class LanguageController extends BackController {

	public function events() {
		return array(
			'onLanguageCreated' => array(
				array('LanguageEvent', 'alterDefaultLanguage'),
				array('LanguageEvent', 'createCopyOfLanguage'),
			),
			'onLanguageUpdated' => array(
				array('LanguageEvent', 'alterDefaultLanguage'),
			),
			'onLanguageDeleted' => array(
				array('LanguageEvent', 'onLanguageHasBeenRemoved'),
			),
		);
	}

	public function actionIndex() {
		$languages = Language::model()->sortable()->findAll();
		$this->title=Yii::t('language', 'Manage existing languages');
		$this->render("index", array(
			"languages" => $languages,
		));
	}

	public function actionCreate() {
		$scenario = 'create';
		$model = new Language($scenario);
		$model->copy=Yii::app()->language;

		$languages = CMap::mergeArray(
			array(''=>Yii::t("language", "Do not copy translations"), '-'=>'----------'),
			CHtml::listData(Language::model()->getList(false), 'id', array('Language', 'formatLanguage'))
		);

		$this -> title = Yii::t("language", "Create new Language");

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['Language'])) {
			$model -> attributes = $_POST['Language'];
			if($model -> validate()) {
				$transaction = Yii::app() -> db -> beginTransaction();
				try {
					if(!$model->save(false)) {
						throw new CExcpetion("Unable to save new language {$model->language}");
					}
					$this->onLanguageCreated(
						new CEvent($this, array("model" => $model))
					);
					$transaction->commit();
					Yii::app()->user->setFlash("success", Yii::t("language", "New language {Language} has been created", array(
						"{Language}"=>"<strong>".Language::formatLanguage($model)."</strong>",
					)));
					$this->redirect(array("admin/language/index"));
				} catch(Exception $e) {
					$transaction -> rollback();
					Yii::log($e->getMessage(), 'error', 'application.admin.language.create');
					Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
				}
				$this->refresh();
			}
		}
		$this->render("create", array(
			"model" => $model,
			"languages" => $languages,
		));
	}

	public function actionUpdate($id) {
		if(!$model = Language::model() -> findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$this -> title = Yii::t("language", "Edit {Language} language", array(
			"{Language}"=>Language::formatLanguage($model),
		));

		$oldState = $model->enabled;

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['Language'])) {
			$model -> scenario = "update";
			$model -> attributes = $_POST['Language'];
			if($model -> validate()) {
				$transaction = Yii::app() -> db -> beginTransaction();
				try {
					$newState = $model->enabled;
					$model->save(false);
					$event = new CEvent($this, array("model"=>$model));
					if($oldState == Language::STATUS_ENABLED AND $newState == Language::STATUS_DISABLED) {
						$this->onLanguageDisabled($event);
					} else if($oldState == Language::STATUS_DISABLED AND $newState == Language::STATUS_ENABLED) {
						$this->onLanguageEnabled($event);
					}
					$this->onLanguageUpdated($event);
					$transaction->commit();
					Yii::app()->user->setFlash('success', Yii::t("notification", "Record has been successfully modified"));
					$this->redirect(array("admin/language/index"));
				} catch(Exception $e) {
					$transaction -> rollback();
					Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
					Yii::log($e->getMessage(), 'error', 'application.admin.language.update');
				}
				$this->refresh();
			}
		}

		$this->render("update", array(
			"model" => $model,
		));
	}

	public function actionDelete($id) {
		if(!$model = Language::model() -> findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		if($model->isDefault()) {
			Yii::app()->user->setFlash('warning', Yii::t("language", "Default language can't be deleted"));
			$this->redirect($this->createUrl("admin/language/index"));
		}
		$transaction = Yii::app() -> db -> beginTransaction();
		try {
			if(!$model->delete()) {
				throw new CException("Unable to delete language {$model->id}");
			}
			$this->onLanguageDeleted(
				new CEvent($this, array("model"=>$model))
			);
			$transaction->commit();
			Yii::app()->user->setFlash('success', Yii::t("notification", "Record has been deleted"));
		} catch(Exception $e) {
			$transaction -> rollback();
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
			Yii::log($e->getMessage(), 'error', 'application.admin.language.delete');
		}
		$this->redirect($this->createUrl("admin/language/index"));
	}

	public function actionCategory() {
		$categories = Yii::app()->messages->getCategoryList();
		$this->title=Yii::t("language", "Manage Translations");
		$this->render("category", array(
			"categories"=>$categories,
			"missingCnt"=>Yii::app()->messages->totalMissingTranslations(),
		));
	}

	public function actionCategoryUpdate($id) { // category name
		if(!Yii::app()->messages->issetCategory($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$this->title=Yii::t("category", "Edit category")." | ". Helper::mb_ucfirst($id);

		$model = new CategoryForm;
		$model->oldName=$model->newName=$id;

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['CategoryForm'])) {
			$model->attributes = $_POST['CategoryForm'];
			if($model->validate()) {
				$transaction = Yii::app() -> db -> beginTransaction();
				try {
					Yii::app()->messages->updateCategoryName($model->oldName, $model->newName);
					$this->onCategoryUpdated(
						new CEvent($this, array("model"=>$model))
					);
					$transaction->commit();
					Yii::app()->user->setFlash('success', Yii::t("notification", "Record has been successfully modified"));
					$this->redirect($this->createUrl("admin/language/category"));
				} catch(Exception $e) {
					$transaction -> rollback();
					Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
					Yii::log($e->getMessage(), 'error', 'application.admin.language.categoryupdate');
				}
				$this->refresh();
			}
		}

		$this->render("category_update", array(
			"model" => $model,
		));
	}

	public function actionDeleteCategory($id) {
		if(!Yii::app()->messages->issetCategory($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$transaction = Yii::app() -> db -> beginTransaction();
		try {
			Yii::app()->messages->removeAllFromCategory($id);
			$this->onCategoryDeleted(
				new CEvent($this, array("category"=>$id))
			);
			$transaction->commit();
			Yii::app()->user->setFlash('success', Yii::t("notification", "Record has been deleted"));
		} catch(Exception $e) {
			$transaction -> rollback();
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
			Yii::log($e->getMessage(), 'error', 'application.admin.language.categorydelete');
		}
		$this->redirect($this->createUrl("admin/language/category"));
	}

	public function actionMessages($id) { // category_id
		if(!Yii::app()->messages->issetCategory($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$m = Yii::app()->messages;
		$messages = $m -> findAllInCategory($id);
		$this->title = Yii::t("language", "Manage translations in {Category} category", array(
			"{Category}"=>Helper::mb_ucfirst($id),
		));
		$this->render("source_message", array(
			"messages" => $messages,
			"id"=>$id,
			"cat_id"=>$id,
		));
	}

	public function actionCreateMessage() {
		$scenario = 'create';
		$cat_id=Yii::app()->request->getQuery('cat_id');
		$this->title=Yii::t("language", "Create new phrase");

		$categories = CMap::mergeArray(
			array(''=>Yii::t("language", "Choose any category"), '-'=>'----------'),
			Yii::app()->messages->getCategoryList()
		);

		$model = new MessageForm($scenario);
		$model->category=$cat_id;

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['MessageForm'])) {
			$model->attributes = $_POST['MessageForm'];
			if($model->validate()) {
				$transaction = Yii::app() -> db -> beginTransaction();
				try {
					Yii::app()->messages->saveSourceMessage($model->category, $model->message);
					$model->id = Yii::app()->db->getLastInsertID();
					$this->onMessageCreated(
						new CEvent($this, array("model"=>$model))
					);
					$transaction->commit();
					Yii::app()->user->setFlash('success', Yii::t("language", "New phrase has been created"));
					$this->redirect($this->createUrl("admin/language/translatemessage", array("id"=>$model->id)));
				} catch(Exception $e) {
					$transaction -> rollback();
					Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
					Yii::log($e->getMessage(), 'error', 'application.admin.language.createmessage');
				}
				$this->refresh();
			}
		}

		$this->render("create_message", array(
			"model" => $model,
			"categories" => $categories,
			"existCat"=>isset($categories[$model->category]) AND in_array($model->category, $categories),
		));
	}

	public function actionUpdateMessage($id) { // message id
		if(!$source = Yii::app()->messages->findSourceMessageByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$this->title=Yii::t("language", "Edit phrase");
		$scenario = "update";
		$model = new MessageForm($scenario);
		$model -> id = $source['id'];
		$model -> category = $source['category'];
		$model -> message = $source['message'];

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['MessageForm'])) {
			$model->attributes = $_POST['MessageForm'];
			if($model->validate()) {
				$transaction = Yii::app() -> db -> beginTransaction();
				try {
					Yii::app()->messages->updateSourceMessage(array(
						"message"=>$model->message,
						"category"=>$model->category,
					), $model->id);
					$this->onMessageUpdated(
						new CEvent($this, array("model"=>$model))
					);
					$transaction->commit();
					Yii::app()->user->setFlash('success', Yii::t("notification", "Record has been successfully modified"));
				} catch(Exception $e) {
					$transaction -> rollback();
					Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
					Yii::log($e->getMessage(), 'error', 'application.admin.language.updatemessage');
				}
				$this->refresh();
			}
		}

		$categories = CMap::mergeArray(
			array(''=>Yii::t("language", "Choose any category"), '-'=>'----------'),
			Yii::app()->messages->getCategoryList()
		);

		$this->render("update_message", array(
			"model"=>$model,
			"source"=>$source,
			"categories"=>$categories,
		));
	}

	public function actionDeleteMessage($id) {
		if(!$row = Yii::app()->messages->findSourceMessageByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$transaction = Yii::app() -> db -> beginTransaction();
		try {
			Yii::app()->messages->removeTranslationBySourceID($id);
			$this->onMessageDeleted(
				new CEvent($this, array("row"=>$row))
			);
			$transaction->commit();
			Yii::app()->user->setFlash('success', Yii::t("notification", "Record has been deleted"));
		} catch(Exception $e) {
			$transaction -> rollback();
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
			Yii::log($e->getMessage(), 'error', 'application.admin.language.deletemessage');
		}
		$this->redirect($this->createUrl("admin/language/messages", array("id"=>$row['category'])));
	}

	public function actionTranslateMessage($id) {
		if(!$source = Yii::app()->messages->findSourceMessageByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$translations = Yii::app()->messages->getTranslationsBySourceID($id);
		$model = new TranslationForm;
		$model->id = $id;
		$this->title=Yii::t("language", "Translate phrase");

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['TranslationForm'])) {
			$model->attributes = $_POST['TranslationForm'];
			if($model->validate()) {
				$transaction = Yii::app() -> db -> beginTransaction();
				try {
					Yii::app()->messages->translateSourceMessage($model->id, $model->language, $model->translation, true);
					$this->onMessageTranslated(
						new CEvent($this, array("model"=>$model))
					);
					$transaction->commit();
					Yii::app()->user->setFlash('success', Yii::t("language", "Phrase has been translated"));
				} catch(Exception $e) {
					$transaction -> rollback();
					Yii::log($e->getMessage(), 'error', 'application.admin.language.translatemessage');
					Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
				}
				$this->refresh();
			}
		}

		$langObj=Language::model()->getList(false);
		$languages = CMap::mergeArray(
			array(''=>Yii::t("language", "Choose any language"), '-'=>'----------'),
			CHtml::listData($langObj, 'id', array('Language', 'formatLanguage'))
		);

		$this->render("translate_message", array(
			"model" => $model,
			"languages" => $languages,
			"translations" =>$translations,
			"source" => $source,
			"langObj"=>$langObj,
		));
	}

	public function actionSearch() {
		$this->title = Yii::t("language", "Find phrase/translation");
		$q=null;

		// Form has been sent
		if(!empty($_GET['q'])) {
			$m = Yii::app()->messages;
			$q=$_GET['q'];
			$criteria = new CDbCriteria;

			$criteria->select = 'count(s.id)';
			$criteria->join = "LEFT JOIN {$m->translatedMessageTable} as m ON s.id = m.id";

			$criteria->addSearchCondition('m.translation', $q, true, 'OR');
			$criteria->addSearchCondition('s.message', $q, true, 'OR');

			if(!empty($_GET['category'])) {
				$criteria->addCondition("s.category=:category");
				$criteria->params[":category"] = $_GET['category'];
			}
			if(!empty($_GET['lang_id'])) {
				$criteria->addCondition("m.language=:language");
				$criteria->params[":language"] = $_GET['lang_id'];
			}

			$builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
			$command = $builder->createFindCommand($m->sourceMessageTable, $criteria, 's');
			$count = $command->queryScalar();

			// Something has been found
			if($count > 0) {
				$criteria->select = 's.id, m.language, m.translation, s.message, s.category';
				$pagination = new CPagination($count);
				$pagination -> pageSize = 10;
				$pagination -> applyLimit($criteria);
				$command = $builder->createFindCommand($m->sourceMessageTable, $criteria, 's');
				$rows = $command->queryAll();
			}
		}

		$this->render("search", array(
			"rows" => isset($rows) ? $rows : null,
			"count" => isset($count) ? $count : null,
			"categories"=>Yii::app()->messages->getCategoryList(),
			"pagination" => isset($pagination) ? $pagination : null,
			"r" => Yii::app()->request,
			"q"=>$q,
		));
	}

	public function actionMissingTranslation() {
		$this->title=Yii::t("language", "Missing translations");
		$dataProvider=Yii::app()->messages->getMissingTranslationProvider();
		$this->render("missing_translation", array(
			"dataProvider"=>$dataProvider,
		));
	}

	public function actionTruncateMissingTranslation() {
		Yii::app()->messages->removeAllMissingTrans();
		Yii::app() -> user -> setFlash("success", Yii::t("notification", "All records have been removed"));
		$this ->redirect($this->createUrl("admin/language/missing-translation"));
	}

	public function actionDeleteMissingTranslation($id) { // id
		if(Yii::app()->messages->removeMissingTransByID($id)) {
			Yii::app() -> user -> setFlash("success", Yii::t("notification", "Record has been deleted"));
		}
		$referrer=Yii::app()->request->urlReferrer;
		$url=$referrer ? $referrer : $this->createUrl("admin/language/missing-translation");
		$this->redirect($url);
	}

	public function actionExport() {
		$this->title = Yii::t("language", "Export Translation");
		
		$scenario = 'export';
		$model = new ExportTranslationForm($scenario);

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['ExportTranslationForm'])) {
			$model->attributes = $_POST['ExportTranslationForm'];
			if($model->validate()) {
				if(!class_exists('ZipArchive')) {
					Yii::app()->user->setFlash("warning", Yii::t("language", "You need to install ZipArchive package"));
					$this->refresh();
				}

				$translationDir = Yii::app()->getRuntimePath(). DIRECTORY_SEPARATOR . "translation";

				if(!is_dir($translationDir)) {
					mkdir($translationDir);
					@chmod($translationDir, 0777);
				}

				$zip = new ZipArchive;
				$zipFile = $translationDir. DIRECTORY_SEPARATOR . 'translation.zip';

				$res = $zip->open($zipFile, ZipArchive::CREATE);
				if ($res !== TRUE) {
					Yii::app()->user->setFlash("warning", Yii::t("language", "Can't create temporary file"));
					$this->refresh();
				}

				$categories = Yii::app()->messages->getCategoriesByLang($model->language);
				if(empty($categories)) {
					Yii::app()->user->setFlash("warning", Yii::t("language", "The language must have at least one category"));
					$this->refresh();
				}

				foreach($categories as $category) {
					$trans = Yii::app()->messages->getTranslationByCategory($category, $model->language);
					if(!empty($model->trans_language)) {
						$into = Yii::app()->messages->getTranslationByCategory($category, $model->trans_language);
						foreach($trans as $id=>$row) {
							if(isset($into[$id])) {
								$trans[$id]['translate_into'] = $into[$id]['translation'];
							}
						}
					}

					ob_start();
					$fp = fopen("php://output", "w");
					$header = array("phrase", "source_".$model->language, "translation");
					fputcsv($fp, $header);
					foreach($trans as $t) {
						unset($t['id']);
						fputcsv($fp, $t);
					}
					fclose($fp);
					$csv = ob_get_contents();
					ob_end_clean();

					if(defined('ZipArchive::FL_ENC_UTF_8') AND version_compare(PHP_VERSION, '8.0.0', '>=')) {
                        $zip->addFromString($category.".csv", $csv, ZipArchive::FL_ENC_UTF_8);
                    } else {
                        $zip->addFromString($category.".csv", $csv);
                    }
				}
				if(!$zip->close()) {
					Yii::app()->user->setFlash("warning", Yii::t("language", "Can't close temporary file"));
					$this->refresh();
				}

				$filename = "translation_".(empty($model->trans_language) ? $model->language : $model->trans_language).".zip";

				// push to download the zip
				@header('Content-type: application/zip');
				@header('Content-Disposition: attachment; filename="'.$filename.'"');
				@header('Content-Length:'.filesize($zipFile));
				@readfile($zipFile);
				// remove zip file is exists in temp path
				unlink($zipFile);
				Yii::app()->end();
			}
		}

		$languages = CMap::mergeArray(
			array(''=>Yii::t("language", "Choose any language"), '-'=>'----------'),
			CHtml::listData(Language::model()->getList(false), 'id', array('Language', 'formatLanguage'))
		);

		$this->render("export", array(
			"model" => $model,
			"languages" =>$languages,
		));
	}

	public function actionImport() {
		$this->title = Yii::t("language", "Import Translation");

		$scenario = "import";
		$model = new ExportTranslationForm($scenario);

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['ExportTranslationForm'])) {
			$model->attributes = $_POST['ExportTranslationForm'];
			$model->zip = CUploadedFile::getInstance($model, 'zip');
			if($model->validate()) {
				$tmp = $model->zip->getTempName();
				$zip = new ZipArchive;
				$res = $zip->open($tmp);
				if ($res !== TRUE) {
					Yii::app()->user->appendFlash("warning", Yii::t("language", "Couldn't open zip archive"));
					$this->refresh();
				}
				$transaction = Yii::app() -> db -> beginTransaction();
				try {
                    if (version_compare(PHP_VERSION, '8.0.0', '<')) {
                        ini_set('auto_detect_line_endings', TRUE);
                    }
					for($i = 0; $i < $zip->numFiles; $i++) {
						$name = $zip->getNameIndex($i);
						$parts = explode('.', $name);
						$ext = end($parts);
						$category = implode(".", array_slice($parts, 0, count($parts) - 1));
						if(strtolower($ext) != "csv") {
							Yii::app()->user->appendFlash("warning", Yii::t("language", "Ignoring file {File} because it's not a csv file", array(
								"{File}"=>$name,
							)));
							continue;
						}
						$fp = $zip->getStream($name);
						$translations = array();

						if(!$fp) {
							Yii::app()->user->appendFlash("warning", Yii::t("language", "Unable to create stream from {File} file", array(
								"{File}"=>$name,
							)));
							continue;
						}

						while(!feof($fp)) {
							$translations[] = fgetcsv($fp);
						}
						// Remove header
						array_shift($translations);
						// 0 - phrase; 1 - source; 2 - translated phrase
						foreach($translations as $row=>$translation) {
							if(!isset($translation[0], $translation[1], $translation[2])) {
								Yii::app()->user->appendFlash("warning", Yii::t("language", "Invalid format of {Row} row in {Category} category", array(
									"{Row}"=>$row,
									"{Category}"=>$name,
								)));
								continue;
							}
							$phrase = trim($translation[0]);
							$lang = $model->language;
							$source = trim($translation[1]);
							$translated = trim($translation[2]);
							
							$dbSource = Yii::app()->messages->getSourceMessage($phrase, $category);
							// IF source has been found
							if(!empty($dbSource)) {
								Yii::app()->messages->translateSourceMessage($dbSource['id'], $lang, $translated, $model->force);
							} else {
								Yii::app()->messages->saveSourceMessage($category, $phrase);
								$sourceId = Yii::app()->db->getLastInsertID();
								Yii::app()->messages->translateSourceMessage($sourceId, $lang, $translated, $model->force);
							}
						}
					}
                    if (version_compare(PHP_VERSION, '8.0.0', '<')) {
                        ini_set('auto_detect_line_endings', FALSE);
                    }
					$transaction->commit();
					Yii::app()->user->appendFlash("success", Yii::t("language", "Translations for {Language} language have been imported", array(
						"{Language}"=>strtoupper($model->language),
					)));
					$this->refresh();
				} catch(Exception $e) {
					$transaction -> rollback();
                    Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
					Yii::log($e->getMessage(), 'error', 'application.admin.language.import');
					$this->refresh();
				}
			}
		}

		$languages = CMap::mergeArray(
			array(''=>Yii::t("language", "Choose any language"), '-'=>'----------'),
			CHtml::listData(Language::model()->getList(false), 'id', array('Language', 'formatLanguage'))
		);

		$this->render("import", array(
			"model" => $model,
			"languages" =>$languages,
		));
	}

	public function onMessageUpdated(CEvent $event) {
		$this->raiseEvent('onMessageUpdated', $event);
	}

	public function onMessageTranslated(CEvent $event) {
		$this->raiseEvent('onMessageTranslated', $event);
	}

	public function onMessageDeleted(CEvent $event) {
		$this->raiseEvent('onMessageDeleted', $event);
	}

	public function onMessageCreated(CEvent $event) {
		$this->raiseEvent('onMessageCreated', $event);
	}

	public function onCategoryDeleted(CEvent $event) {
		$this->raiseEvent('onCategoryDeleted', $event);
	}

	public function onCategoryUpdated(CEvent $event) {
		$this->raiseEvent('onCategoryUpdated', $event);
	}

	public function onCategoryCreated(CEvent $event) {
		$this->raiseEvent('onCategoryCreated', $event);
	}

	public function onLanguageCreated(CEvent $event) {
		$this->raiseEvent('onLanguageCreated', $event);
	}

	public function onLanguageEnabled(CEvent $event) {
		$this->raiseEvent('onLanguageEnabled', $event);
	}

	public function onLanguageDisabled(CEvent $event) {
		$this->raiseEvent('onLanguageDisabled', $event);
	}

	public function onLanguageDeleted(CEvent $event) {
		$this->raiseEvent('onLanguageDeleted', $event);
	}

	public function onLanguageUpdated(CEvent $event) {
		$this->raiseEvent('onLanguageUpdated', $event);
	}
}


