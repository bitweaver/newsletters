<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_newsletters/BitNewsletter.php,v 1.13.2.6 2006/02/19 04:38:47 wolff_borg Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: BitNewsletter.php,v 1.13.2.6 2006/02/19 04:38:47 wolff_borg Exp $
 *
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived tikiwiki classes that require database access.
 * @package newsletters
 *
 * created 2004/10/20
 *
 * @author drewslater <andrew@andrewslater.com>, spiderr <spider@steelsun.com>
 *
 * @version $Revision: 1.13.2.6 $ $Date: 2006/02/19 04:38:47 $ $Author: wolff_borg $
 */

/**
 * required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyContent.php' );
require_once( NEWSLETTERS_PKG_PATH.'BitNewsletterEdition.php' );

define( 'BITNEWSLETTER_CONTENT_TYPE_GUID', 'bitnewsletter' );

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
	}

	function load() {
		if( $this->verifyId( $this->mNewsletterId ) || $this->verifyId( $this->mContentId ) ) {
			global $gBitSystem;

			$bindVars = array(); $selectSql = ''; $joinSql = ''; $whereSql = '';

			$lookupColumn = $this->verifyId( $this->mNewsletterId ) ? 'nl_id' : 'content_id';
			$lookupId = $this->verifyId( $this->mNewsletterId )? $this->mNewsletterId : $this->mContentId;
			array_push( $bindVars, $lookupId );

			$this->getServicesSql( 'content_load_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "SELECT *
					  FROM `".BIT_DB_PREFIX."tiki_newsletters` tn
						INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tn.`content_id`=tc.`content_id` )
					  WHERE tn.`$lookupColumn`=? $whereSql";
			$result = $this->mDb->query($query,$bindVars);
			if ($result->numRows()) {
				$this->mInfo = $result->fetchRow();
				$this->mNewsletterId = $this->mInfo['nl_id'];
				$this->mContentId = $this->mInfo['content_id'];
			}
		}
		return( count( $this->mInfo ) );
	}

	function store( &$pParamHash ) { //$nl_id, $name, $description, $allow_user_sub, $allow_any_sub, $unsub_msg, $validate_addr) {
		if( $this->verify( $pParamHash ) ) {
			$this->mDb->StartTrans();
			if( parent::store( $pParamHash ) ) {
				if( $this->mNewsletterId ) {
					$result = $this->mDb->associateUpdate( BIT_DB_PREFIX."tiki_newsletters", $pParamHash['newsletter_store'], array ( "name" => "nl_id", "value" => $this->mNewsletterId ) );
				} else {
					$pParamHash['newsletter_store']['content_id'] = $pParamHash['content_id'];
					$result = $this->mDb->associateInsert( BIT_DB_PREFIX."tiki_newsletters", $pParamHash['newsletter_store'] );
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

	function getSubscribers($nl_id) {
		$ret = array();
		if( $this->isValid() ) {
			$query = "select * from `".BIT_DB_PREFIX."tiki_mail_subscriptions` where `unsubscribe_date` is NULL and `nl_content_id`=?";
			if( $res = $this->mDb->query( $query, array( $this->mNewsletterId ) ) ) {
				$ret = $res->GetRows();
			}
		}
		return $ret;
	}

	function getAllSubscribers() {
		$ret = array();
		if( $this->isValid() ) {
			$query = "select * from `".BIT_DB_PREFIX."tiki_mail_subscriptions` where `nl_content_id`=?";
			if( $res = $this->mDb->query( $query, array( $this->mNewsletterId ) ) ) {
				$ret = $res->GetRows();
			}
		}
		return $ret;
	}

	function removeSubscription( $email, $notify = FALSE, $del_record = FALSE ) {
		if ($del_record) {
			$this->mDb->query("DELETE FROM `".BIT_DB_PREFIX."tiki_mail_subscriptions` WHERE `nl_content_id`=? AND `email`=?", array($this->mNewsletterId, $email));
		} else {
			$sub_code = $this->mDb->getOne("select `sub_code` from `".BIT_DB_PREFIX."tiki_mail_subscriptions` where `nl_content_id`=? and `email`=?", array($this->mNewsletterId, $email));
			$this->unsubscribe($sub_code, $notify);
		}
	}

	function subscribe( $email, $notify = FALSE ) {
		$ret = FALSE;
		if( $this->isValid() ) {
			global $gBitSystem;
			global $gBitSmarty;
			global $gBitUser;

			// Check for duplicates
			$all_subs = $this->getAllSubscribers();
			$duplicate = FALSE;
			foreach($all_subs as $sub) {
				if ($sub['email'] == $email) {
					$duplicate = TRUE;
					$sub_code = $sub['sub_code'];
				}
			}

			$sub_code = (!$duplicate) ? md5( BitUser::genPass() ) : $sub_code;
			$now = date("U");
			// Generate a code and store it and send an email  with the
			// URL to confirm the subscription put valid as 'n'
			if (!$duplicate) {
				$query = "insert into `".BIT_DB_PREFIX."tiki_mail_subscriptions`(`nl_content_id`,`email`,`sub_code`,`is_valid`,`subscribed_date`) values(?,?,?,?,?)";
				$result = $this->mDb->query( $query, array( $this->mNewsletterId, $email, $sub_code, 'n', (int)$now ) );
			}
			if( $notify && $this->mInfo['validate_addr'] == 'y' ) {
				// Now send an email to the address with the confirmation instructions
				$gBitSmarty->assign( 'sub_code', $sub_code );
				$mail_data = $gBitSmarty->fetch('bitpackage:newsletters/confirm_newsletter_subscription.tpl');
				@mail($email, tra('Newsletter subscription information at') . ' ' . $gBitSystem->getPreference( "bitmailer_from" ), $mail_data,
					"From: " . $gBitSystem->getPreference( "sender_email" ) . "\r\nContent-type: text/plain;charset=utf-8\r\n");
			}
			$ret = TRUE;
		}
		return $ret;
	}

	function confirmSubscription($sub_code, $notify = FALSE ) {
		global $gBitSystem;
		global $gBitSmarty;
		global $gBitUser;
		$query = "select * from `".BIT_DB_PREFIX."tiki_mail_subscriptions` where `sub_code`=?";
		$result = $this->mDb->query($query,array($sub_code));

		if (!$result->numRows()) return false;

		$res = $result->fetchRow();
		$this->mNewsletterId = $res['nl_content_id'];
		$this->load();
		$query = "update `".BIT_DB_PREFIX."tiki_mail_subscriptions` set `is_valid`=?,`unsubscribe_date`=? where `sub_code`=?";
		$result = $this->mDb->query($query,array('y',NULL,$sub_code));

		if ($notify) {
			// Now send a welcome email
			$gBitSmarty->assign( 'sub_code', $res["sub_code"] );
			$mail_data = $gBitSmarty->fetch('bitpackage:newsletters/newsletter_welcome.tpl');
			@mail($res["email"], tra('Welcome to') . ' ' . $gBitSystem->getPreference( "bitmailer_from" ), $mail_data,
				"From: " . $gBitSystem->getPreference( "sender_email" ) . "\r\nContent-type: text/plain;charset=utf-8\r\n");
		}
	}

	function unsubscribe($sub_code, $notify = TRUE ) {
		global $gBitSystem;
		global $gBitSmarty;
		global $gBitUser;
		$now = date("U");
		$query = "select * from `".BIT_DB_PREFIX."tiki_mail_subscriptions` where `sub_code`=?";
		$result = $this->mDb->query($query,array($sub_code));

		if (!$result->numRows()) return false;

		$res = $result->fetchRow();
		$this->mNewsletterId = $res['nl_content_id'];
		$this->load();
		$query = "update `".BIT_DB_PREFIX."tiki_mail_subscriptions` set `unsubscribe_date`=? where `sub_code`=? and `unsubscribe_date` is NULL";
		$result = $this->mDb->query($query,array($now, $sub_code));

		if ($notify) {
			// Now send a bye bye email
			$gBitSmarty->assign('sub_code', $res["sub_code"]);
			$mail_data = $gBitSmarty->fetch('bitpackage:newsletters/newsletter_byebye.tpl');
			@mail($res["email"], tra('Thank you from') . ' ' . $gBitSystem->getPreference( "bitmailer_from" ), $mail_data,
				"From: " . $gBitSystem->getPreference( "sender_email" ) . "\r\nContent-type: text/plain;charset=utf-8\r\n");
		}

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
			$users = $this->mDb->getOne( "select count(*) from `".BIT_DB_PREFIX."tiki_mail_subscriptions` where `nl_id`=?", array( $this->mNewsletterId ) );
			$query = "update `".BIT_DB_PREFIX."tiki_newsletters` set `users`=? where `nl_id`=?";
			$result = $this->mDb->query( $query, array( $users, $this->mNewsletterId ) );
		}
	}
*/

	function getList( &$pListHash ) {
		if ( empty( $pParamHash["sort_mode"] ) ) {
			$pListHash['sort_mode'] = 'created_desc';
		}
		$this->prepGetList( $pListHash );
		$bindVars = array();
		$mid = '';

		if( @$this->verifyId( $pListHash['nl_id'] ) ) {
			$mid .= ' AND tn.nl_id=? ';
			$bindVars[] = $pListHash['nl_id'];
		}

		if( !empty( $pListHash['find'] ) ) {
			$findesc = '%' . $pListHash['find'] . '%';
			$mid .= " AND (`name` like ? or `description` like ?)";
			$bindVars[] = $findesc;
			$bindVars[] = $findesc;
		}

		$query = "SELECT *
				  FROM `".BIT_DB_PREFIX."tiki_newsletters` tn INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tn.`content_id`=tc.`content_id`)
				  WHERE tn.`content_id`=tc.`content_id` $mid
				  ORDER BY ".$this->mDb->convert_sortmode( $pListHash['sort_mode'] );
		$result = $this->mDb->query( $query, $bindVars, $pListHash['max_records'], $pListHash['offset'] );

		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_newsletters` $mid";

		$ret = array();
		while( $res = $result->fetchRow() ) {
			$res['display_url'] = $this->getDisplayUrl( $res['nl_id'] );
			$res["confirmed"] = $this->mDb->getOne( "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."tiki_mail_subscriptions` WHERE `unsubscribe_date` IS NULL and `nl_content_id`=?",array( (int)$res["nl_id"] ) );
			$res["unsub_count"] = $this->mDb->getOne( "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."tiki_mail_subscriptions` WHERE `nl_content_id`=?",array( (int)$res["nl_id"] ) );
			$ret[$res['content_id']] = $res;
		}

		return $ret;
	}

/*	function list_newsletter_subscriptions($nl_id, $offset, $maxRecords, $sort_mode, $find) {
		$bindVars = array((int)$nl_id);
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `nl_content_id`=? and (`name` like ? or `description` like ?)";
			$bindVars[] = $findesc;
			$bindVars[] = $findesc;
		} else {
			$mid = " where `nl_content_id`=? ";
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_mail_subscriptions` $mid order by ".$this->mDb->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from tiki_mail_subscriptions $mid";
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
	function get_unsub_msg($nl_id, $email) {
		$foo = parse_url($_SERVER["REQUEST_URI"]);

		$foo = str_replace('send_newsletters', 'newsletters', $foo);
		$url_subscribe = httpPrefix(). $foo["path"];
		$sub_code = $this->mDb->getOne("select `sub_code` from `".BIT_DB_PREFIX."tiki_mail_subscriptions` where `nl_content_id`=? and `email`=?",array((int)$nl_id,$email));
		$url_unsub = $url_subscribe . '?unsubscribe=' . $sub_code;
		$msg = '<br/><br/>' . tra( 'You can unsubscribe from this newsletter following this link'). ": <a href='$url_unsub'>$url_unsub</a>";
		return $msg;
	}

	function expunge() {
		$ret = FALSE;
		if( $this->isValid() ) {
			$this->mDb->StartTrans();
			$query = "DELETE FROM `".BIT_DB_PREFIX."tiki_newsletters` where `nl_id`=?";
			$result = $this->mDb->query( $query, array( $this->mNewsletterId ) );
			// Clear out all individual subscriptions/unsubscriptions, but preserve the unsubscribe_all's
			$query = "DELETE FROM `".BIT_DB_PREFIX."tiki_mail_subscriptions` WHERE `nl_content_id`=? AND `unsubscribe_all` IS NOT NULL";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			$query = "UPDATE `".BIT_DB_PREFIX."tiki_mail_subscriptions` SET `nl_content_id`=NULL WHERE `nl_content_id`=? AND `unsubscribe_all` IS NOT NULL";
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
		$ret = NULL;
		if( !$this->verifyId( $pNewsletterId ) ) {
			$pNewsletterId = $this->mNewsletterId;
		}
		global $gBitSystem;
		if( $this->verifyId( $pNewsletterId ) ) {
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


	function getEditions() {
		$ret = array();
		if( $this->isValid() ) {
			$listHash = array( 'nl_id' => $this->mNewsletterId  );
			$ret = BitNewsletterEdition::getList( $listHash );
		}
		return $ret;
	}
}

?>
