<?php
global $gBitSystem;

$gBitSystem->registerPackage( 'Newsletters',  dirname( __FILE__ ).'/' );

if( $gBitSystem->isPackageActive( NEWSLETTERS_PKG_NAME ) ) {
	$gBitSystem->registerAppMenu( NEWSLETTERS_PKG_DIR, 'Newsletters', NEWSLETTERS_PKG_URL.'index.php', 'bitpackage:newsletters/menu_newsletters.tpl', NEWSLETTERS_PKG_NAME );
}
?>