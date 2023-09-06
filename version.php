<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


defined('MOODLE_INTERNAL') || die();


$plugin->version = 2023090343; //año-mes-día-numeroVersion
$plugin->component = 'local_uplannerconnect';

$plugin->requires = 2015030901; 
$plugin->maturity = MATURITY_ALPHA;

// $tasks = array(
//     array(
//         'classname' => 'local_uplannerconnect\task\InsertRecordsCourseTask',
//         'blocking' => 0,
//         'minute' => '0',
//         'hour' => '0',
//         'day' => '*',
//         'dayofweek' => '*',
//         'month' => '*',
//     ),
// );


$plugin->release = '1.0.0';
$plugin->release = '1.0 (Build: 2023090300)';
$plugin->author = 'Samuel Ramirez & Cristian Machado Mosquera & Daniel dorado';
$plugin->authorcontact = 'samuel.ramirez@correounivalle.edu.co & cristian.machado@correounivalle.edu.co & doradodaniel14@gmail.com';


$plugin->license = 'GNU GPL v3 or later';
$plugin->description = 'Este plugin se utiliza para enviar información a uPlanner.';