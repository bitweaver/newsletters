<?php
global $gBitSystem;

$registerHash = array(
	'package_name' => 'newsletters',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( NEWSLETTERS_PKG_NAME ) ) {
	$menuHash = array(
		'package_name'  => NEWSLETTERS_PKG_NAME,
		'index_url'     => NEWSLETTERS_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:newsletters/menu_newsletters.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );
}
?>
