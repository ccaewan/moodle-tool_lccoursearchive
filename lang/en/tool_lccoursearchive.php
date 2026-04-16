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
 * Language strings for tool_lccoursearchive.
 *
 * @package    tool_lccoursearchive
 * @copyright  2025 Gifty Wanzola (UCL)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname']        = 'Course archive trigger';
$string['plugindescription'] = 'Triggers archival of courses that have not been accessed for a configurable period and are older than a configurable age.';

$string['lastaccessdelay']         = 'Last access delay';
$string['lastaccessdelay_help']    = 'Courses whose most recent enrolled-user access is older than this value (or have never been accessed) will be considered for archival.';

$string['creationdelay']           = 'Course creation delay';
$string['creationdelay_help']      = 'Courses created more recently than this value will not be considered for archival, regardless of access history.';

$string['privacy:metadata'] = 'This plugin does not store any personal data.';
