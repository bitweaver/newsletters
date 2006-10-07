<?php

require_once( '../../bit_setup_inc.php' );

$gBitSystem->verifyPermission( 'p_mail_admin' );
$gBitSystem->verifyPermission( 'newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'BitMailer.php' );
global $gBitMailer;
$gBitMailer = new BitMailer();

if( !empty( $_REQUEST['batch_command'] ) && !empty( $_REQUEST['queue_id'] ) ) {
	if( $_REQUEST['batch_command'] == 'delete' ) {
		foreach( $_REQUEST['queue_id'] as $qId ) { 
			$gBitMailer->expungeQueueRow( $qId  );
		}
	} elseif( $_REQUEST['batch_command'] == 'send' ) {
	}
}

$listHash = array();
$queue = $gBitMailer->getQueue( $listHash );
$gBitSmarty->assign_by_ref( 'queue', $queue );

$gBitSystem->display( 'bitpackage:newsletters/mail_queue.tpl' );

?>
