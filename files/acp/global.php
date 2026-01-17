<?php

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */

// phpcs:disable PSR1.Files.SideEffects

// Constant to get relative path to the wcf-root-dir.
// This constant is already set in each package which got an own app.config.inc.php
if (!\defined('RELATIVE_RP_DIR')) {
    \define('RELATIVE_RP_DIR', '../');
}

// include config
require_once(__DIR__ . '/../app.config.inc.php');

// include WCF
require_once(RELATIVE_WCF_DIR . 'acp/global.php');

// include rp
require_once(RP_DIR . 'lib/system/RP.class.php');
new rp\system\RP();
