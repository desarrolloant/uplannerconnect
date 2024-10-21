<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\plugin_config;

/**
 *  Estruture types
 */
class estruture_types
{
    // Estruturas de datos uPlanner.
    const UPLANNER_GRADES = [
        ['name' => 'sectionId', 'type' => 'string'],
        ['name' => 'studentCode', 'type' => 'string'],
        ['name' => 'finalGrade', 'type' => 'numeric'],
        ['name' => 'finalGradeMessage', 'type' => 'string'],
        ['name' => 'finalGradeMessage', 'type' => 'string'],
        ['name' => 'finalGradePercentage', 'type' => 'numeric'],
        ['name' => 'evaluationGroupCode', 'type' => 'string'],
        ['name' => 'grades', 'type' => 'numeric'],
        ['name' => 'evaluationId', 'type' => 'numeric'],
        ['name' => 'value', 'type' => 'string'],
        ['name' => 'evaluationName', 'type' => 'string'],
        ['name' => 'date', 'type' => 'string'],
        ['name' => 'isApproved', 'type' => 'bool'],
        ['name' => 'average', 'type' => 'string'],
        ['name' => 'lastModifiedDate', 'type' => 'string'],
        ['name' => 'action', 'type' => 'string'],
        ['name' => 'transactionId', 'type' => 'numeric']
    ];

    // Estruturas de datos uPlanner.
    const UPLANNER_EVALUATION_ESTRUTURE = [
        ['name' => 'sectionId', 'type' => 'string'],
        ['name' => 'evaluationGroupCode', 'type' => 'string'],
        ['name' => 'evaluationGroupName', 'type' => 'string'],
        ['name' => 'evaluationId', 'type' => 'numeric'],
        ['name' => 'evaluationName', 'type' => 'string'],
        ['name' => 'weight', 'type' => 'numeric'],
        ['name' => 'action', 'type' => 'string'],
        ['name' => 'date', 'type' => 'string'],
        ['name' => 'transactionId', 'type' => 'numeric'],
        ['name' => 'courseid', 'type' => 'numeric'],
    ];

    const UPLANNER_MATERIALS_ESTRUTURE = [
        ['name' => 'id', 'type' => 'string'],
        ['name' => 'name', 'type' => 'string'],
        ['name' => 'type', 'type' => 'string'],
        ['name' => 'url', 'type' => 'string'],
        ['name' => 'fileName', 'type' => 'string'],
        ['name' => 'fileExtension', 'type' => 'string'],
        ['name' => 'parentId', 'type' => 'string'],
        ['name' => 'blackboardSectionId', 'type' => 'string'],
        ['name' => 'size', 'type' => 'numeric'],
        ['name' => 'lastUpdatedTime', 'type' => 'string'],
        ['name' => 'action', 'type' => 'string'],
        ['name' => 'transactionId', 'type' => 'numeric'],
    ];

    const UPLANNER_ANNOUNCEMENTS_ESTRUTURE = [
        ['name' => 'blackboardSectionId', 'type' => 'string'],
        ['name' => 'createdDate', 'type' => 'string'],
        ['name' => 'type', 'type' => 'string'],
        ['name' => 'createdTime', 'type' => 'string'],
        ['name' => 'title', 'type' => 'string'],
        ['name' => 'content', 'type' => 'string'],
        ['name' => 'id', 'type' => 'string'],
        ['name' => 'usernameCreator', 'type' => 'string'],
        ['name' => 'action', 'type' => 'string'],
        ['name' => 'transactionId', 'type' => 'numeric'],
    ];

    // Estructuras de datos para la creacion.
    // De un Evento.
    const CREATE_EVENT_DATA = [
        ['name' => 'dataEvent' , 'type' => 'object'],
        ['name' => 'typeEvent' , 'type' => 'string'],
        ['name' => 'dispatch' , 'type' => 'string'],
        ['name' => 'enum_etities' , 'type' => 'string'],
    ];
}