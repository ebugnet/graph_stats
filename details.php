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
 * This file print users connected today
 *
 * @package    block
 * @subpackage graph_stats
 * @copyright  2011 Éric Bugnet with help of Jean Fruitet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
global $CFG, $USER, $SESSION, $COURSE, $DB;

/**
 * Today's date
 * @var timestamp 
 */
$today = mktime(0, 0, 0, date("m") , date("d") , date("Y"));
        
$url = new moodle_url('/block/graph_stats/details.php');

/**
 * course id to show in graph
 * @var integer 
 */
$course_id = optional_param('course_id', 1, PARAM_INT);
require_course_login($course_id);

$context = get_context_instance(CONTEXT_COURSE, $course_id);
$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('connectedtoday', 'block_graph_stats'));
$PAGE->set_heading($COURSE->fullname);

echo $OUTPUT->header();

/*
// Moodle 2.0
if (has_capability('coursereport/log:view', $context)) {
    echo '<h2 class="main">'.get_string('connectedtoday','block_graph_stats').'</h2>';
    echo '<a href="'.$CFG->wwwroot.'/course/report/log/index.php?chooselog=1&showusers=1&showcourses=1&host_course=1%2F'.$course_id.'&user=&date='.$today.'&modid=&modaction=&logformat=showashtml" alt="'.get_string('moredetails','block_graph_stats').'">'.get_string('moredetails', 'block_graph_stats').'</a>';
    if (!empty($SESSION->fullnamedisplay)) {
        $CFG->fullnamedisplay = $SESSION->fullnamedisplay;
    }
*/
// Moodle 2.3
if (has_capability('report/log:view', $context)) {
    echo '<h2 class="main">'.get_string('connectedtoday','block_graph_stats').'</h2>';
    echo '<a href="'.$CFG->wwwroot.'/report/log/index.php?chooselog=1&showusers=1&showcourses=1&host_course=1%2F'.$course_id.'&user=&date='.$today.'&modid=&modaction=&logformat=showashtml" alt="'.get_string('moredetails','block_graph_stats').'">'.get_string('moredetails', 'block_graph_stats').'</a>';
    if (!empty($SESSION->fullnamedisplay)) {
        $CFG->fullnamedisplay = $SESSION->fullnamedisplay;
    }

	echo "<ul>";
	$params=array(
	    'time1' => mktime(0, 0, 0, date('m') , date('d'), date('Y')),
        'time2' => mktime(23, 59, 59, date('m') , date('d'), date('Y')) );
    $query = "
		SELECT DISTINCT
			l.userid, u.firstname, u.lastname
		FROM
			{log} l, {user} u
		WHERE
			l.userid = u.id AND
			time > :time1 AND
			time < :time2 AND
			action = 'login'
	";

	 if ($CFG->fullnamedisplay == 'lastname firstname') {
		$query = $query."
			ORDER BY 
				u.lastname, u.firstname ASC 
		";
	 } else {
		$query = $query."
			 ORDER BY 
				u.firstname, u.lastname  ASC 
		";
	 }	 

	 if ($connections = $DB->get_records_sql($query, $params)) {
     	 foreach ($connections as $connection) {
			 if ($CFG->fullnamedisplay == 'firstname lastname') {
				 $fullname = $connection->firstname.' '. $connection->lastname;
			 } else if ($CFG->fullnamedisplay == 'lastname firstname') {
				 $fullname = $connection->lastname.' '. $connection->firstname;
            } else if ($CFG->fullnamedisplay == 'firstname') {
				 $fullname = $connection->firstname;
             } else {
				 $fullname = $connection->lastname.' '. $connection->firstname;
             }
			 echo '<li>'.$fullname.'</li>';
		}

	 }
	echo "</ul>";
}

echo $OUTPUT->footer();

