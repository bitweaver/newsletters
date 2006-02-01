<?php

$tables = array(

'newsletters' => "
  nl_id I4 AUTO PRIMARY,
  content_id I4 NOTNULL,
  last_sent I8,
  allow_user_sub C(1) default 'y',
  allow_any_sub C(1),
  unsub_msg C(1) default 'y',
  validate_addr C(1) default 'y',
  frequency I8
  CONSTRAINTS ', CONSTRAINT `tiki_nl_ed_con_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."tiki_content`( `content_id` )'
",

'newsletters_editions' => "
  edition_id I4 AUTO PRIMARY,
  nl_content_id I4 NOTNULL,
  is_draft C(1),
  reply_to C(160),
  content_id I4 NOTNULL
  CONSTRAINTS ', CONSTRAINT `tiki_nl_ed_nl_con_ref` FOREIGN KEY (`nl_content_id`) REFERENCES `".BIT_DB_PREFIX."tiki_content`( `content_id` )
  			   , CONSTRAINT `tiki_nl_ed_con_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."tiki_content`( `content_id` )'
",

'mail_subscriptions' => "
  email C(160),
  user_id I4,
  nl_content_id I4,
  response_content_id I4,
  unsubscribe_all C(1),
  unsubscribe_date I8
  CONSTRAINTS ', CONSTRAINT `mail_unsub_con_ref` FOREIGN KEY (`unsub_content_id`) REFERENCES `".BIT_DB_PREFIX."newsletters`( `unsub_content_id` ),
			   , CONSTRAINT `mail_unsub_ref` FOREIGN KEY (`user_id`) REFERENCES `".BIT_DB_PREFIX."users_users`( `user_id` )'
",

'mail_errors' => "
  url_code C(32) PRIMARY,
  email C(160),
  user_id I4,
  content_id I4,
  error_date I8,
  error_message X
  CONSTRAINTS ', CONSTRAINT `mail_err_user_ref` FOREIGN KEY (`user_id`) REFERENCES `".BIT_DB_PREFIX."users_users`( `user_id` )'
",

'mail_mailings' => "
  content_id I4 NOTNULL,
  queue_date I8,
  send_date I8,
  emails_sent I8
  CONSTRAINTS ', CONSTRAINT `mail_content_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."tiki_content` ( `content_id` )'
",

'mail_queue' => "
  content_id I4 PRIMARY,
  email C(160) PRIMARY,
  nl_content_id I4 NOTNULL,
  user_id I4,
  url_code C(32),
  queue_date I8 NOTNULL,
  begin_date I8,
  sent_date I8,
  last_read_date I8,
  reads I2 NOTNULL DEFAULT '0'
  CONSTRAINTS ', CONSTRAINT `tiki_nl_mailq_ed_ref` FOREIGN KEY (`edition_id`) REFERENCES `".BIT_DB_PREFIX."newsletters_editions`( `edition_id` ),
			   , CONSTRAINT `tiki_nl_mailq_user_ref` FOREIGN KEY (`users_id`) REFERENCES `".BIT_DB_PREFIX."users_users`( `users_id` )'
"

);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( NEWSLETTERS_PKG_DIR, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( NEWSLETTERS_PKG_NAME, array(
	'description' => "Newsletters is for emailing users updates about your site.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
	'version' => '1.0',
	'state' => 'R1',
	'dependencies' => '',
) );

// ### Indexes
$indices = array (
	'nl_sub_nl_idx' => array( 'table' => 'mail_subscriptions', 'cols' => 'nl_id', 'opts' => NULL ),
	'nl_sub_group_idx' => array( 'table' => 'mail_subscriptions', 'cols' => 'group_id', 'opts' => NULL ),
	'nl_sub_email_idx' => array( 'table' => 'mail_subscriptions', 'cols' => 'email', 'opts' => NULL ),
	'nl_ed_nl_idx' => array( 'table' => 'newsletters_editions', 'cols' => 'nl_id', 'opts' => NULL ),
	//'nl_group_nl_idx' => array( 'table' => 'newsletter_groups', 'cols' => 'nl_id', 'opts' => NULL ),
	'mq_email_idx' => array( 'table' => 'mail_queue', 'cols' => 'email', 'opts' => NULL ),
	'mq_user_idx' => array( 'table' => 'mail_queue', 'cols' => 'user_id', 'opts' => NULL ),
	'mq_content_idx' => array( 'table' => 'mail_queue', 'cols' => 'content_id', 'opts' => NULL ),
	'mq_sent_idx' => array( 'table' => 'mail_queue', 'cols' => 'sent_date', 'opts' => NULL ),
);
$gBitInstaller->registerSchemaIndexes( LIBERTY_PKG_NAME, $indices );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( NEWSLETTERS_PKG_NAME, array(
	array('bit_p_admin_newsletters', 'Can admin and send newsletters', 'editors', 'newsletters'),
	array('bit_p_create_newsletters', 'Can create newsletters', 'editors', 'newsletters'),
	array('bit_p_create_editions', 'Can create editions', 'editors', 'newsletters'),
	array('bit_p_subscribe_newsletters', 'Can subscribe to newsletters', 'registered', 'newsletters'),
	array('bit_p_subscribe_email', 'Can subscribe any email to newsletters', 'editors', 'newsletters'),
) );

?>
