<?php
class User extends CActiveRecord {
	const STATUS_ACTIVE = 1;
	const STATUS_BLOCKED = 0;
	const STATUS_DELETED = 2;

	const ROLE_USER = 'user';
	const ROLE_ADMIN = 'administrator';
	const ROLE_ROOT = 'root';

	const EMAIL_CONFIRMED = 1;
	const EMAIL_NOTCONFIRMED = 0;

	const ALLOW_MESSAGE = 1;
	const DISALLOW_MESSAGE = 0;

	/**
	* @param string $className
	* @return User instance
	*/
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function relations() {
		return array(
			'tokens'=>array(self::HAS_MANY, 'UserToken', 'user_id'),
			'onsale'=>array(self::HAS_MANY, 'Sale', 'user_id'),
		);
	}

	public function tableName() {
		return '{{user}}';
	}

	public function isActive() {
		return !in_array($this->status, array(self::STATUS_BLOCKED, self::STATUS_DELETED));
	}

	public static function getStatusList() {
		return array(
			self::STATUS_ACTIVE => Yii::t("user", 'Active'),
			self::STATUS_BLOCKED => Yii::t("user", 'Blocked'),
			self::STATUS_DELETED => Yii::t("user", 'Deleted'),
		);
	}

	public function getStatusCSS() {
		switch($this->status) {
			case self::STATUS_BLOCKED:
				return 'warning';
			break;
			case self::STATUS_DELETED:
				return 'danger';
			break;
			default:
				return null;
			break;
		}
	}

	public static function getRoleList() {
		return array(
			self::ROLE_USER => Yii::t("user", 'User'),
			self::ROLE_ADMIN => Yii::t("user", 'Administrator'),
            self::ROLE_ROOT => Yii::t("user", 'Root'),
		);
	}

	public function getRoleMessage() {
		switch($this->role) {
			case self::ROLE_USER:
				return Yii::t("user", 'User');
			break;
			case self::ROLE_ADMIN:
				return Yii::t("user", 'Administrator');
			break;
			case self::ROLE_ROOT:
				return Yii::t("user", 'Root');
			break;
			default:
				return Yii::t("user", 'Unknown');
			break;
		}
	}

	public function getStatusMessage() {
		switch($this->status) {
			case self::STATUS_ACTIVE:
				return Yii::t("user", 'Active');
			break;
			case self::STATUS_BLOCKED:
				return Yii::t("user", 'Blocked');
			break;
			case self::STATUS_DELETED:
				return Yii::t("user", 'Deleted');
			break;
			default:
				return null;
			break;
		}
	}

	public function beforeSave() {
		if(parent::beforeSave()) {
			$now = date("Y-m-d H:i:s");
			if($this->isNewRecord) {
				$this->registered_at = $now;
				$this->modified_at = $now;
				$this->last_login_at = $now;
				$this->ip = $this->last_ip_login = Yii::app()->getRequest()->getUserHostAddress();
			} else {
				$this->modified_at = $now;
			}
			return true;
		} else {
			return false;
		}
	}

	public function beforeDelete() {
		return false;
	}

	public function hasConfirmedEmail() {
		return $this->email_confirmed == self::EMAIL_CONFIRMED;
	}

	public function canSendMessage() {
		return $this->can_send_message == self::ALLOW_MESSAGE;
	}

	public function isSuperUser() {
		return $this->role == self::ROLE_ROOT;
	}

	public function isAdministrator()
    {
        return $this->role == self::ROLE_ADMIN;
    }

    public function isSimpleUser()
    {
        return $this->role == self::ROLE_USER;
    }

	public function scopes() {
		return array(
			'adminIndex' => array(
				'condition'=>'role NOT IN(:root)',
				'params' => array(':root' => self::ROLE_ROOT),
				'order'=>'registered_at DESC',
			),
			'active'=>array(
				'condition'=>'status=:status',
				'params'=>array(':status'=>self::STATUS_ACTIVE),
			),
			'confirmed'=>array(
				'condition'=>'email_confirmed=:email_confirmed',
				'params'=>array(':email_confirmed'=>self::EMAIL_CONFIRMED),
			),
		);
	}

