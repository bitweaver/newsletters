<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_newsletters/Attic/BitMailer.php,v 1.40 2008/01/02 18:26:31 spiderr Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: BitMailer.php,v 1.40 2008/01/02 18:26:31 spiderr Exp $
 *
 * Class that handles editions of newsletters
 * @package newsletters
 *
 * created 2005/12/08
 *
 * @author spiderr <spider@steelsun.com>
 *
 * @version $Revision: 1.40 $ $Date: 2008/01/02 18:26:31 $ $Author: spiderr $
 */

/**
 * required setup
 */
require_once( NEWSLETTERS_PKG_PATH.'BitNewsletter.php' );
require_once( UTIL_PKG_PATH.'phpmailer/class.phpmailer.php' );

/**
 * @package newsletters
 */
class BitMailer {
    // Set default variables for all new objects
    var $From;
    var $FromName;
    var $Host;
    var $Mailer;                         // Alternative to IsSMTP()
    var $WordWrap;
	function BitMailer () {
		global $gBitDb, $gBitSystem, $gBitLanguage;
		$this->mDb = $gBitDb;
	}

    // Replace the default error_handler
    function error_handler( $msg ) {
    	global $gBitDb;
    	bit_error_handler( NULL, NULL, NULL, "FULFILLMENT ERROR: MISSSING PDF for ORDER $pOrderId CID ".$prod->mInfo['related_content_id'], $pdfInfo['pdf_file'], '', $prod->mDb );
        print("My Site Error");
        print("Description:");
        printf("%s", $msg);
        exit;
    }

