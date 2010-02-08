<?php
// $Header: /cvsroot/bitweaver/_bit_newsletters/index.php,v 1.28 2010/02/08 21:27:24 wjames5 Exp $

// Copyright (c) 2006 - bitweaver.org - Christian Fowler, Max Kremmel, et. al
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

// Initialization
require_once( '../kernel/setup_inc.php' );
$feedback = array();

$gDefaultCenter = 'bitpackage:newsletters/center_list_newsletters.tpl';
$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );

$gBitSmarty->assign( 'feedback', $feedback );

// Display the template
$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', tra( 'Newsletters' ) , array( 'display_mode' => 'display' ));
?>
