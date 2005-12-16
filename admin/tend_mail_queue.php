<?php

	global $gBitSystem, $_SERVER;

	$_SERVER['SCRIPT_URL'] = '';
	$_SERVER['HTTP_HOST'] = '';
	$_SERVER['HTTP_HOST'] = '';
	$_SERVER['HTTP_HOST'] = '';
	$_SERVER['SERVER_NAME'] = '';
	$_SERVER['SERVER_ADMIN'] = '';
	$_SERVER['SERVER_SOFTWARE'] = 'command line';

/**
 * required setup
 */
	if( !empty( $argc ) ) {
		// reduce feedback for command line to keep log noise way down
//		define( 'BIT_PHP_ERROR_REPORTING', E_ERROR | E_PARSE );
	}

	require_once( '../../bit_setup_inc.php' );

	// add some protection for arbitrary thumbail execution.
	// if argc is present, we will trust it was exec'ed command line.
	if( empty( $argc ) && !$gBitUser->isAdmin() ) {
		$gBitSystem->fatalError( 'You cannot run the thumbnailer' );
	}

	if( $gBitSystem->isPackageActive( 'newsletters' ) ) {
		require_once( NEWSLETTERS_PKG_PATH.'BitMailer.php' );
		global $gBitMailer;
		$gBitMailer = new BitMailer();

		$gBitMailer->tendQueue();
	}

?>