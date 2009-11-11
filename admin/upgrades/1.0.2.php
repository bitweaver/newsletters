<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_newsletters/admin/upgrades/1.0.2.php,v 1.1 2009/11/11 22:47:03 lsces Exp $
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
