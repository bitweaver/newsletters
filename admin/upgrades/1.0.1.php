<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_newsletters/admin/upgrades/1.0.1.php,v 1.1 2009/03/25 02:32:49 spiderr Exp $
 */
global $gBitInstaller;

$infoHash = array(
	'package'      => NEWSLETTERS_PKG_NAME,
	'version'      => str_replace( '.php', '', basename( __FILE__ )),
	'description'  => "Minor fix to ip columns to support IPv6",
	'post_upgrade' => NULL,
);

// all we are doing is change the column type of user_id for liberty_content_history.
// postgresql < 8.2 doesn't allow easy column type changing
// and therefore we need to undergo this annoying dance.
$gBitInstaller->registerPackageUpgrade( $infoHash, array(

// copy data into new column
array( 'QUERY' =>
	// postgres > 8.2 needs to have the type cast
	array(
		'PGSQL' => array(	"ALTER TABLE `".BIT_DB_PREFIX."mail_queue ALTER `last_read_ip` TYPE VARCHAR(39)" ,
		),
		'OCI' => array(	"ALTER TABLE `".BIT_DB_PREFIX."mail_queue MODIFY (`last_read_ip` TYPE VARCHAR2(39))" ,
		),
		'MYSQL' => array(	"ALTER TABLE `".BIT_DB_PREFIX."mail_queue MODIFY `last_read_ip` TYPE VARCHAR(39)" ,
		),
	),
),

));
?>
