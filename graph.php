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
 * This file is used to make the graph
 *
 * @package    block
 * @subpackage graph_stats
 * @copyright  2011 Éric Bugnet with help of Jean Fruitet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


include '../../config.php';
defined('MOODLE_INTERNAL') || die;
include $CFG->dirroot.'/lib/graphlib.php';
global $CFG,$DB;

// Get parameters

/**
 * course id to show in graph
 * @var integer 
 */
$course_id = optional_param('course_id', 1, PARAM_INT);
// $course_id = optional_param('course_id', -1, PARAM_INT);

/**
 * number of day for the graph
 * @var integer 
 */
$daysnb = $CFG->block_graph_stats_daysnb;

/**
 * width of the the graph
 * @var integer 
 */
$graphwidth = $CFG->block_graph_stats_graphwidth;

/**
 * height of the the graph
 * @var integer 
 */
$graphheight = $CFG->block_graph_stats_graphheight;

/**
 * does I have to print multiconnexion in the front page ?
 * @var boolean
 */
$multi = $CFG->block_graph_stats_multi;

/**
 * color of outer background
 * @var string 
 */
$color_outer_background = $CFG->block_graph_stats_outer_background;

/**
 * color of inner background
 * @var string 
 */
$color_inner_background = $CFG->block_graph_stats_inner_background;

/**
 * color of inner border
 * @var string 
 */
$color_inner_border = $CFG->block_graph_stats_inner_border;

/**
 * color of axis
 * @var string 
 */
$color_axis_colour = $CFG->block_graph_stats_axis_colour;

/**
 * color of first graph
 * @var string 
 */
$color1 = $CFG->block_graph_stats_color1;

/**
 * color of second graph
 * @var string 
 */
$color2 = $CFG->block_graph_stats_color2;

/**
 * style of the graph
 * @var string 
 */
$style = $CFG->block_graph_stats_style;

$days = array();
$logs = array();
$logs_multi = array();

// Let's get the datas
$a=0;

if ($course_id>1) {
	for ($i=$daysnb;$i>-1;$i--) { // Days count
        $params=array(
            'time1' => mktime(0, 0, 0, date("m") , date("d") - $i, date("Y")),
            'time2' => mktime(0, 0, 0, date("m") , date("d") - ($i-1), date("Y")),
            'courseid' => $course_id );
        $sql="SELECT COUNT(DISTINCT(userid)) as countid FROM {log} WHERE time > :time1 AND time < :time2 AND action = 'view' AND course = :courseid ";
		$countgraph_multi = $DB->get_record_sql($sql, $params);
        $days[$a] = '';
		$logs_multi[$a] = $countgraph_multi->countid;
		$a = $a+1;
	}
} else {
	For ($i=$daysnb;$i>-1;$i--) { // Days count
        $params=array(
            'time1' => mktime(0, 0, 0, date("m") , date("d") - $i, date("Y")),
            'time2' => mktime(0, 0, 0, date("m") , date("d") - ($i-1), date("Y")),
            'courseid' => $course_id );
        $sql= "SELECT COUNT(DISTINCT(userid)) as countid FROM {log} WHERE time > :time1 AND time < :time2 AND action = 'login' ";
		$countgraph = $DB->get_record_sql($sql, $params);
        $days[$a] = '';
		$logs[$a] = $countgraph->countid;
		if ($multi==1) {
            $params=array(
                'time1' => mktime(0, 0, 0, date("m") , date("d") - $i, date("Y")),
                'time2' => mktime(0, 0, 0, date("m") , date("d") - ($i-1), date("Y")),
                'courseid' => $course_id );
            $sql= "SELECT COUNT(userid) as countid FROM {log} WHERE time > :time1 AND time < :time2 AND action = 'login' ";
			$countgraph_multi = $DB->get_record_sql($sql, $params);
			$logs_multi[$a] = $countgraph_multi->countid;
		}
		$a = $a+1;
	}
}

// Draw it now
$graph = new graph($graphwidth, $graphheight);
$graph->parameter['title'] 			    = false;
$graph->x_data           				= $days;
$graph->y_data['logs']   				= $logs;
$graph->y_data['logs_multi']   			= $logs_multi;
if ($course_id>1) { 
	$graph->y_order 				    = array('logs_multi');
} else {
	$graph->y_order 				    = array('logs_multi','logs');
}
if ($style == 'area') {
    $graph->y_format['logs_multi'] 		    = array('colour' => $color1, 'area' => 'fill');
    $graph->y_format['logs'] 				= array('colour' => $color2, 'area' => 'fill');
} else {
    $graph->y_format['logs_multi'] 		    = array('colour' => $color1, 'bar' => 'fill','bar_size' => 0.6);
    $graph->y_format['logs'] 				= array('colour' => $color2, 'line' => 'line');
}
$graph->parameter['bar_spacing'] 		= 0;
$graph->parameter['y_label_left']   	= '';
$graph->parameter['label_size']		    = '1';
$graph->parameter['x_axis_angle']		= 90;
$graph->parameter['x_label_angle']  	= 0;
$graph->parameter['tick_length'] 		= 0;
$graph->parameter['outer_background'] 	= $color_outer_background;
$graph->parameter['inner_background'] 	= $color_inner_background;
$graph->parameter['inner_border'] 		= $color_inner_border;
$graph->parameter['axis_colour'] 		= $color_axis_colour;
$graph->parameter['shadow']         	= 'none'; 
error_reporting(5); // ignore most warnings such as font problems etc
$graph->draw_stack();

