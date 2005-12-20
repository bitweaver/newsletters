<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_newsletters/Attic/BitMailer.php,v 1.2 2005/12/20 22:05:07 spiderr Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: BitMailer.php,v 1.2 2005/12/20 22:05:07 spiderr Exp $
 *
 * Class that handles editions of newsletters
 * @package newsletters
 *
 * created 2005/12/08
 *
 * @author spiderr <spider@steelsun.com>
 *
 * @version $Revision: 1.2 $ $Date: 2005/12/20 22:05:07 $ $Author: spiderr $
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
		global $gBitDb, $gBitSystem;
		$this->mDb = $gBitDb;
		$this->From     = $gBitSystem->getPreference( 'bitmailer_sender_email', $gBitSystem->getPreference( 'sender_email', $_SERVER['SERVER_ADMIN'] ) );
		$this->FromName     = $gBitSystem->getPreference( 'bitmailer_from', $gBitSystem->getPreference( 'siteTitle' ) );
		$this->Host     = $gBitSystem->getPreference( 'bitmailer_servers', $gBitSystem->getPreference( 'feature_server_name', $_SERVER['HTTP_HOST'] ) );
		$this->Mailer   = $gBitSystem->getPreference( 'bitmailer_protocol', 'smtp' ); // Alternative to IsSMTP()
		$this->WordWrap = $gBitSystem->getPreference( 'bitmailer_word_wrap', 75 );
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

	function queueRecipients( $pContentId, $pRecipients ) {
		$ret = 0;
		if( !empty( $pRecipients ) && BitBase::verifyId( $pContentId ) ) {
			$queueTime = time();
			foreach( array_keys( $pRecipients ) AS $email ) {
				$insertHash['email'] = $email;
				if( !empty( $pRecipients[$email]['user_id'] ) ) {
					$insertHash['user_id'] = $pRecipients[$email]['user_id'];
				}
				$insertHash['content_id'] = $pContentId;
				$insertHash['queue_date'] = $queueTime;
				$this->mDb->associateInsert( BIT_DB_PREFIX.'tiki_mail_queue', $insertHash );
				$ret++;
			}
		}
		return $ret;
	}

	function tendQueue() {
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
				$this->sendMail( $pick, $body[$pick['content_id']]['subject'], $body[$pick['content_id']]['body'] );
die;
				$this->mDb->query( $updateQuery, array( time(), $pick['content_id'], $pick['email'] ) );
				$updateQuery = "UPDATE `".BIT_DB_PREFIX."tiki_mail_queue` SET `sent_date`=? WHERE `content_id`=? AND `email`=?";
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
			echo "There has been a mail error sending to " . $pRecipient["email"] . "<br>";
		}

		// Clear all addresses and attachments for next loop
		$this->ClearAddresses();
		$this->ClearAttachments();
		return $ret;
	}

}
?>