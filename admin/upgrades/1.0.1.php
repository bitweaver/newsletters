<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_newsletters/admin/upgrades/1.0.1.php,v 1.3 2009/04/01 15:10:37 dansut Exp $
 */
global $gBitInstaller;

$infoHash = array(
	'package'      => NEWSLETTERS_PKG_NAME,
	'version'      => str_replace( '.php', '', basename( __FILE__ )),
	'description'  => "Minor fix to ip columns to support IPv6",
	'post_upgrade' => NULL,
);

// Increase the size of the IP column to cope with IPv6
$gBitInstaller->registerPackageUpgrade( $infoHash, array(

array( 'QUERY' =>
	array(
		'PGSQL' => array( "ALTER TABLE `".BIT_DB_PREFIX."mail_queue` ALTER `last_read_ip` TYPE VARCHAR(39)" ,),
		'OCI'   => array( "ALTER TABLE `".BIT_DB_PREFIX."mail_queue` MODIFY (`last_read_ip` TYPE VARCHAR2(39))" ,),
		'MYSQL' => array( "ALTER TABLE `".BIT_DB_PREFIX."mail_queue` MODIFY `last_read_ip` VARCHAR(39)" ,),
	),
),

));
?>
