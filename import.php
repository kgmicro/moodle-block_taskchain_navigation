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
 * blocks/taskchain_navigation/accesscontrol.js
 *
 * @package    blocks
 * @subpackage taskchain_navigation
 * @copyright  2014 Gordon Bateson <gordon.bateson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/xmlize.php');
require_once($CFG->dirroot.'/lib/uploadlib.php');

$id = required_param('id', PARAM_INT); // block_instance id
$plugin = 'block_taskchain_navigation';

if (! $block_instance = $DB->get_record('block_instances', array('id' => $id))) {
    print_error('invalidinstanceid', $plugin);
}
if (! $block = $DB->get_record('block', array('name' => $block_instance->blockname))) {
    print_error('invalidblockid', $plugin, $block_instance->blockid);
}
if (! $context = $DB->get_record('context', array('id' => $block_instance->parentcontextid))) {
    print_error('invalidcontextid', $plugin, $block_instance->parentcontextid);
}
if (! $course = $DB->get_record('course', array('id' => $context->instanceid))) {
    print_error('invalidcourseid', $plugin, $context->instanceid);
}

require_login($course->id);

if (class_exists('context_course')) {
    $context = context_course::instance($course->id);
} else {
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
}
require_capability('moodle/site:manageblocks', $context);

if (optional_param('cancel', '', PARAM_ALPHA)) {
    $url = $CFG->wwwroot.'/course/view.php?id='.$course->id.'&instanceid='.$block_instance->id.'&blockaction=config';
    if (function_exists('sesskey')) {
        $url .= '&sesskey='.sesskey();
    }
    // return to block config page
    redirect($url);
}

$blockname = get_string('blockname', $plugin);
$pagetitle = get_string('importsettings', $plugin);

// $SCRIPT is set by initialise_fullme() in 'lib/setuplib.php'
// It is the path below $CFG->wwwroot of this script
$url = new moodle_url($SCRIPT, array('id' => $id));

$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('incourse');
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($pagetitle, $url);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
echo $OUTPUT->box_start('generalbox');

if (data_submitted()) {
    // import settings from xml file

    // check session
    if (function_exists('require_sesskey')) {
        require_sesskey();
    } else if (function_exists('confirm_sesskey')) {
        confirm_sesskey();
    }

    // get upload manager and do standard check on uploaded file
    $um = new upload_manager('', false, false, $course);
    if ($um->preprocess_files() && taskchain_navigation_import($block_instance)) {
        // successful import
        $msg   = get_string('validimportfile', $plugin);
        $style = 'notifysuccess';
        $url   = $CFG->wwwroot.'/course/view.php?id='.$course->id;
    } else {
        // import didn't work - shouldn't happen !!
        $msg   = get_string('invalidimportfile', $plugin);
        $style = 'notifyproblem';
        $url   = $CFG->wwwroot.'/blocks/taskchain_navigation/import.php?id='.$id;
    }

    notify($msg, $style);
    echo $OUTPUT->continue_button($url);

} else {
    // show the import form
    taskchain_navigation_import_form($course, $block_instance, $plugin);
}

echo $OUTPUT->box_end();
echo $OUTPUT->footer($course);

/**
 * taskchain_navigation_import_form
 *
 * @param xxx $course
 * @param xxx $block_instance
 */
function taskchain_navigation_import_form($course, $block_instance, $plugin) {
    global $CFG, $OUTPUT;

    echo '<form method="post" action="import.php" enctype="multipart/form-data">'."\n";
    echo '<table border="0" cellpadding="4" cellspacing="4" width="600" style="margin: auto;">'."\n";
    echo '<tr>'."\n";

    echo '<td align="left" valign="top">'."\n";
    print_string('filetoimport', 'glossary');
    echo ' ';
    //helpbutton('filetoimport', get_string('filetoimport', 'glossary'), 'glossary');
    echo $OUTPUT->help_icon('filetoimport', 'glossary');
    echo '<br />';
    echo '<span style="font-size:smaller;">(';
    print_string('maxsize', '', display_size(get_max_upload_file_size($CFG->maxbytes, $course->maxbytes)));
    echo ')</span>'."\n";
    echo '</td>'."\n";

    echo '<td align="left" valign="top">'."\n";
    upload_print_form_fragment(); // file upload element + trailing <br />
    echo '<input type="submit" name="import" value="'.get_string('importsettings', $plugin).'" />'."\n";
    echo ' &nbsp; &nbsp; ';
    echo '<input type="submit" name="cancel" value="'.get_string('cancel').'" />'."\n";
    echo '<input type="hidden" name="id" value="'.$block_instance->id.'" />'."\n";
    if (function_exists('sesskey')) {
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />'."\n";
    }
    echo '</td>'."\n";

    echo '</tr>'."\n";
    echo '</table>'."\n";
    echo '</form>'."\n";
}

