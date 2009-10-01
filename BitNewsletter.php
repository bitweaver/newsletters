<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_newsletters/BitNewsletter.php,v 1.34 2009/10/01 13:45:44 wjames5 Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * $Id: BitNewsletter.php,v 1.34 2009/10/01 13:45:44 wjames5 Exp $
 *
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived tikiwiki classes that require database access.
 * @package newsletters
 *
 * created 2004/10/20
 *
 * @author drewslater <andrew@andrewslater.com>, spiderr <spider@steelsun.com>
 *
 * @version $Revision: 1.34 $ $Date: 2009/10/01 13:45:44 $ $Author: wjames5 $
 */

/**
 * required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyContent.php' );
require_once( NEWSLETTERS_PKG_PATH.'BitNewsletterEdition.php' );

define( 'BITNEWSLETTER_CONTENT_TYPE_GUID', 'bitnewsletter' );

/**
 * @package newsletters
 */
class BitNewsletter extends LibertyContent {
	function BitNewsletter( $pNlId=NULL, $pContentId=NULL ) {
		parent::LibertyContent();
		$this->registerContentType( BITNEWSLETTER_CONTENT_TYPE_GUID, array(
			'content_type_guid' => BITNEWSLETTER_CONTENT_TYPE_GUID,
			'content_description' => 'Newsletter',
			'handler_class' => 'BitNewsletter',
			'handler_package' => 'newsletters',
			'handler_file' => 'BitNewsletter.php',
			'maintainer_url' => 'http://www.bitweaver.org'
		) );
		$this->mNewsletterId = $this->verifyId( $pNlId ) ? $pNlId : NULL;
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITNEWSLETTER_CONTENT_TYPE_GUID;

		// Permission setup
		//$this->mViewContentPerm  = '';
		$this->mUpdateContentPerm  = 'p_newsletters_create';
		$this->mAdminContentPerm = 'p_newsletters_admin';
	}

	function load( $pUserId = NULL ) {
		if( $this->verifyId( $this->mNewsletterId ) || $this->verifyId( $this->mContentId ) ) {
			global $gBitSystem;

			$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';

			$lookupColumn = $this->verifyId( $this->mNewsletterId ) ? 'nl_id' : 'content_id';
			$bindVars[] = $this->verifyId( $this->mNewsletterId )? $this->mNewsletterId : $this->mContentId;

			$this->getServicesSql( 'content_load_function', $selectSql, $joinSql, $whereSql, $bindVars );

			if( $pUserId ) {
				error_log( 'BitNewsleters: user id loading not implemented yet' );
				$whereSql = "";
				$joinSql = "";
			}

			$query = "SELECT * $selectSql
					  FROM `".BIT_DB_PREFIX."newsletters` n
					  	INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( n.`content_id`=lc.`content_id` )
					  	$joinSql
					  WHERE n.`$lookupColumn`=? $whereSql";
			$result = $this->mDb->query($query,$bindVars);
			if ($result->numRows()) {
				$this->mInfo = $result->fetchRow();
				$this->mNewsletterId = $this->mInfo['nl_id'];
				$this->mContentId = $this->mInfo['content_id'];
			}
		}
		return( count( $this->mInfo ) );
	}

	function loadEditions() {
		if( $this->isValid() ) {
			$this->mEditions = $this->getEditions();
		}
	}

