<?php

require_once NEWSLETTERS_PKG_PATH.'BitNewsletter.php';

$listHash = array();

$newsletters = BitNewsletter::getList( $listHash );

global $gBitSmarty;

$gBitSmarty->assign('newsletters',$newsletters);
?>
