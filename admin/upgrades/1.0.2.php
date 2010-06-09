<?php
/**
 * @version $Header$
 */
global $gBitInstaller;

$infoHash = array(
	'package'      => NEWSLETTERS_PKG_NAME,
	'version'      => str_replace( '.php', '', basename( __FILE__ )),
	'description'  => "Replace reserved name reads with hits",
	'post_upgrade' => NULL,
);

// Increase the size of the IP column to cope with IPv6
$gBitInstaller->registerPackageUpgrade( $infoHash, array(

array( 'DATADICT' => array(
	array( 'RENAMECOLUMN' => array(
		'mail_queue' => array(
			'`reads`' => '`hits` I2 NOTNULL DEFAULT 0'
		),
	)),
)),

));
?>