	function store( &$pParamHash ) { //$nl_id, $name, $description, $allow_user_sub, $allow_any_sub, $unsub_msg, $validate_addr) {
		if( $this->verify( $pParamHash ) ) {
			$this->mDb->StartTrans();
			if( parent::store( $pParamHash ) ) {
				if( $this->mNewsletterId ) {
					$result = $this->mDb->associateUpdate( BIT_DB_PREFIX."newsletters", $pParamHash['newsletter_store'], array ( "nl_id" => $this->mNewsletterId ) );
				} else {
					$pParamHash['newsletter_store']['content_id'] = $pParamHash['content_id'];
					$result = $this->mDb->associateInsert( BIT_DB_PREFIX."newsletters", $pParamHash['newsletter_store'] );
				}
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return( count( $this->mErrors ) == 0 );
	}

	function verify( &$pParamHash ) {
		// It is possible a derived class set this to something different
		if( empty( $pParamHash['content_type_guid'] ) ) {
			$pParamHash['content_type_guid'] = $this->mContentTypeGuid;
		}
		$pParamHash['newsletter_store']["allow_user_sub"] = (isset($pParamHash["allow_user_sub"]) && $pParamHash["allow_user_sub"] == 'on') ? 'y' : 'n';
		$pParamHash['newsletter_store']["allow_any_sub"] = (isset($pParamHash["allow_any_sub"]) && $pParamHash["allow_any_sub"] == 'on') ? 'y': 'n';
		$pParamHash['newsletter_store']["unsub_msg"] = (isset($pParamHash["unsub_msg"]) && $pParamHash["unsub_msg"] == 'on') ? 'y' : 'n';
		$pParamHash['newsletter_store']["validate_addr"] = (isset($pParamHash["validate_addr"]) && $pParamHash["validate_addr"] == 'on') ? 'y' : 'n';
		return( count( $this->mErrors ) == 0 );
	}

	function getSubscriberInfo( $pLookup ) {
		$ret = array();
		if( $this->isValid() ) {
			$bindVars = array();
			$whereSql = '';
			if( !empty( $pLookup['email'] ) ) {
				$whereSql .= ' AND `email`=? ` ';
				$bindVars[] = $pLookup['email'];
			}
			if( !empty( $pLookup['user_id'] ) ) {
				$whereSql .= ' AND `user_id`=? ` ';
				$bindVars[] = $pLookup['user_id'];
			}
			$whereSql = preg_replace( '/^[\s]AND/', '', $whereSql );
			$query = "SELECT `content_id` AS `hash_key`, ms.* from `".BIT_DB_PREFIX."mail_subscriptions` ms WHERE $whereSql ";
			if( $res = $this->mDb->query( $query, $bindVars ) ) {
				$ret = $res->GetAssoc();
			}
		}
		return $ret;
	}

	function getSubscribers( $pAll=FALSE) {
		$ret = array();
		if( $this->isValid() ) {
			$whereSql = $pAll ? '' : '  `unsubscribe_date` is NULL AND ';
			$query = "select * from `".BIT_DB_PREFIX."mail_subscriptions` WHERE $whereSql `content_id`=?";
			if( $res = $this->mDb->query( $query, array( $this->mContentId ) ) ) {
				$ret = $res->GetRows();
			}
		}
		return $ret;
	}

	function removeSubscription( $email, $notify = FALSE, $del_record = FALSE ) {
		if ($del_record) {
			$this->mDb->query("DELETE FROM `".BIT_DB_PREFIX."mail_subscriptions` WHERE `content_id`=? AND `email`=?", array($this->mContentId, $email));
		} else {
			$urlCode = $this->mDb->getOne("select `sub_code` from `".BIT_DB_PREFIX."mail_subscriptions` where `content_id`=? and `email`=?", array($this->mContentId, $email));
			$this->unsubscribe($urlCode, $notify);
		}
	}

	function subscribe( $pSubscribeHash ) { // $notify = FALSE, $remind = FALSE ) {
		$ret = FALSE;
		if( $this->isValid() ) {
			global $gBitSystem;
			global $gBitSmarty;
			global $gBitUser;

			// Check for duplicates
			$all_subs = $this->getSubscribers( TRUE );
			$duplicate = FALSE;
			foreach($all_subs as $sub) {
				if( $sub['email'] == $pSubscribeHash['email'] ) {
					$duplicate = TRUE;
					$urlCode = $sub['sub_code'];
				} elseif( !empty( $pSubscribeHash['user_id'] ) && $sub['user_id'] == $pSubscribeHash['user_id'] ) {
				}
			}

			$urlCode = (!$duplicate) ? md5( BitUser::genPass() ) : $urlCode;
			$now = date("U");
			// Generate a code and store it and send an email  with the
			// URL to confirm the subscription put valid as 'n'
			if (!$duplicate) {
				if( @BitBase::verifyId( $pSubscribeHash['user_id'] ) ) {
					// we have user_id subscribing, use the id, NULL the email
					$subUserId = $pSubscribeHash['user_id'];
					$subEmail = NULL;
				} else {
					// we have user_id subscribing, use the id, NULL the email
					$subUserId = NULL;
					$subEmail = $pSubscribeHash['email'];
				}
				$query = "insert into `".BIT_DB_PREFIX."mail_subscriptions` (`content_id`, `user_id`, `email`,`sub_code`,`is_valid`,`subscribed_date`) VALUES (?,?,?,?,?,?)";
				$result = $this->mDb->query( $query, array( $this->mContentId, $subUserId, $subEmail, $urlCode, 'n', (int)$now ) );
			}
			if( ( !empty( $pSubscribeHash['notify'] ) && $this->getField( 'validate_addr' ) == 'y') || !empty( $pSubscribeHash['remind'] ) ) {
				// Generate a code and store it and send an email  with the
				$gBitSmarty->assign( 'sub_code', $urlCode );
				$mail_data = $gBitSmarty->fetch('bitpackage:newsletters/confirm_newsletter_subscription.tpl');
				@mail($email, tra('Newsletter subscription information at') . ' ' . $gBitSystem->getConfig( "bitmailer_from" ), $mail_data,
					"From: " . $gBitSystem->getConfig( "sender_email" ) . "\r\nContent-type: text/plain;charset=utf-8\r\n");
			}
			$ret = TRUE;
		}
		return $ret;
	}

	function unsubscribe( $pMixed, $notify = TRUE ) {
		global $gBitSystem;
		global $gBitSmarty;
		global $gBitUser;

		$ret = FALSE;
		$now = date("U");

		if( is_numeric( $pMixed ) ) {
			$query = "SELECT `content_id` FROM `".BIT_DB_PREFIX."newsletters` WHERE `nl_id`=?";
			if( $subRow['content_id'] = $this->mDb->getOne( $query, array( $pMixed ) ) ) {
				$subRow['col_name'] = 'user_id';
				$subRow['col_val'] = $gBitUser->mUserId;
			}
		} elseif( $pUrlCode ) {
			$query = "SELECT * FROM `".BIT_DB_PREFIX."mail_queue` WHERE `url_code`=?";
			if( $subRow = $this->mDb->getRow( $query, array( $pUrlCode ) ) ) {
				$subRow['col_name'] = !empty( $subRow['user_id'] ) ? 'user_id' : 'email';
				$subRow['col_val'] = !empty( $subRow['user_id'] ) ? $subRow['user_id'] : $subRow['email'];
			}
		}

		if( !empty( $subRow ) ) {
			$this->mContentId = $res['content_id'];
			$this->load();
			if( $this->mDb->getRow( "SELECT * FROM `".BIT_DB_PREFIX."mail_subscriptions` WHERE `$subRow[col_name]`=?", array( $subRow['col_val'] ) ) ) {
				$query = "UPDATE `".BIT_DB_PREFIX."mail_subscriptions` SET `unsubscribe_date`=?, `content_id`=? WHERE `$subRow[col_name]`=? AND `unsubscribe_date` IS NULL";
			} else {
				$query = "INSERT INTO `".BIT_DB_PREFIX."mail_subscriptions` (`unsubscribe_date`,`content_id`,`$subRow[col_name]`) VALUES(?,?,?)";
			}
			$result = $this->mDb->query( $query, array( $now, $subRow['content_id'], $subRow['col_val'] ) );
			if( $notify ) {
				// Now send a bye bye email
				$gBitSmarty->assign('sub_code', $res["sub_code"]);
				$mail_data = $gBitSmarty->fetch('bitpackage:newsletters/newsletter_byebye.tpl');
				@mail($res["email"], tra('Thank you from') . ' ' . $gBitSystem->getConfig( "bitmailer_from" ), $mail_data,
					"From: " . $gBitSystem->getConfig( "sender_email" ) . "\r\nContent-type: text/plain;charset=utf-8\r\n");
			}
			$ret = TRUE;
		}
		return $ret;
	}

/*
	function add_all_users($nl_id) {
		$query = "select `email` from `".BIT_DB_PREFIX."users_users`";
		$result = $this->mDb->query($query,array());
		while ($res = $result->fetchRow()) {
			$email = $res["email"];
			if (!empty($email)) {
				$this->newsletter_subscribe($nl_id, $email);
			}
		}
	}

	function updateUsers() {
		if( $this->isValid() ) {
			$users = $this->mDb->getOne( "select count(*) from `".BIT_DB_PREFIX."mail_subscriptions` where `nl_id`=?", array( $this->mNewsletterId ) );
			$query = "update `".BIT_DB_PREFIX."newsletters` set `users`=? where `nl_id`=?";
			$result = $this->mDb->query( $query, array( $users, $this->mNewsletterId ) );
		}
	}
*/

	function getList( &$pListHash ) {
		global $gBitDb;
		if ( empty( $pParamHash["sort_mode"] ) ) {
			$pListHash['sort_mode'] = 'created_desc';
		}
		BitBase::prepGetList( $pListHash );
		$bindVars = array();
		$mid = '';

		if( @BitBase::verifyId( $pListHash['nl_id'] ) ) {
			$mid .= ' AND n.nl_id=? ';
			$bindVars[] = $pListHash['nl_id'];
		}

		if( !empty( $pListHash['find'] ) ) {
			$findesc = '%' . $pListHash['find'] . '%';
			$mid .= " AND (`name` like ? or `description` like ?)";
			$bindVars[] = $findesc;
			$bindVars[] = $findesc;
		}

		$query = "SELECT *
				  FROM `".BIT_DB_PREFIX."newsletters` n INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( n.`content_id`=lc.`content_id`)
				  WHERE n.`content_id`=lc.`content_id` $mid
				  ORDER BY ".$gBitDb->convertSortmode( $pListHash['sort_mode'] );
		$result = $gBitDb->query( $query, $bindVars, $pListHash['max_records'], $pListHash['offset'] );

		$query_cant = "select count(*) from `".BIT_DB_PREFIX."newsletters` $mid";

		$ret = array();
		while( $res = $result->fetchRow() ) {
			$res['display_url'] = BitNewsletter::getDisplayUrl( $res['nl_id'] );
			$res["confirmed"] = $gBitDb->getOne( "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."mail_subscriptions` WHERE `unsubscribe_date` IS NULL and `content_id`=?",array( (int)$res['content_id'] ) );
			$res["unsub_count"] = $gBitDb->getOne( "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."mail_subscriptions` WHERE `content_id`=?",array( (int)$res['content_id'] ) );
			$ret[$res['content_id']] = $res;
		}

		return $ret;
	}

/*	function list_newsletter_subscriptions($nl_id, $offset, $maxRecords, $sort_mode, $find) {
		$bindVars = array((int)$nl_id);
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `nl_id`=? and (`name` like ? or `description` like ?)";
			$bindVars[] = $findesc;
			$bindVars[] = $findesc;
		} else {
			$mid = " where `nl_id`=? ";
		}

		$query = "select * from `".BIT_DB_PREFIX."mail_subscriptions` $mid order by ".$this->mDb->convertSortmode("$sort_mode");
		$query_cant = "select count(*) from mail_subscriptions $mid";
		$result = $this->mDb->query($query,$bindVars,$maxRecords,$offset);
		$cant = $this->mDb->getOne($query_cant,$bindVars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

*/

	function expunge() {
		$ret = FALSE;
		if( $this->isValid() ) {
			$this->mDb->StartTrans();
			$query = "DELETE FROM `".BIT_DB_PREFIX."newsletters` where `nl_id`=?";
			$result = $this->mDb->query( $query, array( $this->mNewsletterId ) );
			// Clear out all individual subscriptions/unsubscriptions, but preserve the unsubscribe_all's
			$query = "DELETE FROM `".BIT_DB_PREFIX."mail_subscriptions` WHERE `content_id`=? AND `unsubscribe_all` IS NOT NULL";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			$query = "UPDATE `".BIT_DB_PREFIX."mail_subscriptions` SET `content_id`=NULL WHERE `content_id`=? AND `unsubscribe_all` IS NOT NULL";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( parent::expunge() ) {
				$ret = TRUE;
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return $ret;
	}

	function isValid() {
		return( $this->verifyId( $this->mNewsletterId ) );
	}


	/**
	 * Generate a valid url for the Newsletter
	 *
	 * @param	object	$pNewsletterId of the item to use
	 * @return	object	Url String
	 */
	function getDisplayUrl( $pNewsletterId=NULL ) {
		global $gBitSystem;
		$ret = NULL;
		if( !BitBase::verifyId( $pNewsletterId ) ) {
			$pNewsletterId = $this->mNewsletterId;
		}
		if( BitBase::verifyId( $pNewsletterId ) ) {
			if( $gBitSystem->isFeatureActive( 'pretty_urls' ) ) {
				$ret = NEWSLETTERS_PKG_URL.$pNewsletterId;
			} else {
				$ret = NEWSLETTERS_PKG_URL.'index.php?nl_id='.$pNewsletterId;
			}
		} else {
			$ret = NEWSLETTERS_PKG_URL.'index.php';
		}
		return $ret;
	}


	function getEditions( $pNewsletterId = NULL ) {
		$ret = array();
		if( empty( $pNewsletterId ) ) {
			$nlId = $this->mNewsletterId;
		} elseif( BitBase::verifyId( $pNewsletterId ) ) {
			$nlId = $pNewsletterId;
		}
		if( !empty( $nlId ) ) {
			$listHash = array( 'nl_id' => $nlId  );
			$ret = BitNewsletterEdition::getList( $listHash );
		}
		return $ret;
	}
	
	function getUserSubscriptions( $pUserId, $pEmail ) {
		global $gBitDb;
		$query = "SELECT `content_id` AS hash_key, ms.* FROM `".BIT_DB_PREFIX."mail_subscriptions` ms WHERE `user_id`=? OR `email`=?";
		$ret = $gBitDb->getAssoc( $query, array( $pUserId, $pEmail ) );
		return $ret;
	}


}

?>
