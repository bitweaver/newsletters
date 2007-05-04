<?php
global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;

$upgrades = array(

'BONNIE' => array(
	'CLYDE' => array(
array( 'RENAMETABLE' => array(
        'tiki_sent_newsletters' => 'tiki_newsletters_editions',
        'tiki_newsletter_subscriptions' => 'tiki_mail_subscriptions',
)),
// STEP 1
array( 'DATADICT' => array(
	array( 'RENAMECOLUMN' => array(
		'tiki_newsletters' => array( '`nlId`' => '`nl_id` I4 AUTO' ),
		'tiki_newsletters' => array( '`allowUserSub`' => "`allow_user_sub` C(1) default 'y'" ),
		'tiki_newsletters' => array( '`allowAnySub`' => '`allow_any_sub` C(1)' ),
		'tiki_newsletters' => array( '`unsubMsg`' => "`unsub_msg` C(1) default 'y'" ),
		'tiki_newsletters' => array( '`validateAddr`' => "`validate_addr` C(1) default 'y'" ),
		'tiki_newsletters' => array( '`lastSent`' => '`last_sent` I8' ),
		'tiki_newsletters_editions' => array( '`editionId`' => '`edition_id` I4 AUTO' ),
		'tiki_newsletter_subscriptions' => array( '`code`' => "`sub_code` C(36)" ),
		'tiki_newsletter_subscriptions' => array( '`valid`' => "`is_valid` C(1)" ),
		'tiki_newsletter_subscriptions' => array( '`subscribed`' => "`subscribed_date` I8" ),
	)),
	// ALTER
	array( 'ALTER' => array(
		'tiki_newsletters' => array(
			'content_id' => array( '`content_id`', 'I4' ),
		),
		'tiki_newsletters_editions' => array(
			'content_id' => array( '`content_id`', 'I4' ),
			'nl_content_id' => array( '`nl_content_id`', 'I4' ),
			'is_draft' => array( '`is_draft`', 'C(1)' ),
		),
		'tiki_newsletter_subscriptions' => array(
			'nl_content_id' => array( '`nl_content_id`', 'I4' ),
			'response_content_id' => array( '`response_content_id`', 'I4' ),
			'unsubscribe_all' => array( '`unsubscribe_all`', 'C(1)' ),
			'unsubscribe_date' => array( '`unsubscribe_date`', 'I8' ),
		),
	)),
	// CREATE
	array( 'CREATE' => array (
	)),
)),

// STEP 3
array( 'QUERY' =>
	array( 'SQL92' => array(
		),
)),

// STEP 6
array( 'DATADICT' => array(
	array( 'DROPCOLUMN' => array(
		'tiki_newsletters' => array( '`name`', '`description`', '`created`', '`users`', '`editions`' ),
	)),
)),
	)
),

	'BWR1' => array(
		'BWR2' => array(
// de-tikify tables
array( 'DATADICT' => array(
	array( 'RENAMETABLE' => array(
		'tiki_newsletters'          => 'newsletters',
		'tiki_newsletters_editions' => 'newsletters_editions',
		'tiki_mail_subscriptions'   => 'mail_subscriptions',
		'tiki_mail_errors'          => 'mail_errors',
		'tiki_mail_mailings'        => 'mail_mailings',
		'tiki_mail_queue'           => 'mail_queue',
	)),
	array( 'CREATE' => array (
		'mail_clickthrough' => "
			user_id I4 NOTNULL,
			content_id I4 NOTNULL,
			clicked_url C(250) NOTNULL,
			clicks I4
			CONSTRAINT ', CONSTRAINT `mail_clickthrough_user_ref` FOREIGN KEY (`user_id`) REFERENCES `".BIT_DB_PREFIX."users_users` (`user_id`),
						  CONSTRAINT `mail_clickthrough_content_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)'
		"
	)),
)),
		)
	),
);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( NEWSLETTERS_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}


?>
