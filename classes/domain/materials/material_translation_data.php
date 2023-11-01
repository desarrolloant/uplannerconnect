<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\materials;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\plugin_config\plugin_config;
use moodle_exception;

/**
   * Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class material_translation_data
{
    private $typeTransform;
    private $validator;

    public function __construct()
    {
        $this->typeTransform = [
            'resource_created' => 'createFormatMaterial',
        ];
        $this->validator = new data_validator();
    }

    /**
     * Convierte los datos acorde al evento que se requiera
     *
     * @param array $data
     * @return array
     */
    public function converDataJsonUplanner(array $data): array
    {
        $arraySend = [];
        try {
            if (array_key_exists(
                  $data['typeEvent'],
                  $this->typeTransform
            )) {
                //Traer la información
                $typeTransform = $this->typeTransform[$data['typeEvent']];
                //verificar si existe el método
                if (method_exists($this, $typeTransform)) {
                    $arraySend = $this->$typeTransform($data['data']);
                }
            }
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $arraySend;
    }

    /**
     * Create structure array in format of uplanner
     *
     * @param array $data
     * @return array
     */
    private function createFormatMaterial(array $data) : array
    {
        $arraySend = [];
        try {
            $dataSend = $this->validator->verifyArrayKeyExist([
                'array_verification' => plugin_config::UPLANNER_MATERIALS_ESTRUTURE,
                'data' => $data
            ]);
            
            //Estructure of material
            $arraySend = [
            "id" => $dataSend['id'],
            "name" => $dataSend['name'],
            "type" => $dataSend['type'],
            "url"=> $dataSend['url'],
            "parentId" => $dataSend['parentId'],
            "blackboardSectionId" => $dataSend['blackboardSectionId'],
            "size" => $dataSend['size'],
            "lastUpdatedTime" => $dataSend['lastUpdatedTime'],
            "action" => $dataSend['action']
            ];
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $arraySend;
    }
}