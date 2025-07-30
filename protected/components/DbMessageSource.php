<?php
class DbMessageSource extends CDbMessageSource {
	public $missingTranslationTable = '{{trans_missing}}';

	public function findAllInCategory($category_id) {
		$result = array();
		$dataReader = Yii::app()->db->createCommand()
			-> select('*')
			-> from($this->sourceMessageTable)
			-> where("category=:category", array(":category"=>$category_id))
			-> query();
		foreach($dataReader as $row) {
			$result[$row['id']] = $row;
		}
		return $result;
	}

	public function issetSourceMessage($message, $category, $id = null) {
		// If ID passed, then we should exclude it from query
		if($id !== null) {
			$condition = "id!=:id AND category = :category AND message = :message";
			$params = array(":category"=>$category, ":message"=>$message, ":id"=>$id);
		} else {
			$condition = "category = :category AND message = :message";
			$params = array(":category"=>$category, ":message"=>$message);
		}
		return Yii::app()->db->createCommand()
			-> select('count(*)')
			-> from($this->sourceMessageTable)
			-> where($condition, $params)
			-> limit(1)
			-> queryScalar();
	}
	
	public function getSourceMessage($message, $category) {
		return Yii::app()->db->createCommand()
			-> select('*')
			-> from($this->sourceMessageTable)
			-> where("category = :category AND message = :message", array(":category"=>$category, ":message"=>$message))
			-> queryRow();
	}

	public function saveSourceMessage($category, $message) {
		$sql = "INSERT INTO {$this->sourceMessageTable} (`category`, `message`) VALUES (:category, :message)";
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(":category", $category, PDO::PARAM_STR);
		$command->bindParam(":message", $message, PDO::PARAM_STR);
		return $command->execute();
	}

	public function updateSourceMessage($rows, $id) {
		return Yii::app()->db->createCommand()
			-> update($this->sourceMessageTable, $rows, 'id=:id', array(':id'=>$id));
	}

	public function findSourceMessageByPk($id) {
		return Yii::app()->db->createCommand()->select("*")->from($this->sourceMessageTable)->where("id=:id", array(":id"=>$id))->queryRow();
	}

	public function issetSourceMessageByPk($id) {
		return Yii::app()->db->createCommand()->select("count(*)")->from($this->sourceMessageTable)->where("id=:id", array(":id"=>$id))->queryScalar();
	}
	
	public function getTranslationsBySourceID($id) {
		$result = array();
		$dataReader = Yii::app()->db->createCommand()
			-> select('*')
			-> from($this->translatedMessageTable)
			-> where("id=:id", array(":id"=>$id))
			-> query();
		foreach($dataReader as $row) {
			$result[$row['language']] = $row;
		}
		return $result;
	}

	public function translateSourceMessage($id, $language, $translation, $force=false) {
		$bind=$force ? ":" : null;
		$sql = "INSERT INTO {$this->translatedMessageTable} (`id`, `language`, `translation`) VALUES (:id, :language, :translation)
						ON DUPLICATE KEY UPDATE translation={$bind}translation";
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(":id", $id, PDO::PARAM_INT);
		$command->bindParam(":language", $language, PDO::PARAM_STR);
		$command->bindParam(":translation", $translation, PDO::PARAM_STR);
		return $command->execute();
	}

	public function getTranslationByCategory($category, $language) {
		$data = array();
		$dataReader = Yii::app()->db->createCommand()
			-> select("s.id, s.message, m.translation")
			-> from("{$this->sourceMessageTable} as s")
			-> leftJoin("{$this->translatedMessageTable} as m", "m.id=s.id")
			-> where("s.category=:category AND language=:language", array(":category"=>$category, ":language"=>$language))
			-> query();
		foreach($dataReader as $row) {
			$data[$row['id']] = $row;
		}
		return $data;
	}

	public function getCategoryList() {
		$result = array();
		$dataReader = Yii::app()->db->createCommand()
			-> select("category")
			-> from($this->sourceMessageTable)
			-> group("category")
			-> order("category")
			-> query();
		foreach($dataReader as $row) {
			$result[$row['category']] = $row['category'];
		}
		return $result;
	}

