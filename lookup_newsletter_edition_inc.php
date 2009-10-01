<?php
/**
 * Copyright (c) 2005 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
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
 require_once( NEWSLETTERS_PKG_PATH.'BitNewsletterEdition.php' );

if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
	$editionId = !empty( $_REQUEST['edition_id'] ) ? $_REQUEST['edition_id'] : NULL;
	$conId = !empty( $_REQUEST['content_id'] ) ? $_REQUEST['content_id'] : NULL;
	$nlId = !empty( $_REQUEST['nl_id'] ) ? $_REQUEST['nl_id'] : NULL;
	$gContent = new BitNewsletterEdition( $editionId, $conId, $nlId );
	$gContent->load();
	$gBitSmarty->assign_by_ref( 'gContent', $gContent );
}


?>
