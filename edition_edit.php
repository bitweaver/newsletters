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

if( empty( $newsletters ) ) {
	header( 'Location: '.NEWSLETTERS_PKG_URL.'admin/index.php' );
	die;
}

$gBitSmarty->assign( 'newsletters', $newsletters );

if (isset($_REQUEST["preview"])) {
	$gBitSmarty->assign('preview', 'y');

	//$parsed = $tikilib->parse_data($_REQUEST["content"]);
	$parsed = $_REQUEST["edit"];
	$gBitSmarty->assign('parsed', $parsed);
	$info["data"] = $_REQUEST['edit'];
	$info["subject"] = $_REQUEST['title'];
	$gBitSmarty->assign('info', $info);
} elseif (isset($_REQUEST["save"])) {
	if( $gContent->store( $_REQUEST ) ) {
		$gBitSmarty->assign( 'success', tra( 'Newsletter edition saved' ) );
	} else {
		$gBitSmarty->assign( 'errors', $gContent->mErrors );
	}
}

// load the ajax library for this page
$gBitSmarty->assign( 'loadAjax', TRUE );
// Display the template
$gBitSystem->display( 'bitpackage:newsletters/edit_edition.tpl', ($gContent->isValid() ? tra( 'Edit Edition' ).': '.$gContent->getTitle() : tra( 'Create New Edition' )) );


?>