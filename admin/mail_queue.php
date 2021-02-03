<?php

if( !empty( $_REQUEST['batch_command'] ) && $_REQUEST['batch_command'] == 'send' && !empty( $_REQUEST['queue_id'] ) ) {
	$_REQUEST['uri_mode'] = TRUE;
}

require_once( '../../kernel/includes/setup_inc.php' );

$gBitSystem->verifyPermission( 'p_mail_admin' );
$gBitSystem->verifyPermission( 'newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'BitNewsletterMailer.php' );
global $gBitNewsletterMailer;
$gBitNewsletterMailer = new BitNewsletterMailer();

if( !empty( $_REQUEST['batch_command'] ) && !empty( $_REQUEST['queue_id'] ) ) {
	if( $_REQUEST['batch_command'] == 'delete' ) {
		foreach( $_REQUEST['queue_id'] as $qId ) { 
			$gBitNewsletterMailer->expungeQueueRow( $qId  );
		}
	} elseif( $_REQUEST['batch_command'] == 'send' && !empty( $_REQUEST['queue_id'] ) ) {
		foreach( $_REQUEST['queue_id'] as $queueId ) {
			$gBitNewsletterMailer->sendQueue( $queueId );
		}
	}
}

if( empty( $_REQUEST['batch_command'] ) || $_REQUEST['batch_command'] != 'send' ) {
	$listHash = array();
	$queue = $gBitNewsletterMailer->getQueue( $listHash );
	$gBitSmarty->assignByRef( 'queue', $queue );

	$gBitSystem->display( 'bitpackage:newsletters/mail_queue.tpl' , NULL, array( 'display_mode' => 'admin' ));
}
?>
