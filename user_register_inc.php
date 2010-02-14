<?php

require_once NEWSLETTERS_PKG_PATH.'BitNewsletter.php';

$listHash = array( 'registration_optin' => TRUE );

$newsletters = BitNewsletter::getList( $listHash );

global $gBitSmarty;

$gBitSmarty->assign('newsletters',$newsletters);
?>