	public function allowToUpdCategory($oldName, $newName) {
		return Yii::app()->db->createCommand()
			-> select("count(*)")
			-> from($this->sourceMessageTable)
			-> where("category=:newName AND category!=:oldName", array(":newName"=>$newName, ":oldName"=>$oldName))
			-> queryScalar();
	}

	public function issetCategory($category) {
		return Yii::app()->db->createCommand()
			-> select("count(*)")
			-> from($this->sourceMessageTable)
			-> where("category=:category", array(":category"=>$category))
			-> queryScalar();
	}

	public function updateCategoryName($oldName, $newName) {
		$sql = "UPDATE {$this->sourceMessageTable} SET category=:newName WHERE category=:oldName";
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(":newName", $newName, PDO::PARAM_STR);
		$command->bindParam(":oldName", $oldName, PDO::PARAM_STR);
		return $command->execute();
	}

	public function getCategoriesByLang($language) {
		$result = array();
		$dataReader = Yii::app()->db->createCommand()
			-> select("s.category")
			-> from("{$this->sourceMessageTable} as s")
			-> leftJoin("{$this->translatedMessageTable} as m", "m.id=s.id")
			-> where("m.language=:language", array(":language" => $language))
			-> group("s.category")
			-> order("s.category")
			-> query();
		foreach($dataReader as $row) {
			$result[] = $row['category'];
		}
		return $result;
	}

	public function removeTranslationBySourceID($id) {
	    $this->getDbConnection()
            ->createCommand()
            ->delete($this->sourceMessageTable, "id=:id", array(':id'=>$id));
        $this->getDbConnection()
            ->createCommand()
            ->delete($this->translatedMessageTable, "id=:id", array(':id'=>$id));
		return 1;
	}

	public function removeTranslationByLangID($lang_id) {
		return Yii::app()->db->createCommand()->delete($this->translatedMessageTable, 'language=:language', array(':language'=>$lang_id));
	}

	public function removeAllFromCategory($category) {
	    $ids = $this->getDbConnection()
            ->createCommand()
            ->select(["id"])
            ->from($this->sourceMessageTable)
            ->where("category=:category", array(":category"=>$category))
            ->queryColumn();

        $this->getDbConnection()
            ->createCommand()
            ->delete($this->sourceMessageTable, "category=:category", array(':category'=>$category));
        $this->getDbConnection()
            ->createCommand()
            ->delete($this->translatedMessageTable, ["in", "id", $ids]);

		return 1;
	}

	public function copyLanguage($from, $to) {
		$sql = "INSERT INTO {$this->translatedMessageTable} (id, language, translation)
						SELECT id, :to, translation FROM {$this->translatedMessageTable} as m
						WHERE m.language=:from";
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(":from", $from, PDO::PARAM_STR);
		$command->bindParam(":to", $to, PDO::PARAM_STR);
		return $command->execute();
	}

	public function onMissingTranslation($event) {
		$sql = "INSERT IGNORE {$this->missingTranslationTable} (`category`, `key`, `lang_id`, `inserted_at`) VALUES (:category, :key, :lang_id, :datetime)";
		$datetime = date("Y-m-d H:i:s");
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(":category", $event->category, PDO::PARAM_STR);
		$command->bindParam(":key", $event->message, PDO::PARAM_STR);
		$command->bindParam(":lang_id", $event->language, PDO::PARAM_STR);
		$command->bindParam(":datetime", $datetime, PDO::PARAM_STR);
		$command->execute();
	}

	public function totalMissingTranslations() {
		$sql="SELECT COUNT(*) FROM {$this->missingTranslationTable}";
		return Yii::app()->db->createCommand($sql)->queryScalar();
	}

	public function getMissingTranslationProvider() {
		$count=$this->totalMissingTranslations();
		$sql="SELECT * FROM {$this->missingTranslationTable}";
		$dataProvider=new CSqlDataProvider($sql, array(
			'totalItemCount'=>$count,
			'pagination'=>array(
				'pageSize'=>20,
			),
		));
		return $dataProvider;
	}

	public function removeAllMissingTrans() {
		$sql="TRUNCATE {$this->missingTranslationTable}";
		return Yii::app()->db->createCommand($sql)->execute();
	}

	public function removeMissingTransByID($id) {
		$sql = "DELETE FROM {$this->missingTranslationTable} WHERE id=:id";
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(":id", $id, PDO::PARAM_INT);
		return $command->execute();
	}

}