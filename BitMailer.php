<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_newsletters/Attic/BitMailer.php,v 1.10 2005/12/29 15:31:29 spiderr Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: BitMailer.php,v 1.10 2005/12/29 15:31:29 spiderr Exp $
 *
 * Class that handles editions of newsletters
 * @package newsletters
 *
 * created 2005/12/08
 *
 * @author spiderr <spider@steelsun.com>
 *
 * @version $Revision: 1.10 $ $Date: 2005/12/29 15:31:29 $ $Author: spiderr $
 */

/**
 * required setup
 */
require_once( NEWSLETTERS_PKG_PATH.'BitNewsletter.php' );
require_once( UTIL_PKG_PATH.'phpmailer/class.phpmailer.php' );

class BitMailer extends phpmailer {
    // Set default variables for all new objects
    var $From;
    var $FromName;
    var $Host;
    var $Mailer;                         // Alternative to IsSMTP()
    var $WordWrap;
	function BitMailer () {
		global $gBitDb, $gBitSystem, $gBitLanguage;
		$this->mDb = $gBitDb;
		$this->From     = $gBitSystem->getPreference( 'bitmailer_sender_email', $gBitSystem->getPreference( 'sender_email', $_SERVER['SERVER_ADMIN'] ) );
		$this->FromName = $gBitSystem->getPreference( 'bitmailer_from', $gBitSystem->getPreference( 'siteTitle' ) );
		$this->Host     = $gBitSystem->getPreference( 'bitmailer_servers', $gBitSystem->getPreference( 'feature_server_name', $_SERVER['HTTP_HOST'] ) );
		$this->Mailer   = $gBitSystem->getPreference( 'bitmailer_protocol', 'smtp' ); // Alternative to IsSMTP()
		$this->WordWrap = $gBitSystem->getPreference( 'bitmailer_word_wrap', 75 );
		if( !$this->SetLanguage( $gBitLanguage->getLanguage(), UTIL_PKG_PATH.'phpmailer/language/' ) ) {
			$this->SetLanguage( 'en' );
		}
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

	function queueRecipients( $pContentId, $pNewsletterContentId, $pRecipients ) {
		$ret = 0;
		if( !empty( $pRecipients ) && BitBase::verifyId( $pContentId ) ) {
			$queueTime = time();
			foreach( array_keys( $pRecipients ) AS $email ) {
				$insertHash['email'] = $email;
				if( !empty( $pRecipients[$email]['user_id'] ) ) {
					$insertHash['user_id'] = $pRecipients[$email]['user_id'];
				}
				$insertHash['content_id'] = $pContentId;
				$insertHash['nl_content_id'] = $pNewsletterContentId;
				$insertHash['queue_date'] = $queueTime;
				$this->mDb->associateInsert( BIT_DB_PREFIX.'tiki_mail_queue', $insertHash );
				$ret++;
			}
		}
		return $ret;
	}

	function tendQueue() {
		global $gBitSmarty;
		$body = array();
		$this->mDb->StartTrans();
		$query = "SELECT *
				  FROM `".BIT_DB_PREFIX."tiki_mail_queue` tmq
				  WHERE `sent_date` IS NULL ".$this->mDb->SQLForUpdate();
		while( ($rs = $this->mDb->query( $query, NULL, 1 )) && $rs->RowCount() ) {
			$pick = $rs->fields;
			if( !empty( $pick['user_id'] ) ) {
				$userHash = $this->mDb->getRow( "SELECT * FROM `".BIT_DB_PREFIX."users_users` WHERE `user_id`=?", array( $pick['user_id'] ) );
				$pick['full_name'] = BitUser::getDisplayName( FALSE, $userHash );
			} else {
				$pick['full_name'] = NULL;
			}
			if( !isset( $body[$pick['content_id']] ) ) {
				// We only support sending of newsletters currently
				$content = new BitNewsletterEdition( NULL, $pick['content_id'] );
				if( $content->load() ) {
					$body[$pick['content_id']]['body'] = $content->render();
					$body[$pick['content_id']]['subject'] = $content->getTitle();
				}
//				$content[$pick['content_id']] = LibertyBase::getLibertyObject();
			}
			if( !empty( $body[$pick['content_id']] ) ) {
				$pick['url_code'] = md5( $pick['content_id'].$pick['email'].$pick['queue_date'] );
				$gBitSmarty->assign( 'url_code', $pick['url_code'] );
				$unsub = $gBitSmarty->fetch( 'bitpackage:newsletters/unsubscribe_inc.tpl' );
				$htmlBody = $unsub . $body[$pick['content_id']]['body'] . $unsub;

				if( !$this->sendMail( $pick, $body[$pick['content_id']]['subject'], $htmlBody ) ) {
					$this->logError( $pick );
				}
				$updateQuery = "UPDATE `".BIT_DB_PREFIX."tiki_mail_queue` SET `sent_date`=?,`url_code`=?  WHERE `content_id`=? AND `email`=?";
				$this->mDb->query( $updateQuery, array( time(), $pick['url_code'], $pick['content_id'], $pick['email'] ) );
				$this->mDb->CompleteTrans();
				$this->mDb->StartTrans();
			}
			$rs->MoveNext();
		}
		$this->mDb->CompleteTrans();

		$this->mDb->query( "UPDATE tiki_mail_queue set sent_date=NULL" );
	}


	function sendMail( $pRecipient, $pSubject, $pHtmlBody ) {
		$ret = TRUE;
		$this->Body    = $pHtmlBody;
		$this->Subject = $pSubject;
		$this->IsHTML( TRUE );
		$this->AltBody = '';
		$this->AddAddress( $pRecipient['email'], $pRecipient["full_name"] );
		if(!$this->Send()) {
			$ret = FALSE;
		}

		// Clear all addresses and attachments for next loop
		$this->ClearAddresses();
		$this->ClearAttachments();
		return $ret;
	}

	function logError( $pInfo ) {
		if( !empty( $pInfo['url_code'] ) && !$this->mDb->getOne( "SELECT `url_code` FROM `".BIT_DB_PREFIX."tiki_mail_errors` WHERE `url_code`=?", array( $pInfo['url_code'] ) ) ) {
			$store['url_code'] = $pInfo['url_code'];
			$store['user_id'] = !empty( $pInfo['user_id'] ) ? $pInfo['user_id'] : NULL;
			$store['content_id'] = !empty( $pInfo['content_id'] ) ? $pInfo['content_id'] : NULL;
			$store['email'] = !empty( $pInfo['email'] ) ? $pInfo['email'] : NULL;
			$store['error_message'] = $this->ErrorInfo;
			$store['error_date'] = time();
			$this->mDb->associateInsert( BIT_DB_PREFIX."tiki_mail_errors", $store );
		}
	}

	// Looks up the code from the url to determine if the unsubscribe URL is valid.
	// Can be statically called
	function lookupSubscription( $pLookup ) {
		global $gBitDb;
		$ret = NULL;
		if( is_array( $pLookup ) ) {
			$query = "SELECT tmq.*, tc.title, tct.*, uu.`real_name`, uu.`login`, uu.`email` FROM `".BIT_DB_PREFIX."tiki_mail_queue` tmq
						INNER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tmq.`content_id`=tc.`content_id` )
						INNER JOIN `".BIT_DB_PREFIX."tiki_content_types` tct ON( tct.`content_type_guid`=tc.`content_type_guid` )
						LEFT OUTER JOIN `".BIT_DB_PREFIX."users_users` uu ON( tmq.`user_id`=uu.`user_id` )
					  WHERE tmq.`".key( $pLookup )."`=? ";
			$ret = $gBitDb->getRow( $query, array( current( $pLookup ) ) );
		}
		return( $ret );
	}

