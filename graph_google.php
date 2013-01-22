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
 * This file is used to make the graph using the Google API
 *
 * @package    block
 * @subpackage graph_stats
 * @copyright  2011 Éric Bugnet with help of Jean Fruitet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function graph_google($course_id,$title) {
    global $CFG,$DB;
    // Get parameters

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
    
    // define type
    if ($style == 'area') {
        $type1 = 'area';
        $type2 = 'area';
    } else {
        $type1 = 'bars';
        $type2 = 'line';       
    }

    $days = array();
    $day = array();
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
		    $day[$a] = substr(userdate(mktime(0, 0, 0, date("m") , date("d") - $i, date("Y"))),0,-7);
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
		    $day[$a] = substr(userdate(mktime(0, 0, 0, date("m") , date("d") - $i, date("Y"))),0,-7);
		    $a = $a+1;
	    }
    }
    

    
    $graph = '
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
        google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn("string", "Day");
        data.addColumn("number", "'.get_string('visitors','block_graph_stats').'"); ';
        if ($course_id<=1) { $graph .= 'data.addColumn("number", "'.get_string('uniquevisitors','block_graph_stats').'");'; }
        
        $graph .= '
        data.addRows([ ';
            $a = 0;
            for ($i=$daysnb;$i>-1;$i--) {
                if ($course_id>1) {
                    $graph .= '["'.$day[$a].'",'.$logs_multi[$a].'],';
                } else {
                    $graph .= '["'.$day[$a].'",'.$logs_multi[$a].','.$logs[$a].'],';
                }    
                $a++;            
            }
        $graph .= '    ]);
        var options = {
        width: '.$graphwidth.',
        height: '.$graphwidth.',
        legend: {position: "none"},
        hAxis: {textPosition: "none"},
        series: {0:{color: "'.$color1.'", type: "'.$type1.'"},1:{color: "'.$color2.'", type: "'.$type2.'"}}
        };

        var chart = new google.visualization.AreaChart(document.getElementById("chart_div"));
        chart.draw(data, options);
        }
        </script>
        <div id="chart_div"></div>';
    
    return $graph;
}

?>