	public $password2;
	public $verifyCode;
    public $agree;
	public function rules() {
	    $rules = array();
	    $rules[] = array('email, username', 'required');
        $rules[] = array('agree', 'required', 'except'=>array('adminCreate', 'adminUpdate'));
	    $rules[] = array('email_confirmed, role, can_send_message, status, lang_id', 'required', 'on'=>array('adminCreate', 'adminUpdate'));
        // Password required only on create
	    $rules[] = array('password, password2', 'required', 'except'=>array('adminUpdate'));
	    $rules[] = array('password, password2', 'length', 'min' => 5, 'except'=>array('adminUpdate'));
	    $rules[] = array('password2', 'compare', 'compareAttribute' => 'password', 'except'=>array('adminUpdate'), 'message' => Yii::t("user", "Passwords do not match"));

        if(Helper::isAllowedCaptcha() AND !$this->isAdminScenario($this->getScenario())) {
            $rules[] = array('verifyCode', 'ext.recaptcha2.ReCaptcha2Validator', 'privateKey'=>Yii::app()->params['recaptcha.private'], 'message'=>Yii::t("yii", "The verification code is incorrect."));
        }
        $rules[] = array('email', 'length', 'max' => 80);
        $rules[] = array('username', 'length', 'max' => 30);
        $rules[] = array('email', 'email');
        $rules[] = array('email, username', 'unique');

        // Admin create
        $rules[] = array('email_confirmed', 'in', 'range' => array(self::EMAIL_NOTCONFIRMED, User::EMAIL_CONFIRMED), 'on'=>array('adminCreate', 'adminUpdate'));
        $rules[] = array('role', 'in', 'range' => array_keys(self::getRoleList()), 'on'=>array('adminCreate', 'adminUpdate'));
        $rules[] = array('can_send_message', 'in', 'range' => array(self::DISALLOW_MESSAGE, User::ALLOW_MESSAGE), 'on'=>array('adminCreate', 'adminUpdate'));
        $rules[] = array('status', 'in', 'range' => array_keys(self::getStatusList()), 'on'=>array('adminCreate', 'adminUpdate'));
        $rules[] = array('lang_id', 'existsLanguage', 'on'=>array('adminCreate', 'adminUpdate'));

        // Search
        $rules[] = array('id, email, username, registered_at, status, ip, role, email_confirmed', 'safe', 'on' => 'search');

        return $rules;
	}

    public function existsLanguage() {
        if($this -> hasErrors()) {
            return false;
        }
        if(!Language::model()->issetLang($this->lang_id)) {
            $this->addError("language", Yii::t("language", "The language {Language} doesn't exists in the system", array(
                "{Language}" => "<strong>".$this->language."</strong>",
            )));
        }
    }

	public function attributeLabels() {
		return array(
			"id"=>Yii::t("misc", "ID"),
			"email"=>Yii::t("user", "Email"),
			"username"=>Yii::t("user", "Username"),
			"email_confirmed"=>Yii::t("user", "Email confirmation"),
			"role"=>Yii::t("user", "User role"),
			"can_send_message"=>Yii::t("user", "Can send message"),
			"status"=>Yii::t("user", "User status"),
			"password"=>Yii::t("user", "Password"),
			"password2"=>Yii::t("user", "Re-Password"),
			"registered_at"=>Yii::t("user", "Registered at"),
			"modified_at"=>Yii::t("user", "Modified at"),
			"last_login_at"=>Yii::t("user", "Last login at"),
			"ip"=>Yii::t("website", "IP Address"),
			"last_ip_login"=>Yii::t("user", "Last login IP Address"),
			"lang_id"=>Yii::t("misc", "Language"),
            "verifyCode"=>Yii::t("user", "Verification code"),
            "agree"=>Yii::t("user", "I have read and accept the {Terms and Conditions}", array(
                "{Terms and Conditions}" => CHtml::link(Yii::t("misc", "page_terms_link"), Yii::app()->createUrl("site/terms"), array("target"=>"_blank")),
            ))
		);
	}

	public function search() {
		$criteria=new CDbCriteria;
		$criteria -> order = 'registered_at DESC';
		$criteria -> compare('id', $this -> id);
		$criteria -> compare('username', $this -> username, true);
		$criteria -> compare('email', $this -> email, true);
		$criteria -> compare('role', $this -> role);
		$criteria -> compare('status', $this -> status);
		return new CActiveDataProvider($this->adminIndex(), array(
			'criteria'=>$criteria,
		));
	}

	public function isAdminScenario($scenario)
    {
        return in_array($scenario, array("adminCreate", "adminUpdate"));
    }
}