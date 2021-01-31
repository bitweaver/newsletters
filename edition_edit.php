<?php
/**
 *
 * Copyright (c) 2005 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * created 2005/12/10
 *
 * @package newsletters
 * @author spider <spider@steelsun.com>
 */

/** 
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );
$gBitSystem->verifyPackage( 'newsletters' );
$gBitSystem->verifyPermission( 'p_newsletters_create_editions' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_edition_inc.php' );

$listHash = array();
$newsletters = $gContent->mNewsletter->getList( $listHash );

if( empty( $newsletters ) ) {
	header( 'Location: '.NEWSLETTERS_PKG_URL.'newsletters.php' );
	die;
}

$gBitSmarty->assign( 'newsletters', $newsletters );

#edit preview needs this
if( !isset( $_REQUEST['title'] ) && isset( $gContent->mInfo['title'] ) ) {
	$_REQUEST['title'] = $gContent->mInfo['title'];
}

if( !empty( $gContent->mInfo ) ) {
	$formInfo = $gContent->mInfo;
	$formInfo['edit'] = !empty( $gContent->mInfo['data'] )? $gContent->mInfo['data'] : '';
}

if( isset( $_REQUEST["edit"] ) ) {
	$formInfo['data'] = $_REQUEST["edit"];
	$formInfo['format_guid'] = ( !empty( $_REQUEST['format_guid'] ) ? $_REQUEST['format_guid'] : ( isset( $gContent->mInfo['format_guid'] ) ? $gContent->mInfo['format_guid'] : 'tikiwiki' ) );
}
if( isset( $_REQUEST['title'] ) ) {
	$formInfo['title'] = $_REQUEST['title'];
}

if( isset( $_REQUEST['is_draft'] ) && $_REQUEST['is_draft']=='y' ) {
	$formInfo['is_draft'] = 'y';
}

if( isset( $_REQUEST['reply_to'] ) ) {
	$formInfo['reply_to'] = $_REQUEST['reply_to'];
}

if (isset($_REQUEST["preview"])) {
	$gBitSmarty->assign('preview', 'y');

	$gBitSmarty->assign( 'title',!empty( $_REQUEST["title"] ) ? $_REQUEST["title"] : $gContent->getTitle() );

	$parsed = LibertyContent::parseDataHash( $formInfo['data'] );
	$gBitSmarty->assignByRef( 'parsed', $parsed );

	$gContent->invokeServices( 'content_preview_function' );
} elseif (isset($_REQUEST["save"])) {
	if( $gContent->store( $_REQUEST ) ) {
		// Add the content to the search index
//		if( $gBitSystem->isPackageActive( 'search' ) and $gBitSystem->isFeatureActive("search_index_on_submit")) {
//			require_once( SEARCH_PKG_PATH.'refresh_functions.php');
//			refresh_index_tiki_content($gContent->mContentId);
//		}
		header( 'Location: '.$gContent->getDisplayUrl() );
		die;
	} else {
		$formInfo = $_REQUEST;
		$formInfo['data'] = &$_REQUEST['edit'];
	}
} else {
	$gContent->invokeServices( 'content_edit_function' );
}

// WYSIWYG and Quicktag variable
$gBitSmarty->assign( 'textarea_id', LIBERTY_TEXT_AREA );

// formInfo might be set due to a error on submit
if( empty( $formInfo ) ) {
	$formInfo = &$gContent->mInfo;
}

$gBitSmarty->assignByRef( 'pageInfo', $formInfo );
$gBitSmarty->assign( 'errors', $gContent->mErrors );

// Display the template
$gBitSystem->display( 'bitpackage:newsletters/edit_edition.tpl', ($gContent->isValid() ? tra( 'Edit Edition' ).': '.$gContent->getTitle() : tra( 'Create New Edition' )) , array( 'display_mode' => 'edit' ));
?>