	// Accepts a single row has containing the column of tiki_mail_subscriptions as the key to lookup the unsubscription info
	// Can be statically called
	function getUnsubscriptions( $pMixed ) {
		global $gBitDb;
		$ret = NULL;
		if( is_array( $pMixed ) ) {
			$col = key( $pMixed );
			$bindVars[] = current( $pMixed );
			$query = "SELECT tms.`nl_content_id` AS `hash_key`, tms.*, uu.*, tc.title
					  FROM `".BIT_DB_PREFIX."tiki_mail_subscriptions` tms
						LEFT OUTER JOIN `".BIT_DB_PREFIX."users_users` uu ON( tms.`user_id`=uu.`user_id` )
						LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_content` tc ON( tms.`nl_content_id`=tc.`content_id` )
					  WHERE tms.`$col`=? ";
			$ret = $gBitDb->getAssoc( $query, $bindVars );
		}
		return( $ret );
	}

	function storeSubscriptions( $pSubHash ) {
		global $gBitSystem, $gBitDb;
		$ret = FALSE;
		$query = "delete from `".BIT_DB_PREFIX."tiki_mail_subscriptions` where `".key( $pSubHash['sub_lookup'] )."`=?";
		$result = $gBitDb->query($query, array( current( $pSubHash['sub_lookup'] ) ) );
		$ret = TRUE;
		if( !empty( $pSubHash['unsub_content'] ) ) {
			foreach( $pSubHash['unsub_content'] as $conId ) {
				$storeHash = array();
				$storeHash['nl_content_id'] = $conId;
				$storeHash['unsubscribe_all'] = !empty( $pSubHash['unsubscribe_all'] ) ? 'y' : NULL;
				$storeHash['unsubscribe_date'] = time();
				$storeHash[key( $pSubHash['sub_lookup'] )] = current( $pSubHash['sub_lookup'] );
				if( !empty( $pSubHash['response_content_id'] ) ) {
					$storeHash['response_content_id'] = $pSubHash['response_content_id'];
				}
				$gBitDb->associateInsert( BIT_DB_PREFIX."tiki_mail_subscriptions", $storeHash );
			}
		}
		return $ret;
	}


}
?>