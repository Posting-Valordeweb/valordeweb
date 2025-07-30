<?php
class SaleEvent {
	public static function removeFromSale($id) {
		if(Yii::app()->db->getCurrentTransaction()===null) {
			$transaction=Yii::app()->db->beginTransaction();
		}
		try {
			Sale::model()->deleteByPk($id);
			BindWebsite::model()->deleteAllByAttributes(array(
				"website_id"=>$id,
			));
			SaleChecker::model()->deleteByPk($id);
			if(isset($transaction))
				$transaction->commit();
			return true;
		} catch(Exception $e) {
			if(isset($transaction)) {
				$transaction->rollback();
				Yii::log($e->getMessage(), 'error', 'application.sale_event.remove_from_sale');
				return false;
			}
			throw $e;
		}
	}
}