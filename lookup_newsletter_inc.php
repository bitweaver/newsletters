<?php
/**
 * Copyright (c) 2005 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * created 2005/12/10
 *
 * @package newsletters
 * @author spider <spider@steelsun.com>
 */

/** 
 * Initialization
 */
require_once( NEWSLETTERS_PKG_PATH.'BitNewsletter.php' );

if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
	$nlId = !empty( $_REQUEST['nl_id'] ) ? $_REQUEST['nl_id'] : NULL;
	$conId = !empty( $_REQUEST['content_id'] ) ? $_REQUEST['content_id'] : !empty( $_REQUEST['nl_content_id'] ) ? $_REQUEST['nl_content_id'] : NULL;
	$gContent = new BitNewsletter( $nlId, $conId );
	$gContent->load();
	$gBitSmarty->assign_by_ref( 'gContent', $gContent );
}

//	$gBitSmarty->assign('individual', 'n');
/*
	if ($userlib->object_has_one_permission($_REQUEST["nl_id"], 'newsletter')) {
		$gBitSmarty->assign('individual', 'y');

		if( !$gBitUser->isAdmin() ) {
			$perms = $userlib->get_permissions(0, -1, 'perm_name_desc', '', 'newsletters');

			foreach ($perms["data"] as $perm) {
				$perm_name = $perm["perm_name"];

				if ($userlib->object_has_permission($user, $_REQUEST["nl_id"], 'newsletter', $perm_name)) {
					$$perm_name = 'y';

					$gBitSmarty->assign("$perm_name", 'y');
				} else {
					$$perm_name = 'n';

					$gBitSmarty->assign("$perm_name", 'n');
				}
			}
		}
	}
*/



?>
