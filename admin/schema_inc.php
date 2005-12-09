<?php

$tables = array(

'tiki_newsletter_subscriptions' => "
  nl_id I4 PRIMARY,
  email C(160) PRIMARY,
  code C(32),
  valid C(1),
  subscribed I8
",

'tiki_newsletters' => "
  nl_id I4 AUTO PRIMARY,
  name C(200),
  description X,
  created I8,
  last_sent I8,
  editions I8,
  users I8,
  allow_user_sub C(1) default 'y',
  allow_any_sub C(1),
  unsub_msg C(1) default 'y',
  validate_addr C(1) default 'y',
  frequency I8
",

'tiki_sent_newsletters' => "
  edition_id I4 AUTO PRIMARY,
  nl_id I4 NOTNULL,
  users I8,
  sent I8,
  subject C(200),
  data B
"

);

global $gTikiInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gTikiInstaller->registerSchemaTable( NEWSLETTERS_PKG_DIR, $tableName, $tables[$tableName] );
}


$gTikiInstaller->registerSchemaDefault( NEWSLETTERS_PKG_DIR, array(

	"INSERT INTO `".TIKI_DB_PREFIX."tiki_menu_options` (`menu_id` , `type` , `name` , `url` , `position` , `section` , `perm` , `groupname`) VALUES (42,'s','Newsletters','tiki-newsletters.php',900,'feature_newsletters','','')",
	"INSERT INTO `".TIKI_DB_PREFIX."tiki_menu_options` (`menu_id` , `type` , `name` , `url` , `position` , `section` , `perm` , `groupname`) VALUES (42,'o','Send newsletters','tiki-send_newsletters.php',905,'feature_newsletters','tiki_p_admin_newsletters','')",
	"INSERT INTO `".TIKI_DB_PREFIX."tiki_menu_options` (`menu_id` , `type` , `name` , `url` , `position` , `section` , `perm` , `groupname`) VALUES (42,'o','Admin newsletters','tiki-admin_newsletters.php',910,'feature_newsletters','tiki_p_admin_newsletters','')",

	"INSERT INTO `".TIKI_DB_PREFIX."users_permissions` (`perm_name`, `perm_desc`, `level`, `package`) VALUES ('tiki_p_admin_newsletters', 'Can admin newsletters', 'editors', 'newsletters')",
	"INSERT INTO `".TIKI_DB_PREFIX."users_permissions` (`perm_name`, `perm_desc`, `level`, `package`) VALUES ('tiki_p_subscribe_newsletters', 'Can subscribe to newsletters', 'basic', 'newsletters')",
	"INSERT INTO `".TIKI_DB_PREFIX."users_permissions` (`perm_name`, `perm_desc`, `level`, `package`) VALUES ('tiki_p_subscribe_email', 'Can subscribe any email to newsletters', 'editors', 'newsletters')",


	"INSERT INTO `".TIKI_DB_PREFIX."tiki_preferences`(`package`,`name`,`value`) VALUES ('', 'feature_newsletters','n')"

) );

?>
