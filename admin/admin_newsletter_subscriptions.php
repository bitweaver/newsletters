<?php

// $Header$

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

// Initialization
require_once( '../../kernel/setup_inc.php' );
$gBitSystem->verifyPackage( 'newsletters' );
$gBitSystem->verifyPermission( 'p_admin_newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_inc.php' );

if( $gContent->isValid() ) {
	$nl_id = $_REQUEST['nl_id'];
	$gBitSmarty->assign( 'nl_id', $nl_id );

	/* mass-remove:
	   the checkboxes are sent as the array $_REQUEST["checked[]"], values are the wiki-PageNames,
	   e.g. $_REQUEST["checked"][3]="HomePage"
	   $_REQUEST["submit_mult"] holds the value of the "with selected do..."-option list
	   we look if any page's checkbox is on and if remove_pages is selected.
	   then we check permission to delete pages.
	   if so, we call BitPage::expunge for all the checked pages.
	*/
	if (isset($_REQUEST["submit_mult"]) && isset($_REQUEST["checked"]) && $_REQUEST["submit_mult"] == "remove") {
		if( !empty( $_REQUEST['cancel'] ) ) {
			// user cancelled - just continue on, doing nothing
		} elseif( empty( $_REQUEST['confirm'] ) ) {
			$formHash['nl_id'] = $nl_id;
			$formHash['delete'] = TRUE;
			$formHash['submit_mult'] = 'remove';
			foreach( $_REQUEST["checked"] as $del ) {
				$formHash['input'][] = '<input type="hidden" name="checked[]" value="'.$del.'"/>';
			}
			$gBitSystem->confirmDialog( $formHash, 
				array( 
					'warning' => tra('Are you sure you want to delete these subscriptions?') . ' (' . tra('Count: ') . count( $_REQUEST["checked"] ) . ')',
					'error' => tra('This cannot be undone!'),
				)
			);
		} else {
			foreach ($_REQUEST["checked"] as $delete) {
				$gContent->removeSubscription($delete, FALSE, TRUE );
			}
		}
	} elseif (isset($_REQUEST["submit_mult"]) && isset($_REQUEST["checked"]) && $_REQUEST["submit_mult"] == "unsubscribe") {
		if( !empty( $_REQUEST['cancel'] ) ) {
			// user cancelled - just continue on, doing nothing
		} elseif( empty( $_REQUEST['confirm'] ) ) {
			$formHash['nl_id'] = $nl_id;
			$formHash['delete'] = TRUE;
			$formHash['submit_mult'] = 'unsubscribe';
			foreach( $_REQUEST["checked"] as $del ) {
				$formHash['input'][] = '<input type="hidden" name="checked[]" value="'.$del.'"/>';
			}
			$gBitSystem->confirmDialog( $formHash, 
				array( 
					'warning' => tra('Are you sure you want to unsubcsribe these subscriptions?') . ' (' . tra('Count: ') . count( $_REQUEST["checked"] ) . ')',
					'error' => tra('This cannot be undone!'),
				)
			);
		} else {
			foreach ($_REQUEST["checked"] as $delete) {
				$gContent->removeSubscription($delete, FALSE, FALSE );
			}
		}
	} elseif (isset($_REQUEST["submit_mult"]) && isset($_REQUEST["checked"]) && $_REQUEST["submit_mult"] == "resubscribe") {
		if( !empty( $_REQUEST['cancel'] ) ) {
			// user cancelled - just continue on, doing nothing
		} elseif( empty( $_REQUEST['confirm'] ) ) {
			$formHash['nl_id'] = $nl_id;
			$formHash['delete'] = TRUE;
			$formHash['submit_mult'] = 'resubscribe';
			foreach( $_REQUEST["checked"] as $del ) {
				$formHash['input'][] = '<input type="hidden" name="checked[]" value="'.$del.'"/>';
			}
			$gBitSystem->confirmDialog( $formHash, 
				array( 
					'warning' => tra('Are you sure you want to resubscribe these subscriptions?') . ' (' . tra('Count: ') . count( $_REQUEST["checked"] ) . ')',				
					'error' => tra('This cannot be undone!'),
				)
			);
		} else {
			foreach ($_REQUEST["checked"] as $delete) {
				$gContent->subscribe($delete, FALSE, FALSE );
			}
		}
	} elseif (isset($_REQUEST["save"])) {
		$new_subs = preg_split("/[\s,]+/", $_REQUEST["new_subscribers"]);
		foreach($new_subs as $sub) {
			$sub = trim($sub);
			if (empty($sub))
				continue;
			$gContent->subscribe($sub, TRUE );
		}
	}

	$subscribers = $gContent->getSubscribers( TRUE );
	$gBitSmarty->assign( 'subscribers', $subscribers );
}
/*
$cat_type='newsletter';
$cat_objid = $_REQUEST["nl_id"];
include_once( CATEGORIES_PKG_PATH.'categorize_list_inc.php' );
*/

// Display the template
$gBitSystem->display( 'bitpackage:newsletters/admin_newsletter_subscriptions.tpl' , NULL, array( 'display_mode' => 'admin' ));

?>
