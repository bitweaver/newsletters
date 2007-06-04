<?php
// $Header: /cvsroot/bitweaver/_bit_newsletters/index.php,v 1.24 2007/06/04 03:51:22 spiderr Exp $

// Copyright (c) 2006 - bitweaver.org - Christian Fowler, Max Kremmel, et. al
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );
$feedback = array();

$gDefaultCenter = 'bitpackage:newsletters/center_list_newsletters.tpl';
$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

$gBitSmarty->assign( 'feedback', $feedback );

// Display the template
$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', tra( 'Newsletters' ) );
?>
