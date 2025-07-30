<?php
class Website extends CActiveRecord {
	/**
	* @param string $className
	* @return Website
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{webdata_main}}';
	}

	public function relations() {
		return array(
			'alexa'=>array(self::HAS_ONE, 'WebdataAlexa', 'wid'),
			'antivirus'=>array(self::HAS_ONE, 'WebdataAntivirus', 'wid'),
			'catalog'=>array(self::HAS_ONE, 'WebdataCatalog', 'wid'),
			'location'=>array(self::HAS_ONE, 'WebdataLocation', 'wid'),
			'meta_tags'=>array(self::HAS_ONE, 'WebdataMetaTags', 'wid'),
			'search_engine'=>array(self::HAS_ONE, 'WebdataSearchEngine', 'wid'),
			'social'=>array(self::HAS_ONE, 'WebdataSocial', 'wid'),
			'whois'=>array(self::HAS_ONE, 'WebdataWhois', 'wid'),
			'moz'=>array(self::HAS_ONE, 'WebdataMoz', 'wid'),
			'sale'=>array(self::HAS_ONE, 'Sale', 'website_id'),
			'sale_stat'=>array(self::STAT, 'Sale', 'website_id'),

		);
	}

	public function beforeSave() {
		if(parent::beforeSave()) {
			$now = date("Y-m-d H:i:s");
			if($this->isNewRecord) {
				$this -> added_at = $now;
				$this -> modified_at = $now;
				$this->md5domain = md5($this->domain);
			} else {
				$this -> modified_at = $now;
			}
			return true;
		} else {
			return false;
		}
	}

	public $sale_search='-1';
	public function search() {
		$criteria=new CDbCriteria;
		$criteria->order = 't.added_at DESC';

		$criteria->compare('t.id', $this->id);
		/*$domain=idn_to_ascii(Helper::trimDomain($this->domain));
		if(!empty($domain)) {
			$criteria->compare('t.md5domain', md5($domain));
		}*/
        if(!empty($this->domain)) {
            $criteria->compare('t.idn', $this->domain, true);
        }
		$criteria->compare('t.price', $this->price, true);

		if((string)$this->sale_search==='1') {
			$criteria->compare('sale.website_id', '>0');
		} elseif((string)$this->sale_search==='0') {
			$criteria->condition ='sale.website_id IS NULL';
		}

		$criteria->with = array(
			"sale"=>array(
				"select"=>"sale.website_id",
			),
		);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => 30,
            ),
		));
	}

	public function rules() {
		return array(
			array('price, domain, idn', 'required'),

			// Search
			array('id, domain, price, sale_search', 'safe', 'on' => 'search'),
		);
	}

	public function attributeLabels() {
		return array(
			"id"=>Yii::t("misc", "ID"),
			"domain"=>Yii::t("website", "Domain"),
			"idn"=>Yii::t("website", "Domain"),
			"added_at"=>Yii::t("misc", "Added at"),
			"modified_at"=>Yii::t("user", "Modified at"),
			"price"=>Yii::t("website", "Estimate Price"),
			"sale_search"=>Yii::t("website", "Is On Sale"),
		);
	}
}