<?php
/**
 *
 * Copyright (c) 2005 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * created 2005/12/10
 *
 * @author spider <spider@steelsun.com>
 */

// Initialization
require_once( '../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );
include_once( UTIL_PKG_PATH.'htmlMimeMail.php' );

$gBitSystem->verifyPackage( 'newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_edition_inc.php' );

$listHash = array();
$newsletters = $gContent->mNewsletter->getList( $listHash );
$gBitSmarty->assign( 'newsletters', $newsletters );

if (isset($_REQUEST["remove"] ) && $gContent->isValid() ) {
	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['remove'] = TRUE;
		$formHash['edition_id'] = $gContent->mEditionId;
		$gBitSystem->confirmDialog( $formHash, array( 'warning' => 'Are you sure you want to delete the newsletter edition '.$gContent->getTitle().'?' ) );
	} else {
		if( $gContent->expunge() ) {
			header( "Location: ".NEWSLETTERS_PKG_URL.'edition.php' );
			die;
		}
	}
}

if( $gContent->isValid() ) {
	$mid = 'bitpackage:bitcommerce/view_edition.tpl';
} else {
	$listHash = array();
	$editions = $gContent->getList( $listHash );
	$gBitSmarty->assign_by_ref( 'editions', $editions );
	$gBitSmarty->assign( 'listInfo', $listHash );
	$mid = 'bitpackage:bitcommerce/list_editions.tpl';
}

// Display the template
$gBitSystem->display( $mid );

?>