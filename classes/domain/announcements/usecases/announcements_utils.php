<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\announcements\usecases;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\plugin_config\plugin_config;
use local_uplannerconnect\domain\service\transition_endpoint;
use local_uplannerconnect\domain\service\utils;
use moodle_exception;

/**
 *  Extraer los datos
 */
class announcements_utils
{   
    const TABLE_FORUM = 'forum_posts';
    const TABLE_USER = 'user';

    private $validator;
    private $moodle_query_handler;
    private $transition_endpoint;
    private $utils_service;

    /**
     *  Construct
     */
    public function __construct()
    {
        $this->validator = new data_validator();
        $this->moodle_query_handler = new moodle_query_handler();
        $this->transition_endpoint = new transition_endpoint();
        $this->utils_service = new utils();
    }

    /**
     * Return data user_graded
     *
     * @param array $data
     * @return array
     */
    public function createdAnnouncementResources(array $data) : array
    {
        $dataToSave = [];
        try {
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento user_graded');
                return $dataToSave;
            }

            // Get data.
            $event = $data['dataEvent'];
            $dataEvent = $event->get_data();
            $courseid = $event->courseid;
            $dataCourse = $this->getDataCourse($courseid);
            $dateCreated = $this->validator->isIsset($dataEvent['timecreated']);
            $idForum = $this->validator->isIsset($dataEvent['objectid']);
            $isMessage = $this->isMessageCreated($dataEvent);
            $createdDate = date('Y-m-d', $dateCreated);
            $createdTime = date('H:i', $dateCreated);
            $dataForum = $this->getDataForum([
                'forumid' => $idForum,
                'key' => $isMessage ? 'id' : 'discussion',
            ]);

            // Get data user.
            $idUserForum = $this->validator->isIsset($dataForum->userid);
            $nameUser = $this->utils_service->getUserNameTeacher($courseid);

            $dataToSave = [
                'blackboardSectionId' => $this->validator->isIsset($this->utils_service->convertFormatUplanner($dataCourse->shortname)),
                'id' => $this->validator->isIsset(strval($idForum)),
                'createdDate' => $this->validator->isIsset($createdDate),
                'createdTime' => $this->validator->isIsset($createdTime),
                'usernameCreator' => $this->validator->isIsset($nameUser),
                'title' => $this->validator->isIsset($dataForum->subject),
                'content' => $this->validator->isIsset($dataForum->message),
                'type' => 'html',
                'action' => strtoupper($data['dispatch']),
                'transactionId' => $this->validator->isIsset($this->transition_endpoint->getLastRowTransaction($courseid)),
            ];
        } catch (moodle_exception $e) {
            error_log('Exception capturada: '. $e->getMessage(). "\n");
        }
        return $dataToSave;
    }

    /**
     * Return data of in course
     * 
     * @param int $courseid
     * @return object
     */
    private function getDataCourse(int $courseid) : object
    {
        $data = new \stdClass();
        try {
            
            if (!empty($courseid)) {
                $response = ($this->validator->verifyQueryResult([                        
                    'data' => $this->moodle_query_handler->extract_data_db([
                        'table' => plugin_config::TABLE_COURSE,
                        'conditions' => [
                            'id' => $this->validator->isIsset($courseid)
                        ]
                    ])
                ]))['result'];

                if (is_object($response)) {
                    $data = $response;
                }
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $data;
    }

    /**
     * Return data of in forum
     */
    private function getDataForum(array $params) : object
    {
        $data = new \stdClass();
        $data->userid = "";
        $data->subject = "";
        $data->message = "";
        try {
            
            if (!empty($params)) {
                $forumid = $params['forumid'];
                $response = ($this->validator->verifyQueryResult([                        
                    'data' => $this->moodle_query_handler->extract_data_db([
                        'table' => self::TABLE_FORUM,
                        'conditions' => [
                            $params['key'] => $this->validator->isIsset($forumid)
                        ]
                    ])
                ]))['result'];
 
                if (is_object($response)) {
                    $data = $response;
                }
            }
        } catch (moodle_exception $e) {
            error_log('Exception capturada: '. $e->getMessage(). "\n");
        }
        return $data;
    }

    /**
     * Return if is created
     */
    private function isMessageCreated(array $data) : bool
    {
        $isCreated = false;
        try {
            if (!empty($data)) {
                $isCreated = key_exists('discussionid', $data['other']);
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $isCreated;
    }
}