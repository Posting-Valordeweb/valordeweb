<?php
class WebsiteList extends Widget {
	public $config = array();
	public $template="website_list";

	public function init() {
		$config = array(
			"criteria"=>array(
				"select"=>"t.id, t.domain, t.idn, t.price",
				"with"=>array(
					"search_engine" => array(
						"select"=>"google_index, bing_index, yahoo_index, google_backlinks",
					),
					"social" => array(
						"select"=>"facebook_total_count",
					),
                    "antivirus"=>array(
                        "select"=>"avg",
                    ),
				),
				"order"=>"t.added_at DESC",
			),
			"countCriteria"=>array(),
			"pagination" => array(
				"pageVar"=>"page",
				"pageSize"=>Yii::app()->params['site_cost.websites_per_page'],
			)
		);
		$this->config = CMap::mergeArray($config, $this->config);
	}

	public function run() {
		$dataProvider=new CActiveDataProvider('Website', $this->config);
        $data=$dataProvider->getData();
        if(empty($data)) {
            return null;
        }
		$thumbnailStack=WebsiteThumbnail::thumbnailStack($data, array(
            'size'=>'l',
        ));
		$this->render($this->template, array(
			"dataProvider" => $dataProvider,
			"thumbnailStack"=>$thumbnailStack,
            "data"=>$data,
		));
	}
}