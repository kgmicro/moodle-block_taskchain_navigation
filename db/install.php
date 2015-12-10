<?php
/**
 * blocks/taskchain_navigation/db/install.php
 *
 * @package    blocks
 * @subpackage taskchain_navigation
 * @copyright  2014 Gordon Bateson <gordon.bateson@gmail.com>
 * @license    you may not copy of distribute any part of this package without prior written permission
 */

// prevent direct access to this script
defined('MOODLE_INTERNAL') || die();

function xmldb_block_taskchain_navigation_install() {
    global $DB;

    // convert all quizport_navigation blocks to taskchain_navigation
    $params = array('blockname' => 'quizport_navigation');
    $DB->set_field('block_instances', 'blockname', 'taskchain_navigation', $params);
}
