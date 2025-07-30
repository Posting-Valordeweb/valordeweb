<?php

return array(
	'viewPath' => 'application.views.mail',
	'layoutPath' => 'application.views.mail.layouts',
	'baseDirPath' => 'webroot.images.mail', //note: 'webroot' alias in console apps may not be the same as in web apps
	'savePath' => 'webroot.assets.mail',
	'testMode' => false,
	'layout' => 'main',
	'CharSet' => 'UTF-8',
	'AltBody' => 'You need an HTML capable viewer to read this message.',
	'language' => array(
		'authenticate' => 'SMTP Error: Could not authenticate.',
		'connect_host' => 'SMTP Error: Could not connect to SMTP host.',
		'data_not_accepted' => 'SMTP Error: Data not accepted.',
		'empty_message' => 'Message body empty',
		'encoding' => 'Unknown encoding: ',
		'execute' => 'Could not execute: ',
		'file_access' => 'Could not access file: ',
		'file_open' => 'File Error: Could not open file: ',
		'from_failed' => 'The following From address failed: ',
		'instantiate' => 'Could not instantiate mail function.',
		'invalid_address' => 'Invalid address',
		'mailer_not_supported' => ' mailer is not supported.',
		'provide_address' => 'You must provide at least one recipient email address.',
		'recipients_failed' => 'SMTP Error: The following recipients failed: ',
		'signing' => 'Signing Error: ',
		'smtp_connect_failed' => 'SMTP Connect() failed.',
		'smtp_error' => 'SMTP server error: ',
		'variable_set' => 'Cannot set or reset variable: '
	),
    'Mailer' => 'smtp',
    'Host' => Yii::app()->params['mailer.host'],
    'Port' => Yii::app()->params['mailer.port'],
    'SMTPSecure' => Yii::app()->params['mailer.protocol'],
    'SMTPAuth' => Yii::app()->params['mailer.auth'],
    'Username' => Yii::app()->params['mailer.username'],
    'Password' => Yii::app()->params['mailer.password'],
    // SMTP connection options
    'SMTPOptions' => array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    ),
);
