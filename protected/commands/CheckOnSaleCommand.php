<?php
/**
Error code:
101. Transaction failed
*/

class CheckOnSaleCommand extends CConsoleCommand {
	public function actionIndex() {
		$onSale=Sale::model()->with(array(
			"website"=>array(
				"select"=>"domain",
			),
		))->findAll(array(
			"select"=>"website_id",
		));
		$transaction=Yii::app()->db->beginTransaction();
		try {
			foreach($onSale as $sale) {
				if($sale->website===null) {
					continue;
				}
				if(Helper::checkDoFollowLink($sale->website->domain)) {
					SaleChecker::model()->deleteByPk($sale->website_id);
					continue;
				}
				$checker=SaleChecker::model()->findByPk($sale->website_id);
				if(null===$checker) {
					$checker=new SaleChecker;
					$checker->website_id=$sale->website_id;
					$checker->attempts=1;
					$checker->save(false);
					continue;
				}
				if($checker->attempts >= Yii::app()->params["site_cost.on_sale_validation_limit"]) {
					SaleEvent::removeFromSale($sale->website_id);
					continue;
				}
				$checker->saveCounters(array('attempts'=>1));
			}
			$transaction->commit();

		} catch(Exception $e) {
			$transaction->rollback();
			Yii::log($e->getMessage(), 'error', 'application.command.check_on_sale.index');
			return 1;
		}
		return 0;
	}
}