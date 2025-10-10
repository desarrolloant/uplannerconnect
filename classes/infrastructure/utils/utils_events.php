<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado<cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\utils;

use local_uplannerconnect\application\service\announcements_utils;
use local_uplannerconnect\application\service\event_access_validator;
use local_uplannerconnect\application\service\filter_evaluation_update;
use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\domain\management_factory;
use moodle_exception;

/**
  * Class Utils Events
*/
class utils_events
{
    const FIELD_COURSE = 'courseid';
    const TEXT_ERROR = 'Exception caught: ';
    const QUERY_IS_JSON_REPEAT = "SELECT id FROM {uplanner_materials} WHERE json::jsonb->>'id' = :id AND json::jsonb->>'name' = :name AND json::jsonb->>'url' = :url ORDER BY id DESC LIMIT 1";

    /**
     * @param $event
     * @param $action
     * @return void
     */
    public static function isStructureCourse($event,$action): void
    {
        $get_grade_item = $event->get_grade_item();
        $dispatchStructure = new dispatch_structure();
        $dispatchStructure->executeEventHandler(
            [
                'courseid' => $get_grade_item->courseid,
                'itemid' => $get_grade_item->id,
                'action' => $action
            ],
            $event
        );
    }

    /**
     * Validate if the faculty has access
     *
     * @param $data
     * @return bool
     */
    public static function validateAccessFaculty($data) : bool
    {
        $response = false;
        try {
            $event_access_validator = new event_access_validator();
            $academic_period_checker = new academic_period_checker();
            $eventData = $data->get_data();

            if (array_key_exists(self::FIELD_COURSE, $eventData)) {
                $response = $event_access_validator->validateAccessByFaculty($eventData[self::FIELD_COURSE]);
                if ($response) {
                    $response = $academic_period_checker->validateAcademicPeriod($eventData[self::FIELD_COURSE]);
                }
            }
        }
        catch (moodle_exception $e) {
            error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
        }
        return $response;
    }

    /**
     * Filter recent update
     */
    public static function filterRecentUpdate($event)
    {
        try {
            $filter_evaluation_update = new filter_evaluation_update();
            $isFilter = $filter_evaluation_update->filterRecentUpdate($event);

            if ($isFilter) {
                self::isStructureCourse($event,'update');
            }
        } catch (moodle_exception $e) {
            error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
        }
    }

    /**
     *  Validate if the grade item is total
     */
    public static function isTotalItem($grade_item)
    {
        $filter_evaluation_update = new filter_evaluation_update();
        return $filter_evaluation_update->isTotalItem($grade_item);
    }

    /**
     * Instantiate the factory.
     *
     * @param array $data
     * @return void
     */
    public static function instanceFactory(array $data) : void
    {
        try {
            if (empty($data['dataEvent']) || empty($data['typeEvent']) ||
                empty($data['dispatch'])  || empty($data['enum_etities']))
            {
                error_log("Error in provided data: some fields are empty.");
                return;
            }

            $ManageEntity = new management_factory();

            if (method_exists($ManageEntity, 'create')) {
                // Llamar al método create
                $ManageEntity->create([
                    "dataEvent" => $data['dataEvent'],
                    "typeEvent" => $data['typeEvent'],
                    "dispatch" => $data['dispatch'],
                    "enum_etities" => $data['enum_etities']
                ]);
            } else {
                error_log("Error in provided data: some fields are empty.");
            }
        } catch (moodle_exception $e) {
            error_log(self::TEXT_ERROR . $e->getMessage() . "\n");
        }
    }

    /**
     * Instantiate the factory
     *
     * @param array $data
     * @return bool
     */
    public static function validateAccessTypeEvent(array $data) : bool
    {
        $response = false;
        try {
            $event_access_validator = new event_access_validator();
            $response = $event_access_validator->validateTypeEvent($data);
        }
        catch (moodle_exception $e) {
            error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
        }
        return $response;
    }

    /**
     * Validate if the user is teacher
     */
    public static function isRolTeacher($event)
    {
        $isRolTeacher = false;
        try {
            if (!empty($event)) {
                $dataEvent = $event->get_data();
                $userId = $dataEvent['userid'];
                $announcements_utils = new announcements_utils();
                $isRolTeacher = $announcements_utils->isRolTeacher($userId);
            }
        } catch (moodle_exception $e) {
            error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
        }
        return $isRolTeacher;
    }

    /**
     * Is Forum Post Parent
     */
    public static function isForumPostParent($idPost)
    {
        $isParent = false;
        try {
            if (!empty($idPost)) {
                $announcements_utils = new announcements_utils();
                $isParent = $announcements_utils->isParentFormPost($idPost);
            }
        } catch (moodle_exception $e) {
            error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
        }
        return $isParent;
    }

    /**
     *
     */
    public static function isGradeEvent(array $data , $event): void
    {
        try {
             $grade = $event->get_grade();
             if (isset($data['iduser'], $data['action'])) {
                 $dispatchGrade = new dispatch_grades();
                 $data['courseid'] = $grade->grade_item->courseid;
                 $data['iduser'] = $grade->userid;
                 $dispatchGrade->executeEventHandler($data,$event);
             }
        } catch (moodle_exception $e) {
             error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
        }
    }

    public static function isRepeatJsonResource(array $data): bool {
        $result = false;
        try {
            $_query = new moodle_query_handler();
            $isRepeatQury = $_query->executeQuery(
                self::QUERY_IS_JSON_REPEAT,
                [
                    'id'=> $data['id'],
                    'name' => $data['name'],
                    'url' => $data['url']
                ]
            );
            $result = !empty($isRepeatQury);
        } catch(moodle_exception $e) {
            error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
        }
        return $result;
    }
}