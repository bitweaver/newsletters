<?php
// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/admin_newsletters_inc.php,v 1.6 2008/01/02 15:55:20 spiderr Exp $

$formNewsletterFeatures = array(
	"bitmailer_sender_email" => array(
		'label' => 'From Email',
		'note' => 'If empty, it will default to the site Sender Email',
		'default' => $gBitSystem->getConfig( 'site_sender_email', $_SERVER['SERVER_ADMIN'] ),
	),
	"bitmailer_from" => array(
		'label' => 'From Name',
		'note' => '',
		'default' => $gBitSystem->getConfig( 'siteTitle' ),
	),
	"bitmailer_servers" => array(
		'label' => 'Mail Servers',
		'note' => '',
		'default' => $gBitSystem->getConfig( 'kernel_server_name', '127.0.0.1' ),
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

if( !empty( $_POST ) ) {

	foreach( array_keys( $formNewsletterFeatures ) as $key ) {
		if( empty( $_REQUEST[$key] ) || $_REQUEST[$key] != $formNewsletterFeatures[$key]['default'] ) {
			$gBitSystem->storeConfig( $key, isset( $_REQUEST[$key] ) ? $_REQUEST[$key] : NULL );
		}
	}
}

?>
