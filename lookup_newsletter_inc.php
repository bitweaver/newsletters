<?php
/**
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
require_once( NEWSLETTERS_PKG_PATH.'BitNewsletter.php' );

if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
	$nlId = !empty( $_REQUEST['nl_id'] ) ? $_REQUEST['nl_id'] : NULL;
	$conId = !empty( $_REQUEST['content_id'] ) ? $_REQUEST['content_id'] : !empty( $_REQUEST['nl_content_id'] ) && is_numeric( $_REQUEST['nl_content_id'] ) ? $_REQUEST['nl_content_id'] : NULL;
	$gContent = new BitNewsletter( $nlId, $conId );
	$gContent->load();
	$gBitSmarty->assign_by_ref( 'gContent', $gContent );
}

?>
