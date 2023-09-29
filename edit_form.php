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
 * blocks/taskchain_navigation/edit_form.php
 *
 * @package    blocks
 * @subpackage taskchain_navigation
 * @copyright 2014 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 */

/** Prevent direct access to this script */
defined('MOODLE_INTERNAL') || die();

/**
 * block_taskchain_navigation_mod_form
 *
 * @copyright 2014 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 * @package    mod
 * @subpackage taskchain
 */
class block_taskchain_navigation_edit_form extends block_edit_form {

    /**
     * constructor
     *
     * @param object $actionurl
     * @param object $block
     * @param object $page
     * @return void, but will update $this
     */
    function __construct($actionurl, $block, $page) {
        if (isset($block->config->showactivitygrades)) {
            $value = $block->config->showactivitygrades;
        } else {
            $value = 'all'; // default value
        }
        if ($value=='all') {
            $block->config->showactivitygrades = 'all';
            $block->config->showactivitygradestypes = '';
        } else {
            $block->config->showactivitygrades = 'specific';
            $block->config->showactivitygradestypes = $value;
        }
        parent::__construct($actionurl, $block, $page);
    }

    /**
     * specific_definition
     *
     * @param object $mform
     * @return void, but will update $mform
     */
    protected function specific_definition($mform) {
        global $PAGE;

        $this->set_form_id($mform, get_class($this));

        // cache the plugin name, because
        // it is quite long and we use it a lot
        $plugin = 'block_taskchain_navigation';

        // cache commonly used menu options
        $depth_options  = range(0, 10);
        $length_options = range(0, 20);
        $grade_options  = array_reverse(range(0, 100), true);
        $keep_options   = array(0 => get_string('remove'), 1 => get_string('keep'));
        $text_options   = array('size' => 10);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'title');
        //-----------------------------------------------------------------------------

        $this->add_field_description($mform, $plugin, 'description');

        $name = 'title';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string($name, $plugin), array('size' => 50));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, 'grades', 'coursegradecategory');
        //-----------------------------------------------------------------------------

        $name = 'showcourse';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string('show'));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'coursename';
        $config_name = 'config_'.$name;
        $options = array(
            'fullname'     => get_string('fullname'),
            'shortname'    => get_string('shortname'),
            'grade'        => get_string('grade', 'grades'),
            'yourgrade'    => get_string('yourgrade', 'grades'),
            'coursegrade'  => get_string('coursegrade', $plugin),
            'currentgrade' => get_string('currentgrade', $plugin),
            'specifictext' => get_string('specifictext', $plugin).' ...',
        );
        $elements = array(
            $mform->createElement('select', $config_name.'field', '', $options),
            $mform->createElement('text',   $config_name.'text', '', array('size' => 24)),
        );
        $mform->addElement('group', $config_name.'elements', get_string('name'), $elements, ' ', false);
        $mform->setType($config_name.'field', PARAM_ALPHA);
        $mform->setDefault($config_name.'field', $this->defaultvalue($name));
        $mform->setType($config_name.'text', PARAM_TEXT);
        $mform->setDefault($config_name.'text', $this->defaultvalue($name.'text'));
        $mform->disabledIf($config_name.'text', $config_name.'field', 'ne', 'specifictext');
        $mform->disabledIf($config_name.'elements', 'config_showcourse', 'ne', '1');
        $mform->addHelpButton($config_name.'elements', $name.'field', $plugin);

        /* =========================================== *\
        $name = 'coursegradeposition';
        $config_name = 'config_'.$name;
        $options = array(
            '0' => get_string('positionfirst', 'grades'),
            '1' => get_string('positionlast', 'grades')
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);
        \* =========================================== */

        //-----------------------------------------------------------------------------
        $this->add_header($mform, 'grades', 'gradecategories');
        //-----------------------------------------------------------------------------

        $name = 'minimumdepth';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string($name, $plugin), $depth_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'maximumdepth';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string($name, $plugin), $depth_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categoryskipempty';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categoryskiphidden';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categoryskipzeroweighted';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categorycollapse';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('no'),
            1 => get_string('usechildcategory', $plugin),
            2 => get_string('useparentcategory', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'gradecategorynames');
        //-----------------------------------------------------------------------------

        $name = 'categoryshortnames';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categoryshowweighting';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categorysectionnum';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'categoryignorechars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string($name, $plugin), $text_options);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'categoryprefixes');
        //-----------------------------------------------------------------------------

        $name = 'categoryprefixlength';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('prefixlength', $plugin), $length_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixlength', $plugin);

        $name = 'categoryprefixchars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('prefixchars', $plugin), $text_options);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixchars', $plugin);

        $name = 'categoryprefixlong';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('short', $plugin),
            1 => get_string('long', $plugin)
        );
        $mform->addElement('select', $config_name, get_string('prefixlong', $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixlong', $plugin);

        $name = 'categoryprefixkeep';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('prefixkeep', $plugin), $keep_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixkeep', $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'categorysuffixes');
        //-----------------------------------------------------------------------------

        $name = 'categorysuffixlength';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('suffixlength', $plugin), $length_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixlength', $plugin);

        $name = 'categorysuffixchars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('suffixchars', $plugin), $text_options);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixchars', $plugin);

        $name = 'categorysuffixlong';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('short', $plugin),
            1 => get_string('long', $plugin)
        );
        $mform->addElement('select', $config_name, get_string('suffixlong', $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixlong', $plugin);

        $name = 'categorysuffixkeep';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('suffixkeep', $plugin), $keep_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixkeep', $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'sections');
        //-----------------------------------------------------------------------------

        $name = 'sectionshowhidden';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('hide'),
            1 => get_string('showwithlink', $plugin),
            2 => get_string('showwithoutlink', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'sectionshowburied';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('hide'),
            1 => get_string('promotetovisiblegradecategory', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'sectionshowungraded';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('hide'),
            1 => get_string('ungradedshow1', $plugin),
            2 => get_string('ungradedshow2', $plugin),
            3 => get_string('ungradedshow3', $plugin),
            4 => get_string('ungradedshow4', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        /* =========================================== *\
        $name = 'sectionshowuncategorized';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('showabovemaingradecategories', $plugin),
            1 => get_string('showbelowmaingradecategories', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);
        \* =========================================== */

        $name = 'sectionshowzeroweighted';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('hide'),
            1 => get_string('show'),
            2 => get_string('mergewithungradedsections', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'sectiontitles');
        //-----------------------------------------------------------------------------

        $name = 'sectiontitletags';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('sectiontitletags', $plugin), $text_options);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'sectionshorttitles';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'sectionignorecase';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string('ignorecase', $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'ignorecase', $plugin);

        $name = 'sectionignorechars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string($name, $plugin), $text_options);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'sectionprefixes');
        //-----------------------------------------------------------------------------

        $name = 'sectionprefixlength';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('prefixlength', $plugin), $length_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixlength', $plugin);

        $name = 'sectionprefixchars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('prefixchars', $plugin), $text_options);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixchars', $plugin);

        $name = 'sectionprefixlong';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('short', $plugin),
            1 => get_string('long', $plugin)
        );
        $mform->addElement('select', $config_name, get_string('prefixlong', $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixlong', $plugin);

        $name = 'sectionprefixkeep';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('prefixkeep', $plugin), $keep_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'prefixkeep', $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'sectionsuffixes');
        //-----------------------------------------------------------------------------

        $name = 'sectionsuffixlength';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('suffixlength', $plugin), $length_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixlength', $plugin);

        $name = 'sectionsuffixchars';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('suffixchars', $plugin), $text_options);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixchars', $plugin);

        $name = 'sectionsuffixlong';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('short', $plugin),
            1 => get_string('long', $plugin)
        );
        $mform->addElement('select', $config_name, get_string('suffixlong', $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixlong', $plugin);

        $name = 'sectionsuffixkeep';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string('suffixkeep', $plugin), $keep_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, 'suffixkeep', $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, 'moodle', 'groups');
        //-----------------------------------------------------------------------------

        $name = 'groupsmenu';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'groupslabel';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'groupscountusers';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'groupssort';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('name'),
            1 => get_string('idnumber'),
            2 => get_string('timecreated', $plugin),
            3 => get_string('timemodified', $plugin)
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'loginasmenu';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'loginassort';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('fullname'),
            1 => get_string('firstname'),
            2 => get_string('lastname'),
            3 => get_string('username'),
            4 => get_string('idnumber')
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, 'moodle', 'grades');
        //-----------------------------------------------------------------------------

        /* =========================================== *\
        $name = 'gradedisplay';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('displaypoints',      'grades'),
            1 => get_string('displaypercent',     'grades'),
            2 => get_string('displayweighted',    'grades'),
            3 => get_string('displaylettergrade', 'grades')
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);
        \* =========================================== */

        $name = 'showaverages';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'highgrade';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string($name, $plugin), $grade_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'mediumgrade';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string($name, $plugin), $grade_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'lowgrade';
        $config_name = 'config_'.$name;
        $mform->addElement('select', $config_name, get_string($name, $plugin), $grade_options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        if ($options = $this->get_graded_activity_types()) {
            $this->add_field_showactivitygrades($mform, $plugin, $options);
            $this->add_field_gradelinecolor($mform, $plugin, $text_options);
            $this->add_field_gradelinestyle($mform, $plugin);
            $this->add_field_gradelinewidth($mform, $plugin, $text_options);
        }

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'coursesections');
        //-----------------------------------------------------------------------------

        $name = 'sectionjumpmenu';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'sectionnumbers';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'singlesection';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'defaultsection';
        $config_name = 'config_'.$name;
        $options = range(0, $this->block->config->numsections);
        $options[0] = get_string('currentsection', $plugin);
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        if (function_exists('course_get_format')) {
            // Moodle >= 2.3 has its own section navigation
        } else {
            $this->add_field_arrowup($mform, $plugin, true);
            $this->add_field_arrowdown($mform, $plugin);
        }

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'coursepageshortcuts');
        //-----------------------------------------------------------------------------

        $name = 'editsettings';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'accesscontrol';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'gradebooklink';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $this->add_field_hiddensections($mform, $plugin);
        $this->add_field_languages($mform, $plugin);

        $name = 'currentsection';
        $config_name = 'config_'.$name;
        $mform->addElement('selectyesno', $config_name, get_string($name, $plugin));
        $mform->setType($config_name, PARAM_INT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        //-----------------------------------------------------------------------------
        $this->add_header($mform, $plugin, 'styles');
        //-----------------------------------------------------------------------------

        $name = 'moodlecss';
        $config_name = 'config_'.$name;
        $options = array(
            0 => get_string('none'),
            1 => get_string($this->get_stringname_simpleview(), 'grades'),
            2 => get_string('pluginname', 'gradereport_user')
        );
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_RAW);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'externalcss';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string('externalcss', $plugin), array('size' => 50));
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        $name = 'internalcss';
        $config_name = 'config_'.$name;
        $params = array('wrap' => 'virtual', 'rows' => 6, 'cols' => 48);
        $mform->addElement('textarea', $config_name, get_string($name, $plugin), $params);
        $mform->setType($config_name, PARAM_TEXT);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);

        if (isset($this->block->instance)) {
            if ($mycourses = $this->get_mycourses()) {

                //-----------------------------------------------------------------------------
                $this->add_header($mform, $plugin, 'applyselectedvalues');
                //-----------------------------------------------------------------------------

                $name = 'mycourses';
                $config_name = 'config_'.$name;
                $params = array('multiple' => 'multiple', 'size' => min(5, count($mycourses)));
                $mform->addElement('select', $config_name, get_string($name, $plugin), $mycourses, $params);
                $mform->setType($config_name, PARAM_INT);
                $mform->setDefault($config_name, $this->defaultvalue($name));
                $mform->addHelpButton($config_name, $name, $plugin);
            }
        }

        // Insert the javascript to adjust layout of form elements
        // and add "itemselect" checkboxes if necessary.
        $PAGE->requires->js_call_amd('block_taskchain_navigation/form', 'init');
    }

    /**
     * set_form_id
     *
     * @param  object $mform
     * @param  string $id
     * @return mixed default value of setting
     */
    protected function set_form_id($mform, $id) {
        $attributes = $mform->getAttributes();
        $attributes['id'] = $id;
        $mform->setAttributes($attributes);
    }

    /**
     * get default value for a setting in this block
     *
     * @param  string $name of setting
     * @return mixed default value of setting
     */
    protected function defaultvalue($name) {
        if (isset($this->block->config->$name)) {
            return $this->block->config->$name;
        } else {
            return null;
        }
    }

    /**
     * get_stringname_simpleview
     *
     * @return string name: 'simpleview' on Moodle <= 2.7, otherwise 'categoriesanditems'
     */
    protected function get_stringname_simpleview() {
        $manager = get_string_manager();

        $plugin = 'grades';
        $oldstring = 'simpleview';

        $method = 'string_exists';
        $newstring = 'gradebooksetup';
        if ($manager->$method($newstring, $plugin)) {
            // Moodle >= 3.0
            return $newstring;
        }

        $method = 'string_deprecated';
        $newstring = 'categoriesanditems';
        if (method_exists($manager, $method) && $manager->$method($oldstring, 'grades')) {
            // Moodle >= 2.8
            return $newstring;
        }

        // Moodle <= 2.7
        return $oldstring;
    }

    /**
     * add_header
     *
     * @param object  $mform
     * @param string  $component
     * @param string  $name of string
     * @param boolean $expanded (optional, default=TRUE)
     * @return void, but will update $mform
     */
    protected function add_header($mform, $component, $name, $expanded=true) {
        $label = get_string($name, $component);
        $mform->addElement('header', $name, $label);
        if (method_exists($mform, 'setExpanded')) {
            $mform->setExpanded($name, $expanded);
        }
    }

    /**
     * add_field_description
     *
     * @param object  $mform
     * @param string  $plugin
     * @param string  $name of field
     * @return void, but will update $mform
     */
    protected function add_field_description($mform, $plugin, $name) {
        global $OUTPUT;

        $label = get_string($name);
        $text = get_string('block'.$name, $plugin);

        $export = get_string('exportsettings', $plugin);
        $params = array('id' => $this->block->instance->id); // URL params
        $params = array('href' => new moodle_url('/blocks/taskchain_navigation/export.php', $params));
        $export = html_writer::tag('a', $export, $params).' '.$OUTPUT->help_icon('exportsettings', $plugin);
        $export = html_writer::tag('div', $export, array('class' => 'exportsettings'));

        $import = get_string('importsettings', $plugin);
        $params = array('id' => $this->block->instance->id); // URL params
        $params = array('href' => new moodle_url('/blocks/taskchain_navigation/import.php', $params));
        $import = html_writer::tag('a', $import, $params).' '.$OUTPUT->help_icon('importsettings', $plugin);
        $import = html_writer::tag('div', $import, array('class' => 'importsettings'));

        $mform->addElement('static', $name, $label, $text.$export.$import);
    }

    /**
     * add_field_hiddensections
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_field_hiddensections($mform, $plugin) {
        $name = 'hiddensections';
        $style = $name.'style';
        $title = $name.'title';

        $config_name  = 'config_'.$name;
        $config_style = 'config_'.$style;
        $config_title = 'config_'.$title;

        $elements_name = 'elements_'.$name;

        $options_style = array(
            0 => get_string('checkboxes', $plugin),
            1 => get_string('multiselect', $plugin)
        );
        $options_title = array(
            0 => get_string('sectionnumber', $plugin),
            1 => get_string('sectiontext', $plugin),
            2 => get_string('sectionnumbertext', $plugin)
        );

        $elements = array(
            $mform->createElement('selectyesno', $config_name),
            $mform->createElement('select', $config_style, '', $options_style),
            $mform->createElement('select', $config_title, '', $options_title)
        );

        $mform->addGroup($elements, $elements_name, get_string($name, $plugin), ' ', false);
        $mform->addHelpButton($elements_name, $name, $plugin);

        $mform->setType($config_name,  PARAM_INT);
        $mform->setType($config_style, PARAM_INT);
        $mform->setType($config_title, PARAM_INT);

        $mform->setDefault($config_name,  $this->defaultvalue($name));
        $mform->setDefault($config_style, $this->defaultvalue($style));
        $mform->setDefault($config_title, $this->defaultvalue($title));
    }

    /**
     * add_field_languages
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_field_languages($mform, $plugin) {
        global $COURSE, $DB;

        $modinfo = get_fast_modinfo($COURSE);

        $langs = array('' => get_string('default'));
        $translations = get_string_manager()->get_list_of_translations();

        $fields = array('name', 'summary');
        $search = '/<span[^>]*class="multilang"[^>]*>/';

        // Get localized lang names used in coure section names/summaries.
        $sections = block_taskchain_navigation::get_section_info_all($modinfo);
        foreach ($sections as $sectionnum => $section) {
            foreach ($fields as $field) {
                if (preg_match_all($search, $section->$field, $matches)) {
                    foreach ($matches[0] as $match) {
                        if (preg_match('/lang="(\w+)"/', $match, $lang)) {
                            $lang = $lang[1];
                            if (array_key_exists($lang, $translations)) {
                                $langs[$lang] = $translations[$lang];
                            }
                        }
                    }
                }
            }
        }        
        unset($translations);

        // cache some useful strings and textbox params
        $total = html_writer::tag('small', get_string('total', $plugin).': ');
        $head  = html_writer::tag('small', get_string('head',  $plugin).': ');
        $tail  = html_writer::tag('small', get_string('tail',  $plugin).': ');
        $params = array('size' => 2);

        $elements = array();
        foreach ($langs as $lang => $text) {

            $lang = substr($lang, 0, 2);
            $namelength = 'config_namelength'.$lang;
            $headlength = 'config_headlength'.$lang;
            $taillength = 'config_taillength'.$lang;

            // add line break (except before the first language, the default, which has $lang=='')
            if (count($elements)) {
                $elements[] = $this->create_linebreak($mform);
            }

            // add length fields for this language
            $elements[] = $mform->createElement('static', '', '', $total);
            $elements[] = $mform->createElement('text', $namelength, '', $params);
            $elements[] = $mform->createElement('static', '', '', $head);
            $elements[] = $mform->createElement('text', $headlength, '', $params);
            $elements[] = $mform->createElement('static', '', '', $tail);
            $elements[] = $mform->createElement('text', $taillength, '', $params);
            if ($lang || count($langs) > 1) {
                $elements[] = $mform->createElement('static', '', '', html_writer::tag('small', $text));
            }
        }

        $name = 'sectiontextlength';
        $mform->addGroup($elements, $name, get_string($name, $plugin), ' ', false);
        $mform->addHelpButton($name, $name, $plugin);

        foreach ($elements as $element) {
            if ($element->getType()=='text') {
                $mform->setType($element->getName(), PARAM_INT);
            }
        }
    }

    /**
     * add_field_arrowup
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_field_arrowup($mform, $plugin, $add_js=false) {
        $name = 'arrowup';
        $config_name = 'config_'.$name;
        $arrows = array('&#x2191', '&#x219F', '&#x21A5',
                        '&#x21B0', '&#x21B1', '&#x21BE',
                        '&#x21BF', '&#x21C8', '&#x21D1',
                        '&#x21DF', '&#x21E1', '&#x21E7');
        $this->add_field_arrow($mform, $plugin, $name, $arrows, $add_js);
    }

    /**
     * add_field_arrowdown
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_field_arrowdown($mform, $plugin, $add_js=false) {
        $name = 'arrowdown';
        $config_name = 'config_'.$name;
        $arrows = array('&#x2193', '&#x21A1', '&#x21A7',
                        '&#x21B2', '&#x21B3', '&#x21C2',
                        '&#x21C3', '&#x21CA', '&#x21D3',
                        '&#x21DE', '&#x21E3', '&#x21E9');
        $this->add_field_arrow($mform, $plugin, $name, $arrows, $add_js);
    }

    /**
     * add_field_arrow
     *
     * @param object  $mform
     * @param string  $plugin
     * @param string  $name
     * @param array   $arrows
     * @param array   $add_js
     * @return void, but will update $mform
     */
    protected function add_field_arrow($mform, $plugin, $name, $arrows, $add_js) {

        $config_name = 'config_'.$name;
        $elements_name = 'elements_'.$name;

        $static = array();
        $params = array('href' => '#', 'onclick' => 'return select_arrow(this)');
        foreach ($arrows as $arrow) {
            $static[] = html_writer::tag('a', $arrow, $params);
        }
        $static = implode(' &nbsp; ', $static);
        $static = html_writer::tag('span', $static, array('class' => 'arrows'));

        $elements = array();
        if ($add_js) {
            $js = '';
            $js .= '<script type="text/javascript">'."\n";
            $js .= "//<![CDATA[\n";
            $js .= "function select_arrow(txt) {\n";
            $js .= "    var obj = txt.parentNode.previousSibling;\n";
            $js .= "    while (obj) {\n";
            $js .= "        if (obj.tagName=='INPUT') {\n";
            $js .= "            obj.value = txt.innerHTML;\n";
            $js .= "            obj = null;\n";
            $js .= "        } else {\n";
            $js .= "            obj = obj.previousSibling;\n";
            $js .= "        }\n";
            $js .= "    }\n";
            $js .= "    return false;\n";
            $js .= "}\n";
            $js .= "function select_all_courses(truefalse) {\n";
            $js .= "    var obj = document.getElementById('menumycourses');\n";
            $js .= "    if (obj) {\n";
            $js .= "        var i_max = obj.options.length;\n";
            $js .= "        for (var i=0; i<i_max; i++) {\n";
            $js .= "            obj.options[i].selected = truefalse;\n";
            $js .= "        }\n";
            $js .= "    }\n";
            $js .= "    return false;\n";
            $js .= "}\n";
            $js .= "//]]>\n";
            $js .= "</script>\n";
            $elements[] = $mform->createElement('static', '', '', $js);
        }

        $params = array('size' => 2);
        $elements[] = $mform->createElement('text', $config_name, '', $params);
        $elements[] = $mform->createElement('static', '', '', $static);

        $mform->addGroup($elements, $elements_name, get_string($name, $plugin), ' ', false);
        $mform->setType($config_name, PARAM_TEXT);
    }

    /**
     * add_field_gradelinecolor
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_field_gradelinecolor($mform, $plugin, $options) {
        $name = 'gradelinecolor';
        $config_name = 'config_'.$name;
        $elements_name = 'elements_'.$name;
        $label = get_string($name, $plugin);

        switch (substr(current_language(), 0, 2)) {
            case 'ja':
                $help = 'https://standards.mitsue.co.jp/resources/w3c/TR/css3-color';
                break;
            case 'ko':
                $help = 'https://techhtml.github.io/css3-color';
                break;
            default:
                $help = 'https://www.w3.org/TR/css-color-3';
        }
        $params = array('href' => "$help/#svg-color", 'target' => '_blank');
        $help = html_writer::tag('a', get_string('help'), $params);
        $help = html_writer::tag('small', $help);

        $elements = array(
            $mform->createElement('text', $config_name, $label, $options),
            $mform->createElement('static', '', '', $help)
        );

        $mform->addGroup($elements, $elements_name, $label, ' ', false);
        $mform->setType($config_name, PARAM_ALPHANUM);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($elements_name, $name, $plugin);
    }

    /**
     * add_field_gradelinestyle
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_field_gradelinestyle($mform, $plugin) {
        $name = 'gradelinestyle';
        $config_name = 'config_'.$name;

        $strman = get_string_manager();
        if ($strman->string_exists('none', 'atto_table')) {
            // Moodle >= 3.x
            $options = array(
                '' => get_string('none', 'atto_table'),
                'dotted' => get_string('dotted', 'atto_table'),
                'dashed' => get_string('dashed', 'atto_table'),
                'solid'  => get_string('solid', 'atto_table'),
            );
        } else {
            // Moodle <= 2.9
            $options = array(
                '' => get_string('none'),
                'dotted' => '...',
                'dashed' => '_ _ _',
                'solid'  => '_____',
            );
        }
        $mform->addElement('select', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_ALPHANUM);
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);
    }

    /**
     * add_field_gradelinewidth
     *
     * @param object  $mform
     * @param string  $plugin
     * @return void, but will update $mform
     */
    protected function add_field_gradelinewidth($mform, $plugin, $options) {
        $name = 'gradelinewidth';
        $config_name = 'config_'.$name;
        $mform->addElement('text', $config_name, get_string($name, $plugin), $options);
        $mform->setType($config_name, PARAM_TEXT); // PARAM_ALPHANUM
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->addHelpButton($config_name, $name, $plugin);
    }

    /**
     * add_field_showactivitygrades
     *
     * @param object  $mform
     * @param string  $plugin
     * @param array   $types
     * @return void, but will update $mform
     */
    protected function add_field_showactivitygrades($mform, $plugin, $types) {
        $name = 'showactivitygrades';
        $config_name = 'config_'.$name;
        $elements_name = 'elements_'.$name;
        $label = get_string($name, $plugin);

        $elements = array();

        // "all" or "specific" activity types
        $options = array('' => get_string('none'),
                         'all' => get_string('allgradeableactivities', $plugin),
                         'specific' => get_string('specificactivitytypes', $plugin));
        $elements[] = $mform->createElement('select', $config_name, '', $options);

        // multi-select list of activity types
        $params = array('multiple' => 'multiple', 'size' => min(5, count($types)));
        $elements[] = $mform->createElement('select', $config_name.'types', '', $types, $params);

        // add group of elements
        $mform->addElement('group', $elements_name, $label, $elements, ' ', false);
        $mform->addHelpButton($elements_name, $name, $plugin);

        // set elements types
        $mform->setType($config_name, PARAM_ALPHA);
        $mform->setType($config_name.'types', PARAM_ALPHANUM);

        // set elements defaults
        $mform->setDefault($config_name, $this->defaultvalue($name));
        $mform->setDefault($config_name.'types', $this->defaultvalue($name.'types'));

        // specify conditional access to list of activity types
        $mform->disabledIf($config_name.'types[]', $config_name, 'ne', 'specific');
    }

    /**
     * get_graded_activity_types
     *
     * @return mixed, either an array() of activity types that have at least one graded instance in this course, or FALSE
     */
    protected function get_graded_activity_types() {
        global $COURSE, $DB;

        $select = 'm.name, COUNT(*) AS countinstances';
        $from   = '{modules} m '.
                  'RIGHT JOIN {course_modules} cm ON m.id = cm.module '.
                  'LEFT JOIN {grade_items} gi ON gi.itemtype = :itemtype '.
                                            'AND gi.itemmodule = m.name '.
                                            'AND gi.iteminstance = cm.instance';
        $where  = 'cm.course = :courseid AND gi.id IS NOT NULL';
        $group  = 'm.name';
        $order  = 'm.name';
        $params = array('itemtype' => 'mod',
                        'courseid' => $COURSE->id);
        if ($types = $DB->get_records_sql_menu("SELECT $select FROM $from WHERE $where GROUP BY $group ORDER BY $order", $params)) {
            $strman = get_string_manager();
            foreach ($types as $name => $count) {
                $mod_name = "mod_$name";
                if ($strman->string_exists('pluginname', $mod_name)) {
                    $mod_name = get_string('pluginname', $mod_name);
                }
                $types[$name] = "$mod_name ($count)";
            }
        }
        return $types;
    }

    /**
     * get_mycourses
     *
     * @return mixed, either an array() of contextids of accessible courses with a similar block, or FALSE
     */
    protected function get_mycourses() {
        global $COURSE, $DB;

        $mycourses = array();

        $select = 'bi.id, c.id AS courseid, c.shortname, ctx.id AS contextid';
        $from   = '{block_instances} bi '.
                  'JOIN {context} ctx ON bi.parentcontextid = ctx.id '.
                  'JOIN {course} c ON ctx.instanceid = c.id';
        $where  = 'bi.blockname = ? AND bi.pagetypepattern = ? AND ctx.contextlevel = ? AND c.id <> ? AND c.id <> ?';
        $order  = 'c.sortorder ASC';
        $params = array('taskchain_navigation', 'course-view-*', CONTEXT_COURSE, SITEID, $COURSE->id);

        if ($instances = $DB->get_records_sql("SELECT $select FROM $from WHERE $where ORDER BY $order", $params)) {
            $capability = 'block/taskchain_navigation:addinstance';
            if (class_exists('context_course')) {
                $context = context_course::instance(SITEID);
            } else {
                $context = get_context_instance(COURSE_CONTEXT, SITEID);
            }
            $has_site_capability = has_capability($capability, $context);
            foreach ($instances as $instance) {
                if ($has_site_capability) {
                    $has_course_capability = true;
                } else {
                    if (class_exists('context')) {
                        $context = context::instance_by_id($instance->contextid);
                    } else {
                        $context = get_context_instance_by_id($instance->contextid);
                    }
                    $has_course_capability = has_capability($capability, $context);
                }
                if ($has_course_capability) {
                    $mycourses[$instance->contextid] = $instance->shortname;
                }
            }
        }

        if (empty($mycourses)) {
            return false;
        } else {
            return $mycourses;
        }
    }

    protected function create_linebreak($mform) {
        global $CFG;

        static $bootstrap =  null;
        if ($bootstrap === null) {
            if (file_exists($CFG->dirroot.'/theme/bootstrapbase')) {
                $bootstrap = 3; // Moodle >= 2.5 (until 3.6)
            } else if (file_exists($CFG->dirroot.'/theme/boost')) {
                $bootstrap = 4; // Moodle >= 3.2
            } else {
                $bootstrap = 0;
            }
        }

        if ($bootstrap) {
            // Most modern themes use flex layout, so the only way to force a newline
            // is to insert a DIV that is fullwidth and minimal height.
            $params = array('style' => 'width: 100%; height: 4px;');
            $linebreak = html_writer::tag('div', '', $params);
        } else {
            $linebreak = html_writer::empty_tag('br');
        }
        return $mform->createElement('static', '', '', $linebreak);
    }
}