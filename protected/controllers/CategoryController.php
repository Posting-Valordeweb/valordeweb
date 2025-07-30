<?php
class CategoryController extends FrontController {
	public function sortCat($a, $b) {
		return strcmp($a->getTranslation(), $b->getTranslation());
	}

	public function actionIndex() {
		$categories = Category::model()->cache(60*60*24*30)->with(array(
			"onsaleCount",
			"translations"=>array(
				'scopes'=>array('current_lang'),
			),
		))->findAll();

		usort($categories, array($this, 'sortCat'));

		$activeCat = 0;
		$slug = Yii::app()->request->getQuery('slug');

		$order = $this->getOrderBy();
		$sort = $this->getSortOrder();
		$page = (int) Yii::app()->request->getQuery('page', 1);
		if(!empty($slug)) {
			$category=null;
			foreach($categories as $cat) {
				if(mb_strtolower($cat->slug)==mb_strtolower($slug)) {
					$category=$cat;
					break;
				}
			}
			if(!$category) {
				throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
			}
			$activeCat = $category->id;
			$this->title = Yii::t("category", "Websites in the category {Category}. Page {PageNr}", array(
                "{Category}"=>$category->getTranslation(),
                "{PageNr}"=>$page,
            ));
		} else {
			$this->title = Yii::t("category", 'Websites on sale. Page {PageNr}', array(
                "{PageNr}"=>$page,
            ));
		}

		$criteria = new CDbCriteria;
		if($activeCat) {
			$criteria->condition = "category_id=:category_id";
			$criteria->params = array(":category_id"=>$activeCat);
		}
		$criteria->order= $order .' '. $sort;
		$criteria->with=array(
				"website"=>array(
						"select"=>"t.id, t.domain, t.price, t.idn",
						"with"=>array(
								"search_engine" => array(
										"select"=>"page_rank",
								),
								"alexa" => array(
										"select"=>"rank",
								),
								"social" => array(
										"select"=>"facebook_like_count",
								)
						),
				)
		);

		$dataProvider=new CActiveDataProvider('Sale', array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageVar'=>'page',
				'pageSize' => Yii::app()->params['site_cost.on_sale_per_page'],
			),
		));

		$data = $dataProvider->getData();

		$pagination = $dataProvider->getPagination();
		$total = $dataProvider->getTotalItemCount();

		$summaryText=Helper::getSummaryText($pagination->currentPage+1, $total, Yii::app()->params['site_cost.category_per_page']);

        $websiteStack=array();
        $i=0;
        foreach($data as $onSale) {
            $websiteStack[$i]['id']=$onSale->website->id;
            $websiteStack[$i]['domain']=$onSale->website->domain;
            $i++;
        }

		$this->render("website_sale", array(
			"data" => $data,
			"activeCat" => $activeCat,
			"categories" => $categories,
			"pagination" => $pagination,
			"summaryText" => $summaryText,
			"order"=>$order,
			"sort"=>$sort,
			"thumbnailStack"=>WebsiteThumbnail::thumbnailStack($websiteStack),
		));
	}

	protected function getOrderBy() {
		$order = Yii::app()->request->getQuery('order');
		$o = array("added_at", "price");
		return in_array($order, $o) ? "t.".$order : "t.added_at";
	}

	protected function getSortOrder() {
		$sort = Yii::app()->request->getQuery('sort');
		$s = array("asc", "desc");
		return in_array($sort, $s) ? $sort : "desc";
	}

}