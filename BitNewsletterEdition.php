<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_newsletters/BitNewsletterEdition.php,v 1.1 2005/12/09 20:24:55 spiderr Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: BitNewsletterEdition.php,v 1.1 2005/12/09 20:24:55 spiderr Exp $
 *
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived tikiwiki classes that require database access.
 * @package blogs
 *
 * created 2004/10/20
 *
 * @author drewslater <andrew@andrewslater.com>, spiderr <spider@steelsun.com>
 *
 * @version $Revision: 1.1 $ $Date: 2005/12/09 20:24:55 $ $Author: spiderr $
 */

/**
 * required setup
 */
require_once( NEWSLETTERS_PKG_PATH.'BitNewsletter.php' );
require_once( LIBERTY_PKG_PATH.'LibertyContent.php' );

define( 'BITNEWSLETTEREDITION_CONTENT_TYPE_GUID', 'bitnewsletteredn' );

class BitNewsletterEdition extends LibertyContent {
	function BitNewsletterEdition( $pEditionId=NULL, $pContentId=NULL, $pNlId=NULL ) {
		parent::LibertyContent();
		$this->registerContentType( BITNEWSLETTEREDITION_CONTENT_TYPE_GUID, array(
			'content_type_guid' => BITNEWSLETTEREDITION_CONTENT_TYPE_GUID,
			'content_description' => 'Newsletter',
			'handler_class' => 'BitNewsletter',
			'handler_package' => 'newsletters',
			'handler_file' => 'BitNewsletter.php',
			'maintainer_url' => 'http://www.bitweaver.org'
		) );
		$this->mEditionId = $pEditionId;
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITNEWSLETTEREDITION_CONTENT_TYPE_GUID;
		$this->mNewsletter = new BitNewsletter( $pNlId );
	}

	function replace_edition($nl_id, $subject, $data, $users) {
		$now = date("U");
		$query = "insert into `".BIT_DB_PREFIX."tiki_newsletters_editions`(`nl_id`,`subject`,`data`,`sent`,`users`) values(?,?,?,?,?)";
		$result = $this->mDb->query($query,array((int)$nl_id,$subject,$data,(int)$now,$users));
	}

	function load() {
		if( !empty( $this->mNlId ) || !empty( $this->mContentId ) ) {
			global $gBitSystem;

			$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';

			$lookupColumn = !empty( $this->mNlId )? 'nl_id' : 'content_id';
			$lookupId = !empty( $this->mNlId )? $this->mNlId : $this->mContentId;
			array_push( $bindVars, $lookupId );

			$this->getServicesSql( 'content_load_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "SELECT *
					  FROM `".BIT_DB_PREFIX."tiki_newsletters_editions` tne
						INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tne.`content_id`=tc.`content_id` )
					  WHERE `$lookupColumn`=? $whereSql";
			$result = $this->mDb->query($query,$bindVars);
			if ($result->numRows()) {
				$this->mInfo = $result->fetchRow();
				$this->mNlId = $this->mInfo['nl_id'];
				$this->mContentId = $this->mInfo['content_id'];
				$this->mNewsletter = new BitNewsletter( $this->mInfo['nl_id'] );
			}
		}
		return( count( $this->mInfo ) );
	}

	function getList( &$pListHash ) {
		$bindVars = array();
		$this->prepGetList( $pListHash );
		$mid = '';
		if( $pListHash['find'] ) {
			$findesc = '%' . $pListHash['find'] . '%';
			$mid = " WHERE (tc.`title` like ? or tc.`data` like ?)";
			$bindVars[] = $findesc;
			$bindVars[] = $findesc;
		}

		$query = "SELECT `edition_id` AS `has_key`, *
				  FROM `".BIT_DB_PREFIX."tiki_newsletters` tn
				  	INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tc.`content_id`=tn.`content_id` )
				  	INNER JOIN `".BIT_DB_PREFIX."tiki_newsletters_editions` tsn ON( tn.`nl_id`=tsn.`nl_id` )
				  $mid ORDER BY ".$this->mDb->convert_sortmode( $pListHash['sort_mode'] );

		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_newsletters` tn, `".BIT_DB_PREFIX."tiki_newsletters_editions` tsn where tn.`nl_id`=tsn.`nl_id` $mid";
		$ret = $this->mDb->getAssoc( $query, $bindVars, $pListHash['max_records'], $pListHash['offset'] );

        $pListHash['total_records'] = $this->mDb->getOne( $query_cant, $bindVars );
		$pListHash['block_pages'] = 5;
		$pListHash['total_pages'] = ceil( $pListHash['total_records'] / $pListHash['max_records'] );
		$pListHash['current_page'] = (!empty( $pListHash['offset'] ) ? floor( $pListHash['offset'] / $pListHash['max_records'] ) + 1 : 1 );

		return $ret;
	}

	function remove_edition($edition_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_newsletters_editions` where `edition_id`=$edition_id";
		$result = $this->mDb->query($query,array((int)$edition_id));
	}

}

?>
