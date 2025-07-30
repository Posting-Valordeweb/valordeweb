<?php
class PostEvent {
	public static function sendEmailOnMessageSent($event) {
		unset($_GET['owner']);
		$sender=$event->sender;
		$params=$event->params;

        $mail = new YiiMailer();
        $subject = Yii::t("mail", "You got new message!", array(), null, $params['receiver']->lang_id);
        $mail->setSubject($subject);
        $mail->setFrom(Yii::app()->params['notification.email'], Yii::app()->params['notification.name']);
        $mail->clearReplyTos();
        $mail->setTo($params['receiver']->email);
        $mail->setView('post/new_message');
        $mail->setData(array(
            "mailer"=>$mail,
            "name"=>Helper::mb_ucfirst($params['receiver']->username),
            "fromName"=>Helper::mb_ucfirst($sender->owner->username),
            "user"=>$params['receiver'],
            "messageLink"=>Yii::app()->controller->createAbsoluteUrl("post/chain", array("id" => $params['receiver_header_id'])),
        ));
        $mail->setAltText(Yii::t('notification', 'Please, use mail client which support HTML markup'));
        $mail->send();
	}

	public static function bindWebsite($event) {
		$chain_id=$event->params['chain_id'];
		$bind=new BindWebsite;
		$bind->chain_id=$chain_id;
		$bind->website_id=Yii::app()->controller->website_id;
		$bind->save();
	}

}