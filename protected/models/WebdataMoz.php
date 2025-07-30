<?php

/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 2016.05.22
 * Time: 16:53
 */
class WebdataMoz extends CActiveRecord
{
    /**
     * @param string $className
     * @return WebdataMoz
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{webdata_moz}}';
    }

    public function rules() {
        return array(
            array('wid, pda, upa, uid', 'required'),
        );
    }

    public function attributeLabels() {
        return array(
            "wid"=>Yii::t("misc", "ID"),
            "pda"=>Yii::t("website", "Domain Authority"),
            "upa"=>Yii::t("website", "Page Authority"),
            "uid"=>Yii::t("website", "MOZ Links"),
        );
    }
}