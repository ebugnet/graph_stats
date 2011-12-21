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
 * This file is used to setting the block allover the site
 *
 * @package    block
 * @subpackage graph_stats
 * @copyright  2011 Éric Bugnet with help of Jean Fruitet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 
defined('MOODLE_INTERNAL') || die;

// Size and print settings
       
$settings->add(new admin_setting_configtext(
            'daysnb',
            get_string('daysnb', 'block_graph_stats'),
            get_string('daysnb_help', 'block_graph_stats'),
            '30',
            PARAM_INT
        ));
        
$settings->add(new admin_setting_configtext(
            'graphwidth',
            get_string('graphwidth', 'block_graph_stats'),
            get_string('graphwidth_help', 'block_graph_stats'),
            '170',
            PARAM_INT
        ));
 
$settings->add(new admin_setting_configtext(
            'graphheight',
            get_string('graphheight', 'block_graph_stats'),
            get_string('graphheight_help', 'block_graph_stats'),
            '150',
            PARAM_INT
        ));

$engine = array(	
    'moodle'=>'Moodle',
    'google'=>'Google'
);

$settings->add(new admin_setting_configselect(
            'engine',
            get_string('engine', 'block_graph_stats'),
            get_string('engine_help', 'block_graph_stats'),
            'moodle',
            $engine
        ));   
          
$style = array(	
    'area'=>get_string('area', 'block_graph_stats'),
    'classic'=>get_string('classic', 'block_graph_stats')
);

$settings->add(new admin_setting_configselect(
            'style',
            get_string('style', 'block_graph_stats'),
            get_string('style_help', 'block_graph_stats'),
            'classic',
            $style
        ));        
        
                      
$settings->add(new admin_setting_configcheckbox(
            'multi',
            get_string('multi', 'block_graph_stats'),
            get_string('multi_help', 'block_graph_stats'),
            '1'
        ));

        
// Color settings 

$colors = array(	
    'aqua'=>get_string('aqua', 'block_graph_stats'),
    'black'=>get_string('black', 'block_graph_stats'),
    'blue'=>get_string('blue', 'block_graph_stats'),
    'fuchsia'=>get_string('fuchsia', 'block_graph_stats'),
    'gray'=>get_string('gray', 'block_graph_stats'),
    'green'=>get_string('green', 'block_graph_stats'),
    'lime'=>get_string('lime', 'block_graph_stats'),
    'maroon'=>get_string('maroon', 'block_graph_stats'),
    'navy'=>get_string('navy', 'block_graph_stats'),
    'olive'=>get_string('olive', 'block_graph_stats'),
    'orange'=>get_string('orange', 'block_graph_stats'),
    'purple'=>get_string('purple', 'block_graph_stats'),
    'red'=>get_string('red', 'block_graph_stats'),
    'white'=>get_string('white', 'block_graph_stats'),
    'yellow'=>get_string('yellow', 'block_graph_stats')
    );


$settings->add(new admin_setting_configselect(
            'outer_background',
            get_string('outer_background', 'block_graph_stats'),
            get_string('outer_background_help', 'block_graph_stats'),
            'white',
            $colors
        ));

$settings->add(new admin_setting_configselect(
            'inner_background',
            get_string('inner_background', 'block_graph_stats'),
            get_string('inner_background_help', 'block_graph_stats'),
            'white',
            $colors
        ));

$settings->add(new admin_setting_configselect(
            'inner_border',
            get_string('inner_border', 'block_graph_stats'),
            get_string('inner_border_help', 'block_graph_stats'),
            'gray',
            $colors
        ));

$settings->add(new admin_setting_configselect(
            'axis_colour',
            get_string('axis_colour', 'block_graph_stats'),
            get_string('axis_colour_help', 'block_graph_stats'),
            'gray',
            $colors
        ));

$settings->add(new admin_setting_configselect(
            'color1',
            get_string('color1', 'block_graph_stats'),
            get_string('color1_help', 'block_graph_stats'),
            'blue',
            $colors
        ));

$settings->add(new admin_setting_configselect(
            'color2',
            get_string('color2', 'block_graph_stats'),
            get_string('color2_help', 'block_graph_stats'),
            'green',
            $colors
        ));
