<?php
class UserEvent {
	public static function sendTokenOnEmail($event) {
		$user = $event->params["user"];
		$userToken = new UserToken;
		if(!$userToken->crateEmailActivation($user)) {
			throw new CException("Unable to create user token");
		}

        $mail = new YiiMailer();

		$subject = Yii::t('user', 'Registration at {InstalledUrl}', array(
            '{InstalledUrl}'=> Helper::getBrandUrl(),
        ));
        $mail->setSubject($subject);
        $mail->setFrom(Yii::app()->params['notification.email'], Yii::app()->params['notification.name']);
        $mail->clearReplyTos();
        $mail->setTo($user->email);
        $mail->setView('user/register');
        $mail->setData(array(
            "mailer"=>$mail,
            "name"=>Helper::mb_ucfirst($user->username),
            "user"=>$user,
            "verifyUrl"=>$event->sender->createAbsoluteUrl("user/confirm", array("t" => $userToken->type, "token"=>$userToken->token)),
        ));
        $mail->setAltText(Yii::t('notification', 'Please, use mail client which support HTML markup'));
        $mail->send();
	}

	public static function createUserServer($event) {
		$user=$event->params["user"];
		$server_id=Yii::app()->innerMail->box($user)->generateUserBoxID();
		$user->post_server_id=$server_id;
		if(!$user->save(false)) {
			throw new CException("Unable to save post user server");
		}
	}

	public static function garbageCollector($event) {
		$user=$event->params['user'];
		UserToken::model()->deleteAllByAttributes(array(
			"user_id"=>$user->id,
		));
		$onSale=Sale::model()->findAllByAttributes(array(
			"user_id"=>$user->id
		));
		foreach($onSale as $sale) {
			SaleEvent::removeFromSale($sale->website_id);
		}
	}

}