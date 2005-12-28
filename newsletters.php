<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/newsletters.php,v 1.1 2005/12/16 16:55:28 spiderr Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );
include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );
$gBitSystem->verifyPackage( 'newsletters' );
$gBitSystem->verifyPermission( 'tiki_p_admin_newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_inc.php' );

if( isset( $_REQUEST["remove"] ) && $gContent->isValid() ) {
	if( !empty( $_REQUEST['cancel'] ) ) {
		// user cancelled - just continue on, doing nothing
	} elseif( empty( $_REQUEST['confirm'] ) ) {
		$formHash['remove'] = TRUE;
		$formHash['nl_id'] = $gContent->mNlId;
		$gBitSystem->confirmDialog( $formHash, array( 'warning' => 'Are you sure you want to delete the newsletter '.$gContent->getTitle().'?' ) );
	} else {
		if( $gContent->expunge() ) {
			header( "Location: ".NEWSLETTERS_PKG_URL.'admin/' );
			die;
		}
	}
} elseif (isset($_REQUEST["save"])) {
	$sid = $gContent->store( $_REQUEST );
	header( "Location: ".$_SERVER['PHP_SELF'] );
	die;
}

$gContent->invokeServices( 'content_edit_function' );

// Configure quicktags list
if ($gBitSystem->isPackageActive( 'quicktags' ) ) {
	include_once( QUICKTAGS_PKG_PATH.'quicktags_inc.php' );
}

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
$gBitSystem->display( 'bitpackage:newsletters/list_newsletters.tpl');

?>