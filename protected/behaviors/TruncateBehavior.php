<?php
class TruncateBehavior extends CActiveRecordBehavior {
	public function truncate() {
	 return $this -> owner -> getDbConnection() -> createCommand() -> truncateTable($this -> owner -> tableName());
	}
}