<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_newsletters/newsletters.php,v 1.13 2009/10/01 14:17:02 wjames5 Exp $
 * 
 * Copyright (c) 2005 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * created 2005/12/10
 *
 * @package newsletters
 * @author spider <spider@steelsun.com>
 */

/** 
 * Initialization
 */
 require_once( '../bit_setup_inc.php' );
$gBitSystem->verifyPackage( 'newsletters' );
$gBitSystem->verifyPermission( 'p_newsletters_create' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_inc.php' );

if( isset( $_REQUEST["remove"] ) && $gContent->isValid() ) {
	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['remove'] = TRUE;
		$formHash['nl_id'] = $gContent->mNewsletterId;
		$gBitSystem->confirmDialog( $formHash, 
			array( 
				'warning' => tra('Are you sure you want to delete this newsletter?') . ' ' . $gContent->getTitle()
			)
		 );
	} else {
		if( $gContent->expunge() ) {
			header( "Location: ".NEWSLETTERS_PKG_URL.'newsletters.php' );
			die;
		}
	}
} elseif (isset($_REQUEST["save"])) {
	$sid = $gContent->store( $_REQUEST );
	header( "Location: ".$_SERVER['PHP_SELF'] );
	die;
} elseif( !empty( $_REQUEST['cancel'] ) ) {
	bit_redirect( NEWSLETTERS_PKG_URL );
}

$gContent->invokeServices( 'content_edit_function' );

$newsletters = $gContent->getList( $listHash );
$gBitSmarty->assign_by_ref( 'newsletters', $newsletters );
$gBitSmarty->assign_by_ref( 'listInfo', $listHash );

// Fill array with possible number of questions per page
/*
$freqs = array();

for ($i = 0; $i < 90; $i++) {
	$aux["i"] = $i;

	$aux["t"] = $i * 24 * 60 * 60;
	$freqs[] = $aux;
}

$gBitSmarty->assign('freqs', $freqs);
*/
/*
$cat_type='newsletter';
$cat_objid = $_REQUEST["nl_id"];
include_once( CATEGORIES_PKG_PATH.'categorize_list_inc.php' );
*/

// Display the template
$gBitSystem->display( 'bitpackage:newsletters/list_newsletters.tpl', NULL, array( 'display_mode' => 'display' ));

?>
