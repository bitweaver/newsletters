<?php
global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;

$upgrades = array(

'BONNIE' => array(
	'CLYDE' => array(
array( 'RENAMETABLE' => array(
        'tiki_sent_newsletters' => 'tiki_newsletters_editions',
)),
// STEP 1
array( 'DATADICT' => array(
array( 'RENAMECOLUMN' => array(
	'tiki_quicktags' => array( '`tagId`' => '`tag_id` I4 AUTO' ),
)),
array( 'ALTER' => array(
	'tiki_quicktags' => array(
		'tagpos' => array( '`tagpos`', 'I4' ), // , 'NOTNULL' ),
		'format_guid' => array( '`format_guid`', 'VARCHAR(16)' ), // , 'NOTNULL' ),
	),
))
)),

// STEP 3
array( 'QUERY' =>
	array( 'SQL92' => array(
		),
)),

	)
)
);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( NEWSLETTERS_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}


?>
