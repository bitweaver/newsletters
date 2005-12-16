<?php
// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/admin_newsletters_inc.php,v 1.1 2005/12/16 06:34:55 spiderr Exp $

$formNewsletterFeatures = array(
	"bitmailer_sender_email" => array(
		'label' => 'From Email',
		'note' => 'If empty, it will default to the site Sender Email',
		'default' => $gBitSystem->getPreference( 'sender_email', $_SERVER['SERVER_ADMIN'] ),
	),
	"bitmailer_from" => array(
		'label' => 'From Name',
		'note' => '',
		'default' => $gBitSystem->getPreference( 'siteTitle' ),
	),
	"bitmailer_servers" => array(
		'label' => 'Mail Servers',
		'note' => '',
		'default' => $gBitSystem->getPreference( 'feature_server_name', $_SERVER['HTTP_HOST'] ),
	),
	"bitmailer_protocol" => array(
		'label' => 'Protocol',
		'note' => '',
		'default' => 'smtp',
	),
	"bitmailer_word_wrap" => array(
		'label' => 'Word wrap',
		'note' => '',
		'default' => '75',
	),
);
$gBitSmarty->assign( 'formNewsletterFeatures',$formNewsletterFeatures );

$processForm = set_tab();

if( $processForm ) {

	foreach( array_keys( $formNewsletterFeatures ) as $key ) {
		if( empty( $_REQUEST[$key] ) || $_REQUEST[$key] != $formNewsletterFeatures[$key]['default'] ) {
			$gBitSystem->storePreference( $key, isset( $_REQUEST[$key] ) ? $_REQUEST[$key] : NULL );
		}
	}
}

?>
