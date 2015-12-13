<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

$plugin->component = 'block_taskchain_navigation';
$plugin->maturity  = MATURITY_STABLE;
$plugin->requires  = 2010112400; // Moodle 2.0
$plugin->version   = 2015121361;
$plugin->release   = '2015-12-13 (61)';

if (defined('ANY_VERSION')) { // Moodle >= 2.2
    $plugin->dependencies = array('mod_taskchain' => ANY_VERSION);
}
