<?php
/**
 * $Header$
 *
 * @copyright (c) 2004-15 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * $Id$
 *
 * Class that handles editions of newsletters
 * @package newsletters
 *
 * @date created 2005/12/08
 *
 * @author spiderr <spider@steelsun.com>
 *
 * @version $Revision$
 */

/**
 * required setup
 */
require_once( NEWSLETTERS_PKG_PATH.'BitNewsletter.php' );
require_once( LIBERTY_PKG_PATH.'LibertyMime.php' );

define( 'BITNEWSLETTEREDITION_CONTENT_TYPE_GUID', 'bitnewsletteredn' );

/**
 * @package newsletters
 */
class BitNewsletterEdition extends LibertyMime {
	function __construct( $pEditionId=NULL, $pContentId=NULL, $pNlId=NULL ) {
		parent::__construct();
		$this->registerContentType( BITNEWSLETTEREDITION_CONTENT_TYPE_GUID, array(
			'content_type_guid' => BITNEWSLETTEREDITION_CONTENT_TYPE_GUID,
			'content_name' => 'Edition',
			'handler_class' => 'BitNewsletterEdition',
			'handler_package' => 'newsletters',
			'handler_file' => 'BitNewsletterEdition.php',
			'maintainer_url' => 'http://www.bitweaver.org'
		) );
		$this->mEditionId = $pEditionId;
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITNEWSLETTEREDITION_CONTENT_TYPE_GUID;
		$this->mNewsletter = new BitNewsletter( $pNlId );

		// Permission setup
		//$this->mViewContentPerm  = '';
		$this->mUpdateContentPerm  = 'p_newsletters_create_editions';
		$this->mAdminContentPerm = 'p_newsletters_admin';
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

	function store( &$pParamHash ) {
		if( $this->verify( $pParamHash ) ) {
			$this->mDb->StartTrans();
			if( parent::store( $pParamHash ) ) {
				if( $this->mEditionId ) {
					$result = $this->mDb->associateUpdate( BIT_DB_PREFIX."newsletters_editions", $pParamHash['edition_store'], array ( "edition_id" => $this->mEditionId ) );
				} else {
					$pParamHash['edition_store']['content_id'] = $pParamHash['content_id'];
					$result = $this->mDb->associateInsert( BIT_DB_PREFIX."newsletters_editions", $pParamHash['edition_store'] );
				}
				$this->mDb->CompleteTrans();
				$this->load();
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return( count( $this->mErrors ) == 0 );
	}

	function load( $pContentId = NULL, $pPluginParams = NULL ) {
		if( $this->verifyId( $this->mEditionId ) || $this->verifyId( $this->mContentId ) ) {
			global $gBitSystem;

			$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';

			$lookupColumn = $this->verifyId( $this->mEditionId )? 'edition_id' : 'content_id';
			$lookupId = $this->verifyId( $this->mEditionId )? $this->mEditionId : $this->mContentId;
			array_push( $bindVars, $lookupId );

			$this->getServicesSql( 'content_load_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "SELECT ne.*, lc.*
					  FROM `".BIT_DB_PREFIX."newsletters_editions` ne
						INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( ne.`content_id`=lc.`content_id` )
					  WHERE ne.`$lookupColumn`=? $whereSql";
			if ( $result = $this->mDb->query($query,$bindVars) ) {
				$this->mInfo = $result->fetchRow();
				$this->mEditionId = $this->mInfo['edition_id'];
				$this->mContentId = $this->mInfo['content_id'];
				LibertyMime::load();
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
	public static function getDisplayUrlFromHash( &$pParamHash ) {
		$ret = NULL;
		global $gBitSystem;
		if( BitBase::verifyId( $pParamHash['edition_id'] ) ) {
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) ) {
				$ret = NEWSLETTERS_PKG_URL.'edition/'.$pParamHash['edition_id'];
			} else {
				$ret = NEWSLETTERS_PKG_URL.'edition.php?edition_id='.$pParamHash['edition_id'];
			}
		} else {
			$ret = NEWSLETTERS_PKG_URL.'edition.php';
		}
		return $ret;
	}


	function getList( &$pListHash ) {
		global $gBitDb;

		$bindVars = array();
		parent::prepGetList( $pListHash );
		$mid = '';

		if( @BitBase::verifyId( $pListHash['nl_id'] ) ) {
			$mid .= (empty( $mid ) ? 'WHERE' : 'AND').' n.nl_id=? ';
			$bindVars[] = $pListHash['nl_id'];
		}

		if( $pListHash['find'] ) {
			$findesc = '%' . $pListHash['find'] . '%';
			$mid .= (empty( $mid ) ? 'WHERE' : 'AND').' (lc.`title` like ? or lc.`data` like ?)';
			$bindVars[] = $findesc;
			$bindVars[] = $findesc;
		}

		$query = "SELECT `edition_id` AS `hash_key`, ne.*, lc.*, lc2.`title` AS `newsletter_title`
				  FROM `".BIT_DB_PREFIX."newsletters_editions` ne
				  	INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id`=ne.`content_id` )
				  	INNER JOIN `".BIT_DB_PREFIX."newsletters` n ON( ne.`nl_content_id`=n.`content_id` )
				  	LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content` lc2 ON( n.`content_id`=lc2.`content_id` )
				  $mid ORDER BY ".$gBitDb->convertSortmode( $pListHash['sort_mode'] );
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."newsletters` n INNER JOIN `".BIT_DB_PREFIX."newsletters_editions` ne ON(n.`content_id`=ne.`nl_content_id`) $mid";
		$ret = $gBitDb->getAssoc( $query, $bindVars, $pListHash['max_records'], $pListHash['offset'] );
		foreach( array_keys( $ret ) as $k ) {
			$ret[$k]['display_url'] = BitNewsletterEdition::getDisplayUrlFromHash( $ret[$k] );
			// remove formating tags
			$data = preg_replace( '/{[^{}]*}/', '', $ret[$k]['data'] );
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
			$query = "DELETE FROM `".BIT_DB_PREFIX."newsletters_editions` WHERE `content_id`=?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( LibertyMime::expunge() ) {
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

	function getRecipients( $pGroupArray, $validated = TRUE, $pRequeue = FALSE ) {
		global $gBitUser;
		$ret = array();
		if( is_array( $pGroupArray ) ) {
			foreach( $pGroupArray as $groupId ) {
				$ret = array_merge( $ret, $gBitUser->getGroupUserData( $groupId, array( 'email', 'uu.user_id', 'login', 'real_name' ) ) );
			}

			if ( array_search( 'send_subs', $pGroupArray ) !== false ) {
				$valid = "";
				$bindvars = array( $this->mNewsletter->mNewsletterId );
				if ($validated) {
					$valid = " AND `is_valid`=?";
					$bindvars[] = 'y';
				}
				$query = "SELECT * FROM `".BIT_DB_PREFIX."mail_subscriptions`
					  WHERE `content_id`=? AND `unsubscribe_date` IS NULL AND `unsubscribe_all` IS NULL".$valid;
				$subs = $this->mDb->getArray( $query, $bindvars );
				foreach( $subs as $sub) {
					if (!isset($ret[$sub['email']]))
						$ret[$sub['email']] = $sub;
				}
			}
			if( !$pRequeue ) {
				$query = "SELECT `email`, `user_id` FROM `".BIT_DB_PREFIX."mail_queue` WHERE `content_id`=?";
				if( $dupes = $this->mDb->getAssoc( $query, array( $this->mContentId ) ) ) {
					$ret = array_diff_keys( $ret, $dupes );
				}
			}
		}
		return $ret;
	}

	function render() {
		global $gBitSmarty;
		$ret = NULL;
		if( $this->isValid() ) {
			$gBitSmarty->assignByRef( 'gContent', $this );
			$ret = $gBitSmarty->fetch( 'bitpackage:newsletters/view_edition.tpl' );
		}
		return $ret;
	}
}

?>
