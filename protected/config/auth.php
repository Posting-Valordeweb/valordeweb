<?php
return array(
	'guest' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Guest',
		'bizRule' => null,
		'data' => null,
	),
	'user' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'User',
		'children' => array(
			'guest',
		),
		'bizRule' => null,
		'data' => null,
	),
	'administrator' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Administrator',
		'children' => array(
			'user',
		),
		'bizRule' => null,
		'data' => null,
	),
	'root' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Root',
		'children' => array(
			'administrator',
		),
		'bizRule' => null,
		'data' => null,
	),
);