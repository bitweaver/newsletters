<?php

class NlLib extends BitBase {
	function NlLib() {
		parent::BitBase();
	}

	function replace_newsletter($nl_id, $name, $description, $allow_user_sub, $allow_any_sub, $unsub_msg, $validate_addr) {
		if ($nl_id) {
			$query = "update `".BIT_DB_PREFIX."tiki_newsletters` set `name`=?, `description`=?, `allow_user_sub`=?, `allow_any_sub`=?, `unsub_msg`=?, `validate_addr`=?  where `nl_id`=?";
			$result = $this->mDb->query($query, array($name,$description,$allow_user_sub,$allow_any_sub,$unsub_msg,$validate_addr,(int)$nl_id));
		} else {
			$now = date("U");
			$query = "insert into `".BIT_DB_PREFIX."tiki_newsletters`(`name`,`description`,`allow_user_sub`,`allow_any_sub`,`unsub_msg`,`validate_addr`,`last_sent`,`editions`,`users`,`created`) ";
      $query.= " values(?,?,?,?,?,?,?,?,?,?)";
			$result = $this->mDb->query($query, array($name,$description,$allow_user_sub,$allow_any_sub,$unsub_msg,$validate_addr,(int)$now,0,0,(int)$now));
			$queryid = "select max(`nl_id`) from `".BIT_DB_PREFIX."tiki_newsletters` where `created`=?";
			$nl_id = $this->mDb->getOne($queryid, array((int)$now));
		}
		return $nl_id;
	}

	function replace_edition($nl_id, $subject, $data, $users) {
		$now = date("U");
		$query = "insert into `".BIT_DB_PREFIX."tiki_sent_newsletters`(`nl_id`,`subject`,`data`,`sent`,`users`) values(?,?,?,?,?)";
		$result = $this->mDb->query($query,array((int)$nl_id,$subject,$data,(int)$now,$users));
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

	function get_newsletter($nl_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_newsletters` where `nl_id`=?";
		$result = $this->mDb->query($query,array((int)$nl_id));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function get_edition($edition_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_sent_newsletters` where `edition_id`=?";
		$result = $this->mDb->query($query,array((int)$edition_id));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function update_users($nl_id) {
		$users = $this->mDb->getOne("select count(*) from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `nl_id`=?",array((int)$nl_id));
		$query = "update `".BIT_DB_PREFIX."tiki_newsletters` set `users`=? where `nl_id`=?";
		$result = $this->mDb->query($query,array($users,(int)$nl_id));
	}

	function getList( &$pListHash ) {
		if ( empty( $_REQUEST["sort_mode"] ) ) {
			$pListHash['sort_mode'] = 'created_desc';
		}
		$this->prepGetList( $pListHash );
		$bindvars = array();
		if( !empty( $pListHash['find'] ) ) {
			$findesc = '%' . $pListHash['find'] . '%';
			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = " ";
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_newsletters` $mid order by ".$this->mDb->convert_sortmode( $pListHash['sort_mode'] );
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_newsletters` $mid";
		$result = $this->mDb->query( $query, $bindvars, $pListHash['max_records'], $pListHash['offset'] );
		$cant = $this->mDb->getOne( $query_cant, $bindvars );
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["confirmed"] = $this->mDb->getOne("select count(*) from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` where `valid`=? and `nl_id`=?",array('y',(int)$res["nl_id"]));
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_editions($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " and (`subject` like ? or `data` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = " ";
		}

		$query = "select tsn.`edition_id`,tn.`nl_id`,`subject`,`data`,tsn.`users`,`sent`,`name` from `".BIT_DB_PREFIX."tiki_newsletters` tn, `".BIT_DB_PREFIX."tiki_sent_newsletters` tsn ";
		$query.= " where tn.`nl_id`=tsn.`nl_id` $mid order by ".$this->mDb->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_newsletters` tn, `".BIT_DB_PREFIX."tiki_sent_newsletters` tsn where tn.`nl_id`=tsn.`nl_id` $mid";
		$result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->mDb->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_newsletter_subscriptions($nl_id, $offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array((int)$nl_id);
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `nl_id`=? and (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = " where `nl_id`=? ";
		}

		$query = "select * from `".BIT_DB_PREFIX."tiki_newsletter_subscriptions` $mid order by ".$this->mDb->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from tiki_newsletter_subscriptions $mid";
		$result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->mDb->getOne($query_cant,$bindvars);
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

	function remove_edition($edition_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_sent_newsletters` where `edition_id`=$edition_id";
		$result = $this->mDb->query($query,array((int)$edition_id));
	}

}

$nllib = new NlLib();

?>