	function isRecipientQueued( $pRecipientMixed, $pContentId ) {
		if( BitBase::verifyId( $pRecipientMixed ) ) {
			$lookupCol = 'user_id';
		} else {
			$lookupCol = 'email';
		}
		return( $this->mDb->getOne( "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."mail_queue` WHERE `content_id`=? AND `$lookupCol`=?", array( $pContentId, $pRecipientMixed ) ) );
	}

	function queueRecipients( $pContentId, $pNewsletterContentId, $pRecipients, $pRequeue=FALSE ) {
		$ret = 0;
		if( !empty( $pRecipients ) && BitBase::verifyId( $pContentId ) ) {
			$queueTime = time();
			foreach( array_keys( $pRecipients ) AS $email ) {
				$unsub = $this->getUnsubscription( $email, $pContentId );
				$lookup = !empty( $pRecipients[$email]['user_id'] ) ? $pRecipients[$email]['user_id'] : $email;
				if( empty( $unsub ) && !$this->isRecipientQueued( $lookup, $pContentId ) ) {
					$insertHash['mail_queue_id'] = $this->mDb->GenID( 'mail_queue_id' );
					$insertHash['email'] = $email;
					if( !empty( $pRecipients[$email]['user_id'] ) ) {
						$insertHash['user_id'] = $pRecipients[$email]['user_id'];
					}
					$insertHash['content_id'] = $pContentId;
					$insertHash['nl_content_id'] = $pNewsletterContentId;
					$insertHash['queue_date'] = $queueTime;
					$this->mDb->associateInsert( BIT_DB_PREFIX.'mail_queue', $insertHash );
					$ret++;
				} elseif( empty( $unsub ) && $pRequeue ) {
					$bindVars = array( $queueTime, $pContentId );
					if( !empty( $pRecipients[$email]['user_id'] ) ) {
						$lookupCol = 'user_id';
						$bindVars[] = $pRecipients[$email]['user_id'];
					} else {
						$lookupCol = 'email';
						$bindVars[]  = $email;
					}
					$rs = $this->mDb->query( "UPDATE `".BIT_DB_PREFIX."mail_queue` SET `queue_date`=?, `begin_date`=NULL, `sent_date`=NULL, `last_read_date`=NULL, `mail_error`=NULL, `reads`=0 WHERE `content_id`=? AND `$lookupCol`=?", array( $bindVars ) );
					$ret++;
				}
			}
		}
		return $ret;
	}


	function tendQueue() {
		$this->mDb->StartTrans();
		$query = "SELECT *
				  FROM `".BIT_DB_PREFIX."mail_queue` mq
				  WHERE `sent_date` IS NULL AND `mail_error` IS NULL
				  ORDER BY `queue_date`,`user_id`,`email` ".$this->mDb->SQLForUpdate();
		if( $rs = $this->mDb->query( $query, NULL ) ) {
			while( $pick = $rs->fetchRow() ) {
				$this->sendQueue( $pick );
				$this->mDb->CompleteTrans();
				$this->mDb->StartTrans();
			}
		}
		$this->mDb->CompleteTrans();
	}

	function sendQueue( $pQueueMixed ) {
		global $gBitSmarty, $gBitSystem, $gBitLanguage;
		static $body = array();
		if( is_array( $pQueueMixed ) ) {
			$pick = $pQueueMixed;
		} elseif( is_numeric( $pQueueMixed ) ) {
			$pick = $this->mDb->GetRow( "SELECT * FROM `".BIT_DB_PREFIX."mail_queue` mq WHERE `mail_queue_id` = ? ".$this->mDb->SQLForUpdate(), array( $pQueueMixed ) );
		}

		if( !empty( $pick ) ) {	
			$startTime = microtime( TRUE );
			$this->mDb->query( "UPDATE `".BIT_DB_PREFIX."mail_queue` SET `begin_date`=? WHERE `mail_queue_id` = ? ", array( time(), $pick['mail_queue_id'] ) );
			if( !empty( $pick['user_id'] ) ) {
				$userHash = $this->mDb->getRow( "SELECT * FROM `".BIT_DB_PREFIX."users_users` WHERE `user_id`=?", array( $pick['user_id'] ) );
				$pick['full_name'] = BitUser::getDisplayName( FALSE, $userHash );
			} else {
				$pick['full_name'] = NULL;
			}
			if( !isset( $body[$pick['content_id']] ) ) {
				$gBitSmarty->assign( 'sending', TRUE );
				// We only support sending of newsletters currently
				$content = new BitNewsletterEdition( NULL, $pick['content_id'] );
				if( $content->load() ) {
					$body[$pick['content_id']]['body'] = $content->render();
					$body[$pick['content_id']]['subject'] = $content->getTitle();
					$body[$pick['content_id']]['reply_to'] = $content->getField( 'reply_to', $gBitSystem->getConfig( 'site_sender_email', $_SERVER['SERVER_ADMIN'] ) );
					$body[$pick['content_id']]['object'] = $content;
				} else {
					bit_log_error( $this->mErrors );
				}
//				$content[$pick['content_id']] = LibertyBase::getLibertyObject();
			}

			print "[ $pick[mail_queue_id] ] $pick[content_id] TO: $pick[email]\t";
			$unsub = $this->getUnsubscription( $pick['email'], $pick['content_id'] );
			if( !empty( $unsub ) ) {
				print " SKIPPED (unsubscribed) <br/>\n";
				$this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."mail_queue` WHERE `mail_queue_id`=?", array( $pick['mail_queue_id'] ) );
			} elseif( !empty( $body[$pick['content_id']] ) ) {
				$pick['url_code'] = md5( $pick['content_id'].$pick['email'].$pick['queue_date'] );
				$unsub = '';
				if( $body[$pick['content_id']]['object']->mNewsletter->getField('unsub_msg') ) {
					$gBitSmarty->assign( 'url_code', $pick['url_code'] );
				}
				$gBitSystem->preDisplay('');
				$gBitSmarty->assign( 'sending', TRUE );
				$gBitSmarty->assign( 'unsubMessage', $unsub );
				$gBitSmarty->assign( 'trackCode', $pick['url_code'] );
				$gBitSmarty->assign( 'mid', 'bitpackage:newsletters/view_edition.tpl' );
				$htmlBody = $gBitSmarty->fetch( 'bitpackage:newsletters/mail_edition.tpl' );
				$htmlBody = bit_add_clickthrough( $htmlBody, $pick['url_code'] );

				$mailer = new PHPMailer();
				$mailer->From     = $gBitSystem->getConfig( 'bitmailer_sender_email', $gBitSystem->getConfig( 'site_sender_email', $_SERVER['SERVER_ADMIN'] ) );
				$mailer->FromName = $gBitSystem->getConfig( 'bitmailer_from', $gBitSystem->getConfig( 'site_title' ) );
				$mailer->Host     = $gBitSystem->getConfig( 'bitmailer_servers', $gBitSystem->getConfig( 'kernel_server_name', '127.0.0.1' ) );
				$mailer->Mailer   = $gBitSystem->getConfig( 'bitmailer_protocol', 'smtp' ); // Alternative to IsSMTP()
				if( $gBitSystem->getConfig( 'bitmailer_smtp_username' ) ) {
					$mailer->SMTPAuth = TRUE;
					$mailer->Username = $gBitSystem->getConfig( 'bitmailer_smtp_username' );
				}
				if( $gBitSystem->getConfig( 'bitmailer_smtp_password' ) ) {
					$mailer->Password = $gBitSystem->getConfig( 'bitmailer_smtp_password' );
				}
				$mailer->WordWrap = $gBitSystem->getConfig( 'bitmailer_word_wrap', 75 );
				if( !$mailer->SetLanguage( $gBitLanguage->getLanguage(), UTIL_PKG_PATH.'phpmailer/language/' ) ) {
					$mailer->SetLanguage( 'en' );
				}
				$mailer->ClearReplyTos();
				$mailer->AddReplyTo( $body[$pick['content_id']]['reply_to'], $gBitSystem->getConfig( 'bitmailer_from' ) );
				$mailer->Body    = $htmlBody;
				$mailer->Subject = $body[$pick['content_id']]['subject'];
				$mailer->IsHTML( TRUE );
				$mailer->AltBody = '';
				$mailer->AddAddress( $pick['email'], $pick["full_name"] );
				if( $mailer->Send() ) {
					print " SENT ".round( microtime( TRUE ) - $startTime, 2)." secs<br/>\n"; flush();
					$updateQuery = "UPDATE `".BIT_DB_PREFIX."mail_queue` SET `sent_date`=?,`url_code`=?  WHERE `content_id`=? AND `email`=?";
					$this->mDb->query( $updateQuery, array( time(), $pick['url_code'], $pick['content_id'], $pick['email'] ) );
				} else {
					$updateQuery = "UPDATE `".BIT_DB_PREFIX."mail_queue` SET `mail_error`=?,`sent_date`=?  WHERE `content_id`=? AND `email`=?";
					$this->mDb->query( $updateQuery, array( $mailer->ErrorInfo, time(), $pick['content_id'], $pick['email'] ) );
					$pick['error'] = $mailer->ErrorInfo;
					$this->logError( $pick );
				}
			}
		}
	}

