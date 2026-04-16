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
 * Lifecycle trigger: archive courses based on inactivity and age.
 *
 * @package    tool_lccoursearchive
 * @copyright  2025 Gifty Wanzola (UCL)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lccoursearchive\lifecycle;

global $CFG;
require_once($CFG->dirroot . '/admin/tool/lifecycle/trigger/lib.php');

use tool_lifecycle\local\manager\settings_manager;
use tool_lifecycle\local\response\trigger_response;
use tool_lifecycle\settings_type;
use tool_lifecycle\trigger\instance_setting;

defined('MOODLE_INTERNAL') || die();

/**
 * Trigger class for archiving inactive courses.
 *
 * Targets courses where:
 *  - Last access (enrolled users) is older than lastaccessdelay (default 12 months)
 *    OR the course has never been accessed
 *  - Course creation is older than creationdelay (default 24 months)
 *  - Course context is not already locked (not already archived)
 */
class trigger extends \tool_lifecycle\trigger\base_automatic {

    /**
     * Every decision is already in the WHERE statement.
     * @param \stdClass $course
     * @param int $triggerid
     * @return trigger_response
     */
    public function check_course($course, $triggerid) {
        return trigger_response::trigger();
    }

    /**
     * @return string full component name
     */
    public function get_subpluginname() {
        return 'tool_lccoursearchive';
    }

    /**
     * @return string human-readable plugin name
     */
    public function get_plugin_name() {
        return get_string('pluginname', 'tool_lccoursearchive');
    }

    /**
     * @return string human-readable plugin description
     */
    public function get_plugin_description() {
        return get_string('plugindescription', 'tool_lccoursearchive');
    }

    /**
     * @return instance_setting[]
     */
    public function instance_settings() {
        return [
            new instance_setting('lastaccessdelay', PARAM_INT),
            new instance_setting('creationdelay', PARAM_INT),
        ];
    }

    /**
     * Returns WHERE clause and params for courses to be archived.
     *
     * @param int $triggerid
     * @return array [$where, $params]
     */
    public function get_course_recordset_where($triggerid) {
        $settings = settings_manager::get_settings($triggerid, settings_type::TRIGGER);

        $lastaccessdelay = isset($settings['lastaccessdelay']) ? $settings['lastaccessdelay'] : DAYSECS * 365;
        $creationdelay   = isset($settings['creationdelay'])   ? $settings['creationdelay']   : DAYSECS * 365 * 2;

        $now = time();
        $lastaccessthreshold = $now - $lastaccessdelay;
        $creationthreshold   = $now - $creationdelay;

        $where = 'c.id <> 1
                  AND c.timecreated < :creationthreshold
                  AND EXISTS (
                        SELECT 1
                          FROM {context} ctx
                         WHERE ctx.contextlevel = 50
                           AND ctx.instanceid = c.id
                           AND ctx.locked = 0
                  )
                  AND (
                        NOT EXISTS (
                            SELECT 1
                              FROM {user_lastaccess} la
                             WHERE la.courseid = c.id
                        )
                        OR
                        EXISTS (
                            SELECT 1
                              FROM {user_lastaccess} la
                             WHERE la.courseid = c.id
                             GROUP BY la.courseid
                            HAVING MAX(la.timeaccess) < :lastaccessthreshold
                        )
                  )';

        $params = [
            'creationthreshold'   => $creationthreshold,
            'lastaccessthreshold' => $lastaccessthreshold,
        ];

        return [$where, $params];
    }

    /**
     * @param \MoodleQuickForm $mform
     */
    public function extend_add_instance_form_definition($mform) {
        $elementname = 'lastaccessdelay';
        $mform->addElement('duration', $elementname,
            get_string($elementname, 'tool_lccoursearchive'));
        $mform->addHelpButton($elementname, $elementname, 'tool_lccoursearchive');
        $mform->setDefault($elementname, DAYSECS * 365);

        $elementname = 'creationdelay';
        $mform->addElement('duration', $elementname,
            get_string($elementname, 'tool_lccoursearchive'));
        $mform->addHelpButton($elementname, $elementname, 'tool_lccoursearchive');
        $mform->setDefault($elementname, DAYSECS * 365 * 2);
    }

    /**
     * @param \MoodleQuickForm $mform
     * @param array $settings
     */
    public function extend_add_instance_form_definition_after_data($mform, $settings) {
        if (is_array($settings)) {
            if (array_key_exists('lastaccessdelay', $settings)) {
                $mform->setDefault('lastaccessdelay', $settings['lastaccessdelay']);
            }
            if (array_key_exists('creationdelay', $settings)) {
                $mform->setDefault('creationdelay', $settings['creationdelay']);
            }
        }
    }
}
