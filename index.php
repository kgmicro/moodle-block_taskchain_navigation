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

/**
 * Display a list of links to TaskChain Navigation block(s) within the current course.
 *
 * @package    blocks
 * @subpackage taskchain_navigation
 * @copyright  2014 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 */

/** get required libraries */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

$courseid = required_param('id', PARAM_INT);
if (! $course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}

if (class_exists('context')) {
    $context = context_course::instance($courseid);
} else {
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
}

require_course_login($course);
require_capability('moodle/course:manageactivities', $context);

$blockname = 'taskchain_navigation';
$pluginname = 'block_'.$blockname;

$PAGE->set_url("/blocks/$blockname/index.php", array('id' => $courseid));
$PAGE->set_title($course->fullname);
$PAGE->set_heading($course->shortname);

// output starts here
echo $OUTPUT->header();

$params = array('blockname' => $blockname,
                'parentcontextid' => $context->id);
if (! $instances = $DB->get_records('block_instances', $params)) {
    echo $OUTPUT->heading(get_string('noinstancesincourse', $pluginname), 2);
    echo $OUTPUT->continue_button(new moodle_url('/course/view.php', array('id' => $courseid)));
    echo $OUTPUT->footer();
    die();
}

$table = new html_table();

$i = 1;
foreach ($instances as $instance) {
    $row = new html_table_row();
    $row->cells[] = new html_table_cell(html_writer::tag('b', $i++));

    $config = unserialize(base64_decode($instance->configdata));
    if ($text = $config->coursenamefield) {
        switch ($text) {
            case 'fullname':
            case 'shortname':
                $text = $course->$text;
                break;
            case 'yourgrade':
            case 'grade':
                $text = get_string($text, 'grades');
                break;
            case 'currentgrade':
            case 'finalgrade':
                $text = get_string($text, $pluginname);
                break;
            case 'specifictext':
                $text = $config->coursenametext;
                break;
        }
    }
    //$text = self::filter_text($text);
    $params = array('id' => $courseid,
                    'sesskey' => sesskey(),
                    'bui_editid' => $instance->id);
    $url = new moodle_url('/course/view.php', $params);
    $text = html_writer::link($url, $text);

    $row->cells[] = new html_table_cell($text);
    $table->data[] = $row;
}

echo $OUTPUT->heading(get_string('pluginnameblocks', $pluginname), 2);
echo html_writer::table($table);
echo $OUTPUT->footer();
