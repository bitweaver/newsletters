<?php

	global $gBitSystem, $_SERVER;

	$_SERVER['SCRIPT_URL'] = '';
	$_SERVER['HTTP_HOST'] = '';
	$_SERVER['HTTP_USER_AGENT'] = '';
	$_SERVER['SERVER_NAME'] = '';
	$_SERVER['SERVER_ADMIN'] = '';
	$_SERVER['SERVER_SOFTWARE'] = 'command line';
	$_REQUEST['uri_mode'] = TRUE;

/**
 * required setup
 */
	if( !empty( $argc ) ) {
		// reduce feedback for command line to keep log noise way down
		define( 'BIT_PHP_ERROR_REPORTING', E_ERROR | E_PARSE );
	}

	require_once( '../../bit_setup_inc.php' );

	if( $gBitSystem->isPackageActive( 'newsletters' ) ) {
		require_once( NEWSLETTERS_PKG_PATH.'BitMailer.php' );
		global $gBitMailer;
		$gBitMailer = new BitMailer();

		$gBitMailer->tendQueue();
	}

?>
