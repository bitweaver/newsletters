<?php

$tables = array(

'tiki_newsletters' => "
  nl_id I4 AUTO PRIMARY,
  content_id I4 NOTNULL,
  last_sent I8,
  editions I8,
  users I8,
  allow_user_sub C(1) default 'y',
  allow_any_sub C(1),
  unsub_msg C(1) default 'y',
  validate_addr C(1) default 'y',
  frequency I8
  CONSTRAINTS ', CONSTRAINT `tiki_nl_ed_con_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."tiki_content`( `content_id` )'
",

'tiki_newsletter_subscriptions' => "
  nl_id I4 PRIMARY,
  email C(160) PRIMARY,
  user_id I4,
  code C(32),
  valid C(1),
  subscribed_date I8,
  unsubscribed_date I8
  CONSTRAINTS ', CONSTRAINT `tiki_nl_sub_nl_ref` FOREIGN KEY (`nl_id`) REFERENCES `".BIT_DB_PREFIX."tiki_newsletters`( `nl_id` ),
			   , CONSTRAINT `tiki_nl_group_ref` FOREIGN KEY (`group_id`) REFERENCES `".BIT_DB_PREFIX."users_groups`( `group_id` )'
",

'tiki_newsletters_editions' => "
  edition_id I4 AUTO PRIMARY,
  nl_id I4 NOTNULL,
  content_id I4 NOTNULL
  CONSTRAINTS ', CONSTRAINT `tiki_nl_ed_nl_ref` FOREIGN KEY (`nl_id`) REFERENCES `".BIT_DB_PREFIX."tiki_newsletters`( `nl_id` )
  			   , CONSTRAINT `tiki_nl_ed_con_ref` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."tiki_content`( `content_id` )'
",

'tiki_newsletters_mailings' => "
  edition_id I4 NOTNULL,
  queue_date I8,
  send_date I8,
  emails_sent I8
  CONSTRAINTS ', CONSTRAINT `tiki_nl_mail_ed_ref` FOREIGN KEY (`edition_id`) REFERENCES `".BIT_DB_PREFIX."tiki_newsletters_editions`( `edition_id` )'
",

'tiki_newsletters_mailings_queue' => "
  edition_id I4 PRIMARY,
  email C(160) PRIMARY,
  user_id C(1),
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
	'tiki_nl_mq_email_idx' => array( 'table' => 'tiki_newsletters_mailings_queue', 'cols' => 'email', 'opts' => NULL ),
	'tiki_nl_mq_sent_idx' => array( 'table' => 'tiki_newsletters_mailings_queue', 'cols' => 'sent_date', 'opts' => NULL ),
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
