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
 * This file is used to make the block in site or course
 *
 * @package    block
 * @subpackage graph_stats
 * @copyright  2011 Éric Bugnet with help of Jean Fruitet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
/**
 * Main block class for graph_stats block
 *
 * @copyright 2011 Éric Bugnet with help of Jean Fruitet
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_graph_stats extends block_base {
	
	/**
	* Standard block API function for initializing block instance
	* @return void
	*/
	public function init() {
		$this->title = get_string('blockname', 'block_graph_stats');
	}
	
    public function instance_allow_config() {
        return true;
    }
    
    public function instance_allow_multiple() {
        return false;
    }
    
    public function applicable_formats() {
        return array(
            'site' => true,
            'course-view' => true);
    }

    public function get_content() {
        global $CFG,$COURSE,$USER,$DB;
        
		if ($this->content !== null) {
            return $this->content;
        }
             
        // Get parameters
        
        /**
         * number of day for the graph
         * @var int 
         */
        $daysnb = 30;
        $daysnb = $CFG->daysnb;
          
        /**
         * engine used for make the graph
         * @var string 
         */
        $engine = 'moodle';
        $engine = $CFG->engine;
     
        $this->content         =  new stdClass;
        $this->content->text   = '';
        $this->content->footer = '';
        
        // Print the graph
        
        if ($engine == 'google') {
            // Graph from Google API
            include 'graph_google.php';    
            $this->content->text .= graph_google($COURSE->id, get_string('graphtitle','block_graph_stats',$daysnb));
        } else {
            // Graph from Moodle
            $this->content->text .= '<center><img src="'.$CFG->wwwroot.'/blocks/graph_stats/graph.php?course_id='.$COURSE->id.'" title="'.get_string('graphtitle','block_graph_stats',$daysnb).'" /></center>';        
        }
        
        // Add a link to course report for today
        if (has_capability('coursereport/log:view', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {
            $this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/graph_stats/details.php?course_id='.$COURSE->id.'" alt="'.get_string('moredetails','block_graph_stats').'" target="_blank">';
            $this->content->text .= get_string('moredetails','block_graph_stats').'</a>';
        }
		
		// Add some details in the footer
		if ($COURSE->id>1) { 
			// In a course
            $params = array('time' => mktime(0, 0, 0, date("m") , date("d"), date("Y")), 'course' => $COURSE->id);
            $sql = "SELECT COUNT(DISTINCT(userid)) as countid FROM {log} WHERE time > :time AND action = 'view' AND course = :course  ";
			$connections = $DB->get_record_sql($sql , $params);
			$this->content->footer .= get_string('connectedtoday','block_graph_stats').$connections->countid;
		} else {
		    // In the front page
            $params = array('time' => mktime(0, 0, 0, date("m") , date("d"), date("Y")));
            $sql = "SELECT COUNT(userid) as countid FROM {log} WHERE time > :time AND action = 'login' ";
			$connections = $DB->get_record_sql($sql, $params);
			$this->content->footer .= get_string('connectedtoday','block_graph_stats').$connections->countid;
			$users = $DB->get_records('user', array('deleted' => 0, 'confirmed' => 1));
			$courses = $DB->get_records('course', array('visible' => 1));
			$this->content->footer .= '<br />'.get_string('membersnb','block_graph_stats').count($users);
			$this->content->footer .= '<br />'.get_string('coursesnb','block_graph_stats').count($courses);
		}
        
        return $this->content;
    }
}
