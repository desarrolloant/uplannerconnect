<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\materials\usecases;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\plugin_config\plugin_config;
use local_uplannerconnect\domain\service\transition_endpoint;
use local_uplannerconnect\domain\service\utils;
use moodle_exception;

/**
 *  Extraer los datos
 */
class material_utils
{
    const QUERY_FILE_MATERIAL = "SELECT * FROM mdl_files where contextid='%s' ORDER BY sortorder DESC LIMIT 1";
    const QUERY_URL_DATA = "SELECT *
        FROM mdl_course_modules mcm
        INNER JOIN mdl_url mu ON mcm.instance = mu.id
        WHERE mcm.id = '%s';";

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
     * Retorna los datos del evento user_graded
     *
     * @return array
     */
    public function resourceCreatedMaterial(array $data) : array
    {
        $dataToSave = [];
        try {
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento user_graded');
                return $dataToSave;
            }

            //Traer la información
            $event = $data['dataEvent'];
            $getData = $this->validator->isArrayData($event->get_data());
            // Sacar el id de la pagina
            $courseid = $event->courseid;

            $queryCourse = ($this->validator->verifyQueryResult([
                'data' => $this->moodle_query_handler->extract_data_db([
                    'table' => plugin_config::TABLE_COURSE,
                    'conditions' => [
                        'id' => $this->validator->isIsset($courseid)
                    ]
                ])
            ]))['result'];
            $fileData = $this->getDataResource($event->contextid);

            $sizeFile = 0;
            $typeFile = $getData['other']['modulename'];

            switch ($typeFile) {
                case 'url':
                    $typeFile = 'link';
                    break;
                case 'resource':
                    $typeFile = 'file';
                    break;
            }

            // Optional fields
            $url = "";
            $fileName = "";
            $fileExtension = "";

            if ($typeFile == 'link') {
                $objectId =  $getData['objectid'];
                $url = $this->getInstanceId(self::QUERY_URL_DATA, $objectId)->externalurl;
            }

            if ($typeFile == 'file') {
                $objectId =  $getData['objectid'];
                $fileName = $fileData->filename;
                $sizeFile = $fileData->filesize;

                if (isset($fileData->mimetype)) {
                    $mimeParts = explode('/', $fileData->mimetype);
                    $fileExtension = end($mimeParts);
                }
            }
            $moduleName = $getData['other']['name'] ?? '';

            $timestamp =  $this->validator->isIsset($getData['timecreated']);
            $formattedDateCreated = date('Y-m-d', $timestamp);

            //información a guardar
            $dataToSave = [
                'id' => $this->validator->isIsset(strval($getData['other']['instanceid'])),
                'name' => $this->validator->isIsset($moduleName),
                'type' => $this->validator->isIsset($typeFile),
                'url' => $this->validator->isIsset($url),
                'fileName' => $fileName,
                'fileExtension' => $fileExtension,
                'blackboardSectionId' => $this->validator->isIsset($this->utils_service->convertFormatUplanner($queryCourse->shortname)),
                'size' => intval($sizeFile),
                'lastUpdatedTime' => $this->validator->isIsset($formattedDateCreated),
                'action' => strtoupper($data['dispatch']),
                'transactionId' => $this->validator->isIsset($this->transition_endpoint->getLastRowTransaction($courseid)),
            ];
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '.  $e->getMessage(). "\n");
        }
        return $dataToSave;
    }

    /**
     * Return the data of the resource
     *
     * @param int $idContext
     * @return object
     */
    private function getDataResource($idContext) : object
    {
        $query = new \stdClass();
        try {

            if (isset($idContext)) {
                $queryResult = $this->moodle_query_handler->executeQuery(
                    sprintf(
                            self::QUERY_FILE_MATERIAL,
                            $idContext
                ));

                if (!empty($queryResult)) {
                    $fileData = reset($queryResult);
                    if (!empty($fileData)) {
                        $query = $fileData;
                    }
                }
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $query;
    }

    /**
     * Return the result for the query given an id
     *
     * @param int $id
     * @return object
     */
    private function getInstanceId($sql, $id) : object
    {
        $query = new \stdClass();
        try {

            if (isset($id)) {
                $queryResult = $this->moodle_query_handler->executeQuery(
                    sprintf(
                            $sql,
                            $id
                ));

                if (!empty($queryResult)) {
                    $data = reset($queryResult);
                    if (!empty($data)) {
                        $query = $data;
                    }
                }
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $query;
    }

    private function getUrlResource($data,$dataFile) : string
    {
        GLOBAL $CFG;
        $url = '';
        try {
            if (!empty($data)) {
                $typeUrl = [
                    'folder' => 'mod/folder/view.php?id=%s',
                    'resource' => 'pluginfile.php/%s/mod_resource/%s/1/%s',
                    'label' => 'mod/label/view.php?id=%s',
                    'lightboxgallery' => 'mod/lightboxgallery/view.php?id=%s',
                    'book' => 'mod/book/view.php?id=%s',
                    'page' => 'mod/page/view.php?id=%s',
                    'url' => 'mod/url/view.php?id=%s',
                    'imscp' => 'mod/imscp/view.php?id=%s',
                ];
                $getData = $this->validator->isArrayData($data->get_data());

                if (isset($getData['other']['modulename'])) {
                    if ($getData['other']['modulename'] === 'resource') {
                        //sacar la url actual
                        $url = $CFG->wwwroot.'/'.sprintf(
                            $typeUrl[$getData['other']['modulename']],
                            $dataFile->contextid ?? '',
                            $dataFile->filearea ?? '',
                            $dataFile->filename ?? ''
                        );
                    }
                    else {
                        $url = $base_url = $CFG->wwwroot.'/'.sprintf(
                            $typeUrl[$getData['other']['modulename']],
                            $this->validator->isIsset($data->objectid)
                        );
                    }
                }
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $url;
    }
}