/**
 * taskchain_navigation_import
 *
 * @param xxx $block_instance
 * @return boolean true if import was successful, false otherwise
 */
function taskchain_navigation_import($block_instance) {
    global $DB;

    if (! $file = array_shift($_FILES)) {
        return false;
    }
    if (! isset($file['tmp_name'])) {
        return false;
    }
    if (! file_exists($file['tmp_name'])) {
        return false;
    }
    if (! is_file($file['tmp_name'])) {
        return false;
    }
    if (! is_readable($file['tmp_name'])) {
        return false;
    }
    if (! $xml = file_get_contents($file['tmp_name'])) {
        return false;
    }
    if (! $xml = xmlize($xml, 0)) {
        return false;
    }
    if (! isset($xml['TASKCHAINNAVIGATIONBLOCK']['#']['CONFIGFIELDS'][0]['#']['CONFIGFIELD'])) {
        return false;
    }

    $configfield = &$xml['TASKCHAINNAVIGATIONBLOCK']['#']['CONFIGFIELDS'][0]['#']['CONFIGFIELD'];
    $config = unserialize(base64_decode($block_instance->configdata));

    if (empty($config)) {
        $config = new stdClass();
    }

    $i = 0;
    while (isset($configfield[$i]['#'])) {
        $name = $configfield[$i]['#']['NAME'][0]['#'];
        $value = $configfield[$i]['#']['VALUE'][0]['#'];
        $config->$name = $value;
        $i++;
    }

    if ($i==0) {
        return false;
    }

    $block_instance->configdata = base64_encode(serialize($config));
    $DB->set_field('block_instances', 'configdata', $block_instance->configdata, array('id' => $block_instance->id));
    return true;
}

/**
 * This function prints out a number of upload form elements.
 *
 * @param int $numfiles The number of elements required (optional, defaults to 1)
 * @param array $names Array of element names to use (optional, defaults to FILE_n)
 * @param array $descriptions Array of strings to be printed out before each file bit.
 * @param boolean $uselabels -Whether to output text fields for file descriptions or not (optional, defaults to false)
 * @param array $labelnames Array of element names to use for labels (optional, defaults to LABEL_n)
 * @param int $coursebytes $coursebytes and $maxbytes are used to calculate upload max size ( using {@link get_max_upload_file_size})
 * @param int $modbytes $coursebytes and $maxbytes are used to calculate upload max size ( using {@link get_max_upload_file_size})
 * @param boolean $return -Whether to return the string (defaults to false - string is echoed)
 * @return string Form returned as string if $return is true
 */
function upload_print_form_fragment($numfiles=1, $names=null, $descriptions=null, $uselabels=false, $labelnames=null, $coursebytes=0, $modbytes=0, $return=false) {
    global $CFG;
    $maxbytes = get_max_upload_file_size($CFG->maxbytes, $coursebytes, $modbytes);
    $str = '<input type="hidden" name="MAX_FILE_SIZE" value="'. $maxbytes .'" />'."\n";
    for ($i = 0; $i < $numfiles; $i++) {
        if (is_array($descriptions) && !empty($descriptions[$i])) {
            $str .= '<strong>'. $descriptions[$i] .'</strong><br />';
        }
        $name = ((is_array($names) && !empty($names[$i])) ? $names[$i] : 'FILE_'.$i);
        $str .= '<input type="file" size="50" name="'. $name .'" alt="'. $name .'" /><br />'."\n";
        if ($uselabels) {
            $lname = ((is_array($labelnames) && !empty($labelnames[$i])) ? $labelnames[$i] : 'LABEL_'.$i);
            $str .= get_string('uploadlabel').' <input type="text" size="50" name="'. $lname .'" alt="'. $lname
                .'" /><br /><br />'."\n";
        }
    }
    if ($return) {
        return $str;
    } else {
        echo $str;
    }
}
