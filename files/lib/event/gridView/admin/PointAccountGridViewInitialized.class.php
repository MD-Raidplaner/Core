<?php

namespace rp\event\gridView\admin;

use rp\system\gridView\admin\PointAccountGridView;
use wcf\event\IPsr14Event;

/**
 * Indicates that the point account grid view has been initialized.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class PointAccountGridViewInitialized implements IPsr14Event
{
    public function __construct(
        public readonly PointAccountGridView $gridView,
    ) {}
}
