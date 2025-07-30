<?php
class UserCommand extends CConsoleCommand
{
    public function actionChangePassword($email, $password)
    {
        $user = User::model()->findByAttributes(array(
            "email"=>$email
        ));
        if(!$user) {
            echo "Unable to find $email\n";
            return;
        }

        $user->salt = Hasher::generateSalt();
        $user->password = Hasher::hashPassword($password, $user->salt);
        $user->save(false);

        echo "Password has been changed\n";
    }

    public function actionFindRoots()
    {
        $roots = User::model()->findAllByAttributes(array(
            "role"=>User::ROLE_ROOT
        ));
        foreach ($roots as $root) {
            echo $root->email. "\n";
        }
    }

    public function actionCreateRoot($username, $email, $password)
    {
        try {
            $user = new User();
            $user->salt = Hasher::generateSalt();
            $user->password = Hasher::hashPassword($password, $user->salt);
            $user->email = $email;
            $user->lang_id = Yii::app()->language;
            $user->role = User::ROLE_ROOT;
            $user->can_send_message = User::ALLOW_MESSAGE;
            $user->status = User::STATUS_ACTIVE;
            $user->username = $username;
            $user->email_confirmed = User::EMAIL_CONFIRMED;
            $user->ip = '127.0.0.1';
            $user->last_ip_login ='127.0.0.1';
            $user->registered_at = date("Y-m-d H:i:s");
            $user->modified_at = date("Y-m-d H:i:s");
            $user->last_login_at = date("Y-m-d H:i:s");
            $user->save(false);

            $server_id = Yii::app()->innerMail->box($user)->generateUserBoxID();
            $user->post_server_id = $server_id;
            $user->save(false);

            echo "New user [{$username}:{$email}] has been added\n";
        } catch (Exception $exception) {
            Yii::log($exception, CLogger::LEVEL_ERROR);
            echo "Unable to create a user: {$exception->getMessage()}\n";
        }
    }
}