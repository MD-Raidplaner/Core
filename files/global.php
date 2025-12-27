<?php

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
// include config
require_once(dirname(__FILE__) . '/app.config.inc.php');

// include wcf
require_once(RELATIVE_WCF_DIR . 'global.php');

// include rp
require_once(RP_DIR . 'lib/system/RP.class.php');
new rp\system\RP();
