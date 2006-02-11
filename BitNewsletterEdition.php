<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_newsletters/BitNewsletterEdition.php,v 1.15.2.3 2006/02/11 15:34:17 wolff_borg Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: BitNewsletterEdition.php,v 1.15.2.3 2006/02/11 15:34:17 wolff_borg Exp $
 *
 * Class that handles editions of newsletters
 * @package newsletters
 *
 * created 2005/12/08
 *
 * @author spiderr <spider@steelsun.com>
 *
 * @version $Revision: 1.15.2.3 $ $Date: 2006/02/11 15:34:17 $ $Author: wolff_borg $
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

		if( @$this->verifyId( $pParamHash['nl_content_id'] ) ) {
			$pParamHash['edition_store']["nl_content_id"] = $pParamHash['nl_content_id'];
		} else {
			$this->mErrors['nl_content_id'] = tra( 'No newsletter was selected for this edition.' );
		}
		$pParamHash['edition_store']['is_draft'] = !empty( $pParamHash['is_draft'] ) ? 'y' : NULL;
		$pParamHash['edition_store']['reply_to'] = !empty( $pParamHash['reply_to'] ) ? $pParamHash['reply_to'] : NULL;

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
		if( $this->verifyId( $this->mEditionId ) || $this->verifyId( $this->mContentId ) ) {
			global $gBitSystem;

			$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';

			$lookupColumn = $this->verifyId( $this->mEditionId )? 'edition_id' : 'content_id';
			$lookupId = $this->verifyId( $this->mEditionId )? $this->mEditionId : $this->mContentId;
			array_push( $bindVars, $lookupId );

			$this->getServicesSql( 'content_load_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "SELECT tne.*, tc.*
					  FROM `".BIT_DB_PREFIX."tiki_newsletters_editions` tne
						INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tne.`content_id`=tc.`content_id` )
					  WHERE tne.`$lookupColumn`=? $whereSql";
			if ( $result = $this->mDb->query($query,$bindVars) ) {
				$this->mInfo = $result->fetchRow();
				$this->mEditionId = $this->mInfo['edition_id'];
				$this->mContentId = $this->mInfo['content_id'];
				$this->mNewsletter = new BitNewsletter( NULL, $this->mInfo['nl_content_id'] );
				$this->mNewsletter->load();
			} else {
				unset( $this->mEditionId );
			}
		}
		return( count( $this->mInfo ) );
	}

	function isValid() {
		return( $this->verifyId( $this->mEditionId ) );
	}

	/**
	 * Generate a valid url for the Newsletter Edition
	 *
	 * @param	object	PostId of the item to use
	 * @return	object	Url String
	 */
	function getDisplayUrl( $pEditionId=NULL ) {
		$ret = NULL;
		if( !$this->verifyId( $pEditionId ) ) {
			$pEditionId = $this->mEditionId;
		}
		global $gBitSystem;
		if( $this->verifyId( $pEditionId ) ) {
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

		if( @$this->verifyId( $pListHash['nl_id'] ) ) {
			$mid .= (empty( $mid ) ? 'WHERE' : 'AND').' tn.nl_id=? ';
			$bindVars[] = $pListHash['nl_id'];
		}

		if( $pListHash['find'] ) {
			$findesc = '%' . $pListHash['find'] . '%';
			$mid .= (empty( $mid ) ? 'WHERE' : 'AND').' (tc.`title` like ? or tc.`data` like ?)';
			$bindVars[] = $findesc;
			$bindVars[] = $findesc;
		}
		$query = "SELECT `edition_id` AS `hash_key`, tne.*, tc.*, tc2.`title` AS `newsletter_title`
				  FROM `".BIT_DB_PREFIX."tiki_newsletters_editions` tne
				  	INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tc.`content_id`=tne.`content_id` )
				  	INNER JOIN `".BIT_DB_PREFIX."tiki_newsletters` tn ON( tne.`nl_content_id`=tn.`content_id` )
				  	LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_content` tc2 ON( tn.`content_id`=tc2.`content_id` )
				  $mid ORDER BY ".$this->mDb->convert_sortmode( $pListHash['sort_mode'] );
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_newsletters` tn INNER JOIN `".BIT_DB_PREFIX."tiki_newsletters_editions` tne ON(tn.`content_id`=tne.`nl_content_id`) $mid";
		$ret = $gBitDb->getAssoc( $query, $bindVars, $pListHash['max_records'], $pListHash['offset'] );
		foreach( array_keys( $ret ) as $k ) {
			$ret[$k]['display_url'] = BitNewsletterEdition::getDisplayUrl( $k );
			// remove formating tags
			$data = preg_replace( '/{[^{}]*}/', '', $ret[$k]['data'] );
			$ret[$k]['parsed'] = BitNewsletterEdition::parseData( $data, $ret[$k]['format_guid'] );
		}
        $pListHash['total_records'] = $gBitDb->getOne( $query_cant, $bindVars );
		$pListHash['block_pages'] = 5;
		$pListHash['total_pages'] = ceil( $pListHash['total_records'] / $pListHash['max_records'] );
		$pListHash['current_page'] = (!empty( $pListHash['offset'] ) ? floor( $pListHash['offset'] / $pListHash['max_records'] ) + 1 : 1 );

		return $ret;
	}

	function expunge() {
		$ret = FALSE;
		if( $this->isValid() ) {
			$this->mDb->StartTrans();
			$query = "delete from `".BIT_DB_PREFIX."tiki_newsletters_editions` where `edition_id`=?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( LibertyAttachable::expunge() ) {
				$ret = TRUE;
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return $ret;
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

			if ( array_search( array('send_subs'), $pGroupArray ) === false ) {
				$query = "SELECT * FROM `".BIT_DB_PREFIX."tiki_mail_subscriptions`
					  WHERE (`nl_content_id`=? AND `unsubscribe_date` IS NULL) AND `unsubscribe_all` IS NULL";
				$subs = $this->mDb->getArray( $query, array( $this->mNewsletter->mNewsletterId) );
				foreach( $subs as $sub) {
					if (!isset($ret[$sub['email']]))
						$ret[$sub['email']] = $sub;
				}
			}

			$query = "SELECT `email`, `user_id` FROM `".BIT_DB_PREFIX."tiki_mail_queue` WHERE `content_id`=?";
			if( $dupes = $this->mDb->getAssoc( $query, array( $this->mContentId ) ) ) {
				$ret = array_diff_keys( $ret, $dupes );
			}
		}
		return $ret;
	}

	function render() {
		global $gBitSmarty;
		$ret = NULL;
		if( $this->isValid() ) {
			$gBitSmarty->assign_by_ref( 'gContent', $this );
			$ret = $gBitSmarty->fetch( 'bitpackage:newsletters/view_edition.tpl' );
		}
		return $ret;
	}
}

?>
