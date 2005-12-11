<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_newsletters/BitNewsletterEdition.php,v 1.4 2005/12/11 08:22:51 spiderr Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: BitNewsletterEdition.php,v 1.4 2005/12/11 08:22:51 spiderr Exp $
 *
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived tikiwiki classes that require database access.
 * @package blogs
 *
 * created 2004/10/20
 *
 * @author drewslater <andrew@andrewslater.com>, spiderr <spider@steelsun.com>
 *
 * @version $Revision: 1.4 $ $Date: 2005/12/11 08:22:51 $ $Author: spiderr $
 */

/**
 * required setup
 */
require_once( NEWSLETTERS_PKG_PATH.'BitNewsletter.php' );
require_once( LIBERTY_PKG_PATH.'LibertyAttachable.php' );

define( 'BITNEWSLETTEREDITION_CONTENT_TYPE_GUID', 'bitnewsletteredn' );

class BitNewsletterEdition extends LibertyAttachable {
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

	function verify( &$pParamHash ) {

		if( !empty( $pParamHash['nl_id'] ) ) {
			$pParamHash['edition_store']["nl_id"] = $pParamHash['nl_id'];
		} else {
			$this->mErrors['nl_id'] = tra( 'No newsletter was selected for this edition.' );
		}
		$pParamHash['edition_store']['is_draft'] = !empty( $pParamHash['is_draft'] ) ? 'y' : NULL;

		return( count( $this->mErrors ) == 0 );
	}

	function store( $pParamHash ) {
		if( $this->verify( $pParamHash ) ) {
			$this->mDb->StartTrans();
			if( parent::store( $pParamHash ) ) {
				if( $this->mEditionId ) {
					$result = $this->mDb->associateUpdate( BIT_DB_PREFIX."tiki_newsletters_editions", $pParamHash['edition_store'], array ( "name" => "edition_id", "value" => $this->mEditionId ) );
				} else {
					$pParamHash['edition_store']['content_id'] = $pParamHash['content_id'];
					$result = $this->mDb->associateInsert( BIT_DB_PREFIX."tiki_newsletters_editions", $pParamHash['edition_store'] );
				}
				$this->mDb->CompleteTrans();
				$this->load();
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return( count( $this->mErrors ) == 0 );
	}

	function load() {
		if( !empty( $this->mEditionId ) || !empty( $this->mContentId ) ) {
			global $gBitSystem;

			$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';

			$lookupColumn = !empty( $this->mEditionId )? 'edition_id' : 'content_id';
			$lookupId = !empty( $this->mEditionId )? $this->mEditionId : $this->mContentId;
			array_push( $bindVars, $lookupId );

			$this->getServicesSql( 'content_load_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "SELECT tne.*, tc.*
					  FROM `".BIT_DB_PREFIX."tiki_newsletters_editions` tne
						INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tne.`content_id`=tc.`content_id` )
					  WHERE `$lookupColumn`=? $whereSql";
			$result = $this->mDb->query($query,$bindVars);
			if ($result->numRows()) {
				$this->mInfo = $result->fetchRow();
				$this->mEditionId = $this->mInfo['edition_id'];
				$this->mContentId = $this->mInfo['content_id'];
				$this->mNewsletter = new BitNewsletter( $this->mInfo['nl_id'] );
				$this->mNewsletter->load();
			}
		}
		return( count( $this->mInfo ) );
	}

	function isValid() {
		return( !empty( $this->mEditionId ) );
	}

	/**
	 * Generate a valid url for the Newsletter Edition
	 *
	 * @param	object	PostId of the item to use
	 * @return	object	Url String
	 */
	function getDisplayUrl( $pEditionId=NULL ) {
		$ret = NULL;
		if( empty( $pEditionId ) ) {
			$pEditionId = $this->mEditionId;
		}
		global $gBitSystem;
		if( is_numeric( $pEditionId ) ) {
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) ) {
				$ret = NEWSLETTERS_PKG_URL.'edition/'.$pEditionId;
			} else {
				$ret = NEWSLETTERS_PKG_URL.'edition.php?edition_id='.$pEditionId;
			}
		} else {
			$ret = NEWSLETTERS_PKG_URL.'edition.php';
		}
		return $ret;
	}


	function getList( &$pListHash ) {
		global $gBitDb;

		$bindVars = array();
		BitBase::prepGetList( $pListHash );
		$mid = '';
		if( $pListHash['find'] ) {
			$findesc = '%' . $pListHash['find'] . '%';
			$mid = " WHERE (tc.`title` like ? or tc.`data` like ?)";
			$bindVars[] = $findesc;
			$bindVars[] = $findesc;
		}

		$query = "SELECT `edition_id` AS `hash_key`, tne.*, tc.*, tc2.`title` AS `newsletter_title`
				  FROM `".BIT_DB_PREFIX."tiki_newsletters_editions` tne
				  	INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tc.`content_id`=tne.`content_id` )
				  	LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_newsletters` tn ON( tne.`nl_id`=tn.`nl_id` )
				  	LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_content` tc2 ON( tn.`content_id`=tc2.`content_id` )
				  $mid ORDER BY ".$this->mDb->convert_sortmode( $pListHash['sort_mode'] );
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_newsletters` tn, `".BIT_DB_PREFIX."tiki_newsletters_editions` tsn where tn.`nl_id`=tsn.`nl_id` $mid";
		$ret = $gBitDb->getAssoc( $query, $bindVars, $pListHash['max_records'], $pListHash['offset'] );
		foreach( array_keys( $ret ) as $k ) {
			$ret[$k]['display_url'] = BitNewsletterEdition::getDisplayUrl( $k );
		}
        $pListHash['total_records'] = $gBitDb->getOne( $query_cant, $bindVars );
		$pListHash['block_pages'] = 5;
		$pListHash['total_pages'] = ceil( $pListHash['total_records'] / $pListHash['max_records'] );
		$pListHash['current_page'] = (!empty( $pListHash['offset'] ) ? floor( $pListHash['offset'] / $pListHash['max_records'] ) + 1 : 1 );

		return $ret;
	}

	function expunge($edition_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_newsletters_editions` where `edition_id`=$edition_id";
		$result = $this->mDb->query($query,array((int)$edition_id));
	}

	function isDraft() {
		return( $this->getField( 'is_draft' ) );
	}

	function getRecipients( $pGroupArray ) {
		global $gBitUser;
		$ret = array();
		if( is_array( $pGroupArray ) ) {
			foreach( $pGroupArray as $groupId ) {
				$ret = array_merge( $ret, $gBitUser->getGroupUserData( $groupId, array( 'email', 'uu.user_id', 'login', 'real_name' ) ) );
			}
		}
		return $ret;
	}
}

?>
