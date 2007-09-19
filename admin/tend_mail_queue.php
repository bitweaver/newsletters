<?php
	global $gShellScript;
	$gShellScript = TRUE;
	$_REQUEST['uri_mode'] = TRUE;

/**
 * required setup
 */
	if( !empty( $argc ) ) {
		$_SERVER["SERVER_NAME"] = '';
		// reduce feedback for command line to keep log noise way down
		define( 'BIT_PHP_ERROR_REPORTING', E_ALL ^ E_NOTICE ^ E_WARNING );
	}

	require_once( '../../bit_setup_inc.php' );

	if( $gBitSystem->isPackageActive( 'newsletters' ) ) {
		require_once( NEWSLETTERS_PKG_PATH.'BitMailer.php' );
		global $gBitMailer;
		$gBitMailer = new BitMailer();
		$gBitMailer->tendQueue();
	}

?>
