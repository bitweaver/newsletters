<?php

$tables = array(

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

'tiki_newsletter_subscriptions' => "
  nl_id I4 PRIMARY,
  email C(160) PRIMARY,
  code C(32),
  valid C(1),
  subscribed I8,
  is_user C(1) NOTNULL default 'n'
  CONSTRAINTS ', CONSTRAINT `tiki_nl_sub_nl_id_ref` FOREIGN KEY (`nl_id`) REFERENCES `".BIT_DB_PREFIX."tiki_newsletters`( `nl_id` )'
",

'tiki_sent_newsletters' => "
  edition_id I4 AUTO PRIMARY,
  nl_id I4 NOTNULL,
  users I8,
  sent I8,
  subject C(200),
  data X
  CONSTRAINTS ', CONSTRAINT `tiki_nl_sent_nl_id_ref` FOREIGN KEY (`nl_id`) REFERENCES `".BIT_DB_PREFIX."tiki_newsletters`( `nl_id` )'
",

'tiki_newsletter_groups_map' => "
  nl_id I4 NOTNULL PRIMARY,
  group_id I4 NOTNULL PRIMARY,
  code C(32)
  CONSTRAINTS ', CONSTRAINT `tiki_nl_groups_nl_id_ref` FOREIGN KEY (`nl_id`) REFERENCES `".BIT_DB_PREFIX."tiki_newsletters`( `nl_id` ),
			   , CONSTRAINT `tiki_nl_groups_id_ref` FOREIGN KEY (`group_id`) REFERENCES `".BIT_DB_PREFIX."users_groups`( `group_id` )'
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
	'tiki_nl_sub_email_idx' => array( 'table' => 'tiki_newsletter_subscriptions', 'cols' => 'email', 'opts' => NULL ),
	'tiki_nl_sent_nl_idx' => array( 'table' => 'tiki_sent_newsletters', 'cols' => 'nl_id', 'opts' => NULL ),
	'tiki_nl_group_idx' => array( 'table' => 'tiki_newsletter_groups', 'cols' => 'group_id', 'opts' => NULL ),
	'tiki_nl_group_nl_idx' => array( 'table' => 'tiki_newsletter_groups', 'cols' => 'nl_id', 'opts' => NULL ),
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
