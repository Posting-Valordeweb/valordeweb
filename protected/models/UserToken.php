<?php
class UserToken extends CActiveRecord {
	const TYPE_EMAILVERIFICATION = 0;
	const TYPE_RESETPASSWORD = 1;

	const STATUS_NEW = 0;
	const STATUS_ACTIVE = 1;

	/**
	* @param string $className
	* @return User instance
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{user_token}}';
	}

	public function create(User $user, $expire, $type) {
		$now = date("Y-m-d H:i:s");
		$expired = date("Y-m-d H:i:s", time() + $expire);
		$this->user_id = $user->id;
		$this->ip = Yii::app()->getRequest()->getUserHostAddress();
		$this->created_at = $now;
		$this->expired_at = $expired;
		$this->token = Hasher::generateToken();
		$this->status = self::STATUS_NEW;
		$this->type = $type;
		if($this->save()) {
			return $this;
		}
		return false;
	}

	public function crateEmailActivation(User $user, $expire=86400) { // One day
		return $this->create($user, $expire, self::TYPE_EMAILVERIFICATION);
	}

	public function createResetPassword(User $user, $expire=86400) { // One day
		return $this->create($user, $expire, self::TYPE_RESETPASSWORD);
	}

	public function isExpired() {
		return $this->expired_at < date("Y-m-d H:i:s");
	}

	public function isActivated() {
		return $this->status != self::STATUS_NEW;
	}

	public function get($token, $type, $status=UserToken::STATUS_NEW) {
		$token = UserToken::model()->find('token=:token AND type=:type AND status=:status', array(
			':token'=>$token,
			':type'=>$type,
			':status'=>$status,
		));
		return ($token AND !$token->isExpired() AND !$token->isActivated()) ? $token : null;
	}

	public function rules() {
		return array(
			array('user_id, ip, created_at, expired_at, token, status, type', 'required'),
		);
	}

	public function relations() {
		return array(
			'user'=> array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}
}