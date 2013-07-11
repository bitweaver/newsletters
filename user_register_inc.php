<?php
/**
 * Copyright (c) 2005 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * created 2005/12/10
 * @author spider <spider@steelsun.com>
 *
 * @package newsletters
 */

/** 
 * Initialization
 */
require_once NEWSLETTERS_PKG_PATH.'BitNewsletter.php';

$listHash = array( 'registration_optin' => TRUE );

$newsletters = BitNewsletter::getList( $listHash );

global $gBitSmarty;

$gBitSmarty->assign('newsletters',$newsletters);
?>