	function trackMail( $pUrlCode ) {
		global $gBitDb;
		$query = "UPDATE `".BIT_DB_PREFIX."mail_queue` SET `reads`=`reads`+1, `last_read_date`=?, `last_read_ip`=? WHERE `url_code`=? ";
		$gBitDb->query( $query, array( time(), $_SERVER['REMOTE_ADDR'], $pUrlCode ) );
	}

	function logError( $pInfo ) {
		if( !empty( $pInfo['url_code'] ) && !$this->mDb->getOne( "SELECT `url_code` FROM `".BIT_DB_PREFIX."mail_errors` WHERE `url_code`=?", array( $pInfo['url_code'] ) ) ) {
			$store['url_code'] = $pInfo['url_code'];
			$store['user_id'] = !empty( $pInfo['user_id'] ) ? $pInfo['user_id'] : NULL;
			$store['content_id'] = !empty( $pInfo['content_id'] ) ? $pInfo['content_id'] : NULL;
			$store['email'] = !empty( $pInfo['email'] ) ? $pInfo['email'] : NULL;
			$store['error_message'] = $pInfo['error'];
			$store['error_date'] = time();
			$this->mDb->associateInsert( BIT_DB_PREFIX."mail_errors", $store );
		}
		print "ERROR: ".$pInfo['error']."\n";
	}

	// Looks up the code from the url to determine if the unsubscribe URL is valid.
	// Can be statically called
	function lookupSubscription( $pLookup ) {
		global $gBitDb;
		$ret = NULL;
		if( is_array( $pLookup ) ) {
			$query = "SELECT mq.*, lc.title, tct.*, uu.`real_name`, uu.`login`, uu.`email` FROM `".BIT_DB_PREFIX."mail_queue` mq
						INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( mq.`content_id`=lc.`content_id` )
						INNER JOIN `".BIT_DB_PREFIX."liberty_content_types` tct ON( tct.`content_type_guid`=lc.`content_type_guid` )
						LEFT OUTER JOIN `".BIT_DB_PREFIX."users_users` uu ON( mq.`user_id`=uu.`user_id` )
					  WHERE mq.`".key( $pLookup )."`=? ";
			$ret = $gBitDb->getRow( $query, array( current( $pLookup ) ) );
		}
		return( $ret );
	}

	// Accepts a single row has containing the column of mail_subscriptions as the key to lookup the unsubscription info
	// Can be statically called
	function getUnsubscriptions( $pMixed ) {
		global $gBitDb;
		$ret = NULL;
		if( is_array( $pMixed ) ) {
			$col = key( $pMixed );
			$bindVars[] = current( $pMixed );
			$query = "SELECT ms.`content_id` AS `hash_key`, ms.*, uu.*, lc.title
					  FROM `".BIT_DB_PREFIX."mail_subscriptions` ms
						LEFT OUTER JOIN `".BIT_DB_PREFIX."users_users` uu ON( ms.`user_id`=uu.`user_id` )
						LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( ms.`content_id`=lc.`content_id` )
					  WHERE ms.`$col`=? ";
			$ret = $gBitDb->getAssoc( $query, $bindVars );
		}
		return( $ret );
	}

