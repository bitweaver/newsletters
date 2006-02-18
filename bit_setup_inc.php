<?php
global $gBitSystem;

$registerHash = array(
	'package_name' => 'newsletters',
	'package_path' => dirname( __FILE__ ).'/',
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( NEWSLETTERS_PKG_NAME ) ) {
	$gBitSystem->registerAppMenu( NEWSLETTERS_PKG_DIR, 'Newsletters', NEWSLETTERS_PKG_URL.'index.php', 'bitpackage:newsletters/menu_newsletters.tpl', NEWSLETTERS_PKG_NAME );
}
?>
