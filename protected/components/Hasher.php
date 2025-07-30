<?php
class Hasher {
	public static function generateSalt() {
		return str_shuffle(md5(uniqid(mt_rand(), true).sha1(microtime(true))));
	}

	public static function hashPassword($password, $salt) {
		return md5($salt.$password);
	}

	public static function generateToken() {
		return md5(self::generateSalt().microtime(true));
	}

	public static function isPasswordMatched($formPassword, $userPassword, $salt) {
		return self::hashPassword($formPassword, $salt) == $userPassword;
	}
}