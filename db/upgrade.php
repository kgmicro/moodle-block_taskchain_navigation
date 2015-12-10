<?php
/**
 * blocks/taskchain_navigation/db/upgrade.php
 *
 * @package    blocks
 * @subpackage taskchain_navigation
 * @copyright  2014 Gordon Bateson <gordon.bateson@gmail.com>
 * @license    you may not copy of distribute any part of this package without prior written permission
 */

// prevent direct access to this script
defined('MOODLE_INTERNAL') || die();

function xmldb_block_taskchain_navigation_upgrade($oldversion=0) {

    global $CFG, $DB;

    $result = true;

    $newversion = 2014051601;
    if ($oldversion < $newversion) {
        update_capabilities('block/taskchain_navigation');
        upgrade_block_savepoint($result, "$newversion", 'taskchain_navigation');
    }

    return $result;
}
