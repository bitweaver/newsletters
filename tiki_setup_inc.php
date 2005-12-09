<?php
global $gTikiSystem;

$gTikiSystem->registerPackage( 'Newsletters',  dirname( __FILE__ ).'/' );

if( $gTikiSystem->isPackageActive( 'NEWSLETTERS_PKG_NAME' ) ) {
	$gTikiSystem->registerAppMenu( NEWSLETTERS_PKG_DIR, NEWSLETTERS_PKG_NAME, NEWSLETTERS_PKG_URL.'index.php', 'tikipackage:newsletters/menu_newsletters.tpl', true );
}
?>
