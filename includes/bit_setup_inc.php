<?php
global $gBitSystem;

define( 'LIBERTY_SERVICE_NEWSLETTERS', 'newsletters' );

$registerHash = array(
	'package_name' => 'newsletters',
	'package_path' => dirname( dirname( __FILE__ ) ).'/',
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
	if( isset( $_GET['ct'] ) && strlen( $_GET['ct'] ) == 32 ) {
		require_once( NEWSLETTERS_PKG_CLASS_PATH.'BitNewsletterMailer.php' );
		BitNewsletterMailer::storeClickthrough( $_GET['ct'] );
	}

	$gLibertySystem->registerService( LIBERTY_SERVICE_NEWSLETTERS, NEWSLETTERS_PKG_NAME, array(
			'users_expunge_function'	=> 'newsletters_user_expunge',
			'users_register_function'   => 'newsletters_user_register',
	) );

	// make sure all mail_queue messages from a deleted user are nuked
	function newsletters_user_expunge( &$pObject ) {
		if( is_a( $pObject, 'BitUser' ) && !empty( $pObject->mUserId ) ) {
			$pObject->mDb->StartTrans();
			$pObject->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."mail_queue` WHERE user_id=?", array( $pObject->mUserId ) );
			$pObject->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."mail_subscriptions` WHERE user_id=?", array( $pObject->mUserId ) );
			$pObject->mDb->CompleteTrans();
		}
	}
	
	function newsletters_user_register( &$pObject ) {
		if( !empty( $_REQUEST['newsletter_optin'] ) ) {
			// hidden flag to indicate at least one newsletter was displayed
			require_once NEWSLETTERS_PKG_CLASS_PATH.'BitNewsletter.php';
			require_once NEWSLETTERS_PKG_CLASS_PATH.'BitNewsletterMailer.php';

			if( !empty( $_REQUEST['unsub_all'] ) ) {
				$subHash['unsubscribe_all'] = 'y';
			} else {
				$newsletter = new BitNewsletter();
				$pParamHash = array();
				$newsletters = $newsletter->getList($pParamHash);
				foreach( array_keys( $newsletters ) as $nlContentId ) {
					if( empty( $_REQUEST['nl_content_id'] ) || !in_array( $nlContentId, $_REQUEST['nl_content_id'] ) ) {
						$subHash['unsub_content'][] = $nlContentId;
						$subHash['unsubscribe_all'] = NULL;
					}
				}
			}

			if( !empty( $subHash ) ) {
				$subHash['sub_lookup'] = array( 'user_id' => $pObject->mUserId );
				BitNewsletterMailer::storeSubscriptions( $subHash );
			}
		}
	}
}
?>