	function getUnsubscription( $pEmail, $pContentId ) {
		global $gBitDb;
		return $gBitDb->getRow( "SELECT * FROM `".BIT_DB_PREFIX."mail_subscriptions` ms LEFT JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`user_id`=ms.`user_id`) WHERE (ms.`content_id`=? OR `unsubscribe_all`='y') AND (ms.`email`=? OR uu.`email`=?)", array( $pContentId, $pEmail, $pEmail ) );
	}

	function storeSubscriptions( $pSubHash ) {
		global $gBitSystem, $gBitDb;
		$ret = FALSE;
		$query = "delete from `".BIT_DB_PREFIX."mail_subscriptions` where `".key( $pSubHash['sub_lookup'] )."`=?";
		$result = $gBitDb->query($query, array( current( $pSubHash['sub_lookup'] ) ) );
		$ret = TRUE;
		if( !empty( $pSubHash['unsub_content'] ) ) {
			foreach( $pSubHash['unsub_content'] as $conId ) {
				$storeHash = array();
				$storeHash['content_id'] = $conId;
				$storeHash['unsubscribe_all'] = !empty( $pSubHash['unsubscribe_all'] ) ? 'y' : NULL;
				$storeHash['unsubscribe_date'] = time();
				$storeHash[key( $pSubHash['sub_lookup'] )] = current( $pSubHash['sub_lookup'] );
				if( !empty( $pSubHash['response_content_id'] ) ) {
					$storeHash['response_content_id'] = $pSubHash['response_content_id'];
				}
				$gBitDb->associateInsert( BIT_DB_PREFIX."mail_subscriptions", $storeHash );
			}
		}
		return $ret;
	}

	function getQueue( &$pListHash ) {
		$ret = array();
		
		LibertyContent::prepGetList( $pListHash );
		
		$query = "SELECT mq.`mail_queue_id` AS `hash_key`, mq.*, lc.`title`, lc2.`title` AS newsletter_title
				  FROM `".BIT_DB_PREFIX."mail_queue` mq 
					INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (lc.content_id=mq.content_id)
					LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content` lc2 ON (lc2.content_id=mq.nl_content_id)
				  WHERE begin_date IS NULL 
				  ORDER BY queue_date";
		if( $rs = $this->mDb->query( $query ) ) {
			$ret = $rs->getAssoc();
		}
		
		return $ret;
	}

	function expungeQueueRow( $pQueueId ) {
		if( BitBase::verifyId( $pQueueId ) ) {
			$this->mDb->query( "DELETE FROM ".BIT_DB_PREFIX."mail_queue` WHERE `mail_queue_id`=?", array( $pQueueId ) );
		}
	}

	function storeClickthrough( $pUrlCode ) {
		global $gBitDb;

		$uri = substr( preg_replace( '/[&\?]?ct=[a-z0-9]{32}/', '', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ), 0, 250 );
		$query = "SELECT mc.`clicks`, mq.`content_id`, mq.`user_id`, mc.`clicked_url` FROM `".BIT_DB_PREFIX."mail_queue` mq
					LEFT JOIN `".BIT_DB_PREFIX."mail_clickthrough` mc ON (mq.`user_id`=mc.`user_id` AND mq.`content_id`=mc.`content_id` AND mc.`clicked_url`=?)
				  WHERE `url_code`=?";
		if( $row = $gBitDb->getRow( $query, array( $uri, $pUrlCode ) ) ) {
			if( $row['clicked_url'] ) {
				$gBitDb->query( "UPDATE `".BIT_DB_PREFIX."mail_clickthrough` SET `clicks`=`clicks`+1 WHERE  `user_id`=? AND `content_id`=? AND `clicked_url`=? ", array( $row['user_id'], $row['content_id'], $row['clicked_url'] ) );
			} else {
				$row['clicks'] = 1;
				$row['clicked_url'] = $uri;
				$gBitDb->associateInsert( BIT_DB_PREFIX.'mail_clickthrough', $row );
			}
		}
	}
}

// This will insert a ticket on all template URL's that have GET parameters.
function bit_add_clickthrough( $pSource, $pUrlCode ) {
	global $gBitUser, $gUrlCode;

	$gUrlCode = $pUrlCode;

	$pcre = '%href[\s]*=[\s]*["\']*((http|https)://[^\s"\']+?)(.*)%sU';

	$ret = preg_replace_callback( $pcre, 'process_clickthrough_match', $pSource	);

	return $ret;

}


function process_clickthrough_match( $matches ) {
	global $gUrlCode;
	$ret = $matches[0];
	if( strpos( $matches[0], '?' ) ) {
		$ret .= '&amp;ct='.$gUrlCode;
	} else {
		$ret .= '?ct='.$gUrlCode;
	}
	return $ret; 
}

?>
