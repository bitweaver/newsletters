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

	require_once( '../../kernel/includes/setup_inc.php' );

	global $gBitSystem;
	$gBitSystem->verifyPermission( 'p_users_admin' );

	if( $gBitSystem->isPackageActive( 'newsletters' ) ) {
		require_once( NEWSLETTERS_PKG_CLASS_PATH.'BitNewsletterMailer.php' );
		global $gBitNewsletterMailer;
		$gBitNewsletterMailer = new BitNewsletterMailer();
		$gBitNewsletterMailer->tendQueue();
	}

?>
