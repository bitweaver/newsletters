<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_newsletters/BitNewsletter.php,v 1.1 2005/12/09 18:51:22 spiderr Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: BitNewsletter.php,v 1.1 2005/12/09 18:51:22 spiderr Exp $
 *
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived tikiwiki classes that require database access.
 * @package blogs
 *
 * created 2004/10/20
 *
 * @author drewslater <andrew@andrewslater.com>, spiderr <spider@steelsun.com>
 *
 * @version $Revision: 1.1 $ $Date: 2005/12/09 18:51:22 $ $Author: spiderr $
 */

/**
 * required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyContent.php' );

define( 'BITNEWSLETTER_CONTENT_TYPE_GUID', 'bitnewsletter' );
define( 'BITNEWSLETTEREDITION_CONTENT_TYPE_GUID', 'bitnewsletteredition' );

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
		$this->mNlId = $pNlId;
		$this->mContentId = $pContentId;
		$this->mContentTypeGuid = BITNEWSLETTER_CONTENT_TYPE_GUID;
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
					  FROM `".BIT_DB_PREFIX."tiki_newsletters` tn
						INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tn.`content_id`=tc.`content_id` )
					  WHERE `$lookupColumn`=? $whereSql";
			$result = $this->mDb->query($query,$bindVars);
			if ($result->numRows()) {
				$this->mInfo = $result->fetchRow();
				$this->mNlId = $this->mInfo['nl_id'];
				$this->mContentId = $this->mInfo['content_id'];
			}
		}
		return( count( $this->mInfo ) );
	}

	function store( &$pParamHash ) { //$nl_id, $name, $description, $allow_user_sub, $allow_any_sub, $unsub_msg, $validate_addr) {
		if( $this->verify( $pParamHash ) ) {
			$this->mDb->StartTrans();
			if( parent::store( $pParamHash ) ) {
				if( $this->mNlId ) {
					$result = $this->mDb->associateUpdate( BIT_DB_PREFIX."tiki_newsletters", $pParamHash['newsletter_store'], array ( "name" => "nl_id", "value" => $this->mNlId ) );
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

	function get_subscribers($nl_id) {
		$query = "select email from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `valid`=? and `nl_id`=?";
		$result = $this->mDb->query($query, array('y',(int)$nl_id));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res["email"];
		}
		return $ret;
	}

	function remove_newsletter_subscription($nl_id, $email) {
		$valid = $this->mDb->getOne("select `valid` from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `nl_id`=? and `email`=?", array((int)$nl_id,$email));
		$query = "delete from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `nl_id`=? and `email`=?";
		$result = $this->mDb->query($query, array((int)$nl_id,$email));
		$this->update_users($nl_id);
	}

	function newsletter_subscribe($nl_id, $email) {
		global $gBitSmarty;
		global $user;
		global $sender_email;
		$info = $this->get_newsletter($nl_id);
		$gBitSmarty->assign('info', $info);
		$code = md5( BitUser::genPass() );
		$now = date("U");
		if ($info["validate_addr"] == 'y') {
			// Generate a code and store it and send an email  with the
			// URL to confirm the subscription put valid as 'n'
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$foopath = preg_replace('/tiki-admin_newsletter_subscriptions.php/', 'tiki-newsletters.php', $foo["path"]);
			$url_subscribe = httpPrefix(). $foopath;
			$query = "delete from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `nl_id`=? and `email`=?";
			$result = $this->mDb->query($query,array((int)$nl_id,$email));
			$query = "insert into `".BIT_DB_PREFIX."tiki_newsletter_subscriptions`(`nl_id`,`email`,`code`,`valid`,`subscribed`) values(?,?,?,?,?)";
			$result = $this->mDb->query($query,array((int)$nl_id,$email,$code,'n',(int)$now));
			// Now send an email to the address with the confirmation instructions
			$gBitSmarty->assign('mail_date', date("U"));
			$gBitSmarty->assign('mail_user', $user);
			$gBitSmarty->assign('code', $code);
			$gBitSmarty->assign('url_subscribe', $url_subscribe);
			$gBitSmarty->assign('server_name', $_SERVER["SERVER_NAME"]);
			$mail_data = $gBitSmarty->fetch('bitpackage:newsletters/confirm_newsletter_subscription.tpl');
			@mail($email, tra('Newsletter subscription information at '). $_SERVER["SERVER_NAME"], $mail_data,
				"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
		} else {
			$query = "delete from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `nl_id`=? and `email`=?";
			$result = $this->mDb->query($query,array((int)$nl_id,$email));
			$query = "insert into `".BIT_DB_PREFIX."tiki_newsletter_subscriptions`(`nl_id`,`email`,`code`,`valid`,`subscribed`) values(?,?,?,?,?)";
			$result = $this->mDb->query($query,array((int)$nl_id,$email,$code,'y',(int)$now));
		}
		$this->update_users($nl_id);
	}

	function confirm_subscription($code) {
		global $gBitSmarty;
		global $user;
		global $sender_email;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$url_subscribe = httpPrefix(). $foo["path"];
		$query = "select * from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `code`=?";
		$result = $this->mDb->query($query,array($code));

		if (!$result->numRows()) return false;

		$res = $result->fetchRow();
		$info = $this->get_newsletter($res["nl_id"]);
		$gBitSmarty->assign('info', $info);
		$query = "update `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` set `valid`=? where `code`=?";
		$result = $this->mDb->query($query,array('y',$code));
		// Now send a welcome email
		$gBitSmarty->assign('mail_date', date("U"));
		$gBitSmarty->assign('mail_user', $user);
		$gBitSmarty->assign('code', $res["code"]);
		$gBitSmarty->assign('url_subscribe', $url_subscribe);
		$mail_data = $gBitSmarty->fetch('bitpackage:newsletters/newsletter_welcome.tpl');
		@mail($res["email"], tra('Welcome to '). $info["name"] . tra(' at '). $_SERVER["SERVER_NAME"], $mail_data,
			"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
		return $this->get_newsletter($res["nl_id"]);
	}

	function unsubscribe($code) {
		global $gBitSmarty;
		global $user;
		global $sender_email;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$url_subscribe = httpPrefix(). $foo["path"];
		$query = "select * from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `code`=?";
		$result = $this->mDb->query($query,array($code));

		if (!$result->numRows()) return false;

		$res = $result->fetchRow();
		$info = $this->get_newsletter($res["nl_id"]);
		$gBitSmarty->assign('info', $info);
		$gBitSmarty->assign('code', $res["code"]);
		$query = "delete from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `code`=?";
		$result = $this->mDb->query($query,array($code));
		// Now send a bye bye email
		$gBitSmarty->assign('mail_date', date("U"));
		$gBitSmarty->assign('mail_user', $user);
		$gBitSmarty->assign('url_subscribe', $url_subscribe);
		$mail_data = $gBitSmarty->fetch('bitpackage:newsletters/newsletter_byebye.tpl');
		@mail($res["email"], tra('Bye bye from '). $info["name"] . tra(' at '). $_SERVER["SERVER_NAME"], $mail_data,
			"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
		$this->update_users($res["nl_id"]);
		return $this->get_newsletter($res["nl_id"]);
	}

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

	function update_users($nl_id) {
		$users = $this->mDb->getOne("select count(*) from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `nl_id`=?",array((int)$nl_id));
		$query = "update `".BIT_DB_PREFIX."tiki_newsletters` set `users`=? where `nl_id`=?";
		$result = $this->mDb->query($query,array($users,(int)$nl_id));
	}

	function getList( &$pListHash ) {
		if ( empty( $pParamHash["sort_mode"] ) ) {
			$pListHash['sort_mode'] = 'created_desc';
		}
		$this->prepGetList( $pListHash );
		$bindVars = array();
		if( !empty( $pListHash['find'] ) ) {
			$findesc = '%' . $pListHash['find'] . '%';
			$mid = " where (`name` like ? or `description` like ?)";
			$bindVars[] = $findesc;
			$bindVars[] = $findesc;
		} else {
			$mid = " ";
		}

		$query = "SELECT *
				  FROM `".BIT_DB_PREFIX."tiki_newsletters` tn INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tn.`content_id`=tc.`content_id`)
				  $mid
				  ORDER BY ".$this->mDb->convert_sortmode( $pListHash['sort_mode'] );
		$result = $this->mDb->query( $query, $bindVars, $pListHash['max_records'], $pListHash['offset'] );

		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_newsletters` $mid";
        $pListHash['total_records'] = $this->mDb->getOne( $query_cant, $bindVars );
		$pListHash['block_pages'] = 5;
		$pListHash['total_pages'] = ceil( $pListHash['total_records'] / $pListHash['max_records'] );
		$pListHash['current_page'] = (!empty( $pListHash['offset'] ) ? floor( $pListHash['offset'] / $pListHash['max_records'] ) + 1 : 1 );

		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["confirmed"] = $this->mDb->getOne("select count(*) from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `valid`=? and `nl_id`=?",array('y',(int)$res["nl_id"]));
			$ret[$res['nl_id']] = $res;
		}

		return $ret;
	}

	function list_newsletter_subscriptions($nl_id, $offset, $maxRecords, $sort_mode, $find) {
		$bindVars = array((int)$nl_id);
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `nl_id`=? and (`name` like ? or `description` like ?)";
			$bindVars[] = $findesc;
			$bindVars[] = $findesc;
		} else {
			$mid = " where `nl_id`=? ";
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` $mid order by ".$this->mDb->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from tiki_newsletter_subscriptions $mid";
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

	function get_unsub_msg($nl_id, $email) {
		$foo = parse_url($_SERVER["REQUEST_URI"]);

		$foo = str_replace('send_newsletters', 'newsletters', $foo);
		$url_subscribe = httpPrefix(). $foo["path"];
		$code = $this->mDb->getOne("select `code` from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `nl_id`=? and `email`=?",array((int)$nl_id,$email));
		$url_unsub = $url_subscribe . '?unsubscribe=' . $code;
		$msg = '<br/><br/>' . tra( 'You can unsubscribe from this newsletter following this link'). ": <a href='$url_unsub'>$url_unsub</a>";
		return $msg;
	}

	function remove_newsletter($nl_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_newsletters` where `nl_id`=?";
		$result = $this->mDb->query($query,array((int)$nl_id));
		$query = "delete from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `nl_id`=?";
		$result = $this->mDb->query($query,array((int)$nl_id));
		$this->remove_object('newsletter', $nl_id);
		return true;
	}

}













class BitNewsletterEdition extends LibertyContent {
	function BitNewsletterEdition( $pEditionId, $pContentId=NULL ) {
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
	}

	function replace_edition($nl_id, $subject, $data, $users) {
		$now = date("U");
		$query = "insert into `".BIT_DB_PREFIX."tiki_sent_newsletters`(`nl_id`,`subject`,`data`,`sent`,`users`) values(?,?,?,?,?)";
		$result = $this->mDb->query($query,array((int)$nl_id,$subject,$data,(int)$now,$users));
	}

	function get_edition($edition_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_sent_newsletters` where `edition_id`=?";
		$result = $this->mDb->query($query,array((int)$edition_id));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function list_editions($offset, $maxRecords, $sort_mode, $find) {
		$bindVars = array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " and (`subject` like ? or `data` like ?)";
			$bindVars[] = $findesc;
			$bindVars[] = $findesc;
		} else {
			$mid = " ";
		}

		$query = "select tsn.`edition_id`,tn.`nl_id`,`subject`,`data`,tsn.`users`,`sent`,`name` from `".BIT_DB_PREFIX."tiki_newsletters` tn, `".BIT_DB_PREFIX."tiki_sent_newsletters` tsn ";
		$query.= " where tn.`nl_id`=tsn.`nl_id` $mid order by ".$this->mDb->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_newsletters` tn, `".BIT_DB_PREFIX."tiki_sent_newsletters` tsn where tn.`nl_id`=tsn.`nl_id` $mid";
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

	function remove_edition($edition_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_sent_newsletters` where `edition_id`=$edition_id";
		$result = $this->mDb->query($query,array((int)$edition_id));
	}

}

$BitNewsletter = new BitNewsletter();

?>
