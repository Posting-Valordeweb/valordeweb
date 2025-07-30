<?php
$cfg_main = __DIR__.DIRECTORY_SEPARATOR."config.php";
$cfg_local = __DIR__.DIRECTORY_SEPARATOR."config_local.php";
$params = is_file($cfg_local) ? require $cfg_local : require $cfg_main;

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>$params['app.name'],
	'language'=>$params['app.default_language'],
	'timeZone'=>$params['app.timezone'],
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.forms.*',
		'application.events.*',
        'ext.mailer.YiiMailer',
	),

	'components'=>array(
		'innerMail' => array(
			'class'=>'InnerMail',
			'connectionID' => 'db',
			'headerTable' => '{{post_header_%d}}',
			'messageTable' => '{{post_message_%d}}',
			'folderTable' => '{{post_folder_%d}}',
			'blockTable' => '{{block_sender}}',
			'scamTable' => '{{scam_report}}',
			'serverCount'=>3,
			'headersPageSize'=>20, // Number of headers per page
			'messagesPageSize'=>30, // Number of messages per page
			'blockedUsersPageSize'=>15, // Number of blocked users per page
		),

		'user'=>array(
			// enable cookie-based authentication
			'class' => 'application.components.WebUser',
			'allowAutoLogin'=>true,
            'identityCookie'=>array(
                'httpOnly' => true,
                'path' => $params['app.base_url'],
                'secure'=> $params['cookie.secure'],
                'sameSite'=> $params['cookie.same_site'],
            ),
		),

		'authManager' => array(
			'class' => 'application.components.PhpAuthManager',
			'defaultRoles' => array('guest'),
		),

		'urlManager'=>array(
			'urlFormat'=>'path',
			'class'=>'UrlManager',
            'cacheID'=>'cache',
			'showScriptName' => $params['url.show_script_name'],
		),
		'messages' => array(
			'class' => 'DbMessageSource',
			'sourceMessageTable' => 'sc_trans_source_message',
			'translatedMessageTable' => 'sc_trans_message',
			'missingTranslationTable' => 'sc_trans_missing',
			'cachingDuration'=>60 * 60 * 24 * 30,
			'onMissingTranslation'=>array('DbMessageSource', 'onMissingTranslation'),
		),

		'cache' => array(
			'class' => 'CFileCache',
		),

		'db'=>array(
			// Mysql host: localhost and databse name catalog
			'connectionString' => "mysql:host={$params['db.host']};dbname={$params['db.dbname']};port={$params['db.port']}",
			// whether to turn on prepare emulation
			'emulatePrepare' => true,
			// db username
			'username' => $params['db.username'],
			// db password
			'password' => $params['db.password'],
			// default cahrset
			'charset' => 'utf8mb4',
			// table prefix
			'tablePrefix' => 'sc_',
			// cache time to reduce SHOW CREATE TABLE * queries
			'schemaCachingDuration' => 60 * 60 * 24 * 30,
			'enableProfiling'=> YII_DEBUG,
			'enableParamLogging' => YII_DEBUG,
		),

		'clientScript'=>array(
			'packages'=>array(
				'jquery'=>array(
					'baseUrl'=>'js',
                    'js'=>array('jquery.min.js'),
				),
			),
		),


		'errorHandler'=>array(
			'errorAction'=>'site/error',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
					'except'=>'exception.CHttpException.*',
				),
                /*array(
                    'class'=>'CWebLogRoute',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'trace',
                    'categories'=>'system.db.*',
                    'logFile'=>'sql.log',
                ),*/
			),
		),

        'securityManager' => array(
            'encryptionKey'=>$params['app.encryption_key'],
            'validationkey'=>$params['app.validation_key'],
        ),

        'session'=>array(
            'cookieParams'=>array(
                'httponly' => true,
                'path' => $params['app.base_url'],
                'secure'=> $params['cookie.secure'],
                'samesite'=> $params['cookie.same_site'],
            ),
        ),

        'request'=>array(
            'enableCookieValidation'=>$params['app.cookie_validation'],
            'csrfCookie' => array(
                'httpOnly' => true,
                'path' => $params['app.base_url'],
                'secure'=> $params['cookie.secure'],
                'sameSite'=> $params['cookie.same_site'],
            ),
        ),
	),

	'params'=>$params,
);