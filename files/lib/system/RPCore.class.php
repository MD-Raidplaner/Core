<?php

namespace rp\system;

use wcf\system\application\AbstractApplication;

// define current raidplaner version
\define('RP_VERSION', '1.0.0 Alpha 1');

/**
 * This class extends the main WCF class by raidplaner specific functions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RPCore extends AbstractApplication
{
    protected $abbreviation = 'rp';
}
