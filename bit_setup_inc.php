<?php
global $gBitSystem;

define( 'LIBERTY_SERVICE_NEWSLETTERS', 'newsletters' );

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
	if( isset( $_GET['ct'] ) && strlen( $_GET['ct'] ) == 32 ) {
		require_once( NEWSLETTERS_PKG_PATH.'BitNewsletterMailer.php' );
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
			require_once NEWSLETTERS_PKG_PATH.'BitNewsletter.php';
			$newsletter = new BitNewsletter();
			$pParamHash = array();
			$newsletters = $newsletter->getList($pParamHash);
			foreach ($newsletters as $nl){
				if(!empty($_REQUEST['subscribe'])){//we want to stay in at least one, which requires more complicated checking
					if( !in_array($nl['nl_id'], $_REQUEST['subscribe'])){ //not checked, implies unsubscribe
						$newsletter->unsubscribe($nl['nl_id'],false);
					}
				}else{ //we wish to unsubscribe from all newsletters
					$newsletter->unsubscribe($nl['nl_id'],false);
				}
			}
	}
}
?>
