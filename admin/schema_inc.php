<?php

$tables = array(

'tiki_newsletters' => "
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

'tiki_mail_unsubscriptions' => "
  content_id I4 PRIMARY,
  email C(160) PRIMARY,
  user_id I4,
  unsubscribe_all C(1),
  unsubscribed_date I8
  CONSTRAINTS ', CONSTRAINT `tiki_mail_unsub_con_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."tiki_newsletters`( `content_id` ),
			   , CONSTRAINT `tiki_mail_unsub_ref` FOREIGN KEY (`user_id`) REFERENCES `".BIT_DB_PREFIX."users_users`( `user_id` )'
",

'tiki_newsletters_editions' => "
  edition_id I4 AUTO PRIMARY,
  nl_id I4 NOTNULL,
  is_draft C(1),
  content_id I4 NOTNULL
  CONSTRAINTS ', CONSTRAINT `tiki_nl_ed_nl_ref` FOREIGN KEY (`nl_id`) REFERENCES `".BIT_DB_PREFIX."tiki_newsletters`( `nl_id` )
  			   , CONSTRAINT `tiki_nl_ed_con_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."tiki_content`( `content_id` )'
",

'tiki_editions_mailings' => "
  content_id I4 NOTNULL,
  queue_date I8,
  send_date I8,
  emails_sent I8
  CONSTRAINTS ', CONSTRAINT `tiki_nl_mail_ed_ref` FOREIGN KEY (`edition_id`) REFERENCES `".BIT_DB_PREFIX."tiki_newsletters_editions`( `edition_id` )'
",

'tiki_mail_queue' => "
  content_id I4 PRIMARY,
  email C(160) PRIMARY,
  user_id I4,
  queue_date I8 NOTNULL,
  sent_date I8
  CONSTRAINTS ', CONSTRAINT `tiki_nl_mailq_ed_ref` FOREIGN KEY (`edition_id`) REFERENCES `".BIT_DB_PREFIX."tiki_newsletters_editions`( `edition_id` ),
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
	'tiki_nl_sub_nl_idx' => array( 'table' => 'tiki_newsletter_subscriptions', 'cols' => 'nl_id', 'opts' => NULL ),
	'tiki_nl_sub_group_idx' => array( 'table' => 'tiki_newsletter_subscriptions', 'cols' => 'group_id', 'opts' => NULL ),
	'tiki_nl_sub_email_idx' => array( 'table' => 'tiki_newsletter_subscriptions', 'cols' => 'email', 'opts' => NULL ),
	'tiki_nl_ed_nl_idx' => array( 'table' => 'tiki_newsletters_editions', 'cols' => 'nl_id', 'opts' => NULL ),
	'tiki_nl_group_nl_idx' => array( 'table' => 'tiki_newsletter_groups', 'cols' => 'nl_id', 'opts' => NULL ),
	'tiki_mq_email_idx' => array( 'table' => 'tiki_mail_queue', 'cols' => 'email', 'opts' => NULL ),
	'tiki_mq_user_idx' => array( 'table' => 'tiki_mail_queue', 'cols' => 'user_id', 'opts' => NULL ),
	'tiki_mq_content_idx' => array( 'table' => 'tiki_mail_queue', 'cols' => 'content_id', 'opts' => NULL ),
	'tiki_mq_sent_idx' => array( 'table' => 'tiki_mail_queue', 'cols' => 'sent_date', 'opts' => NULL ),
);
$gBitInstaller->registerSchemaIndexes( LIBERTY_PKG_NAME, $indices );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( NEWSLETTERS_PKG_NAME, array(
	array('bit_p_admin_newsletters', 'Can admin newsletters', 'editors', 'newsletters'),
	array('bit_p_send_newsletters', 'Can send newsletters', 'editors', 'newsletters'),
	array('bit_p_subscribe_newsletters', 'Can subscribe to newsletters', 'basic', 'newsletters'),
	array('bit_p_subscribe_email', 'Can subscribe any email to newsletters', 'editors', 'newsletters'),
) );

?>
