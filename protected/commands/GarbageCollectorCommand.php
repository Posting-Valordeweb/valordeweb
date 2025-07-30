<?php
class GarbageCollectorCommand extends CConsoleCommand {
    public function actionIndex() {
        // Delete all expired tokens
        $criteria=new CDbCriteria();
        $criteria->condition="expired_at<:current_date";
        $criteria->params=array(":current_date"=>date("Y-m-d H:i:s"));
        UserToken::model()->deleteAll($criteria);

        // Remove all deleted (expired) messages
        Yii::app()->innerMail->removeExpiredMessages();
        return 0;
    }
}