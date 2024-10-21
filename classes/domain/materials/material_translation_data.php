<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\materials;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\plugin_config\estruture_types;
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
                if (method_exists($this, $typeTransform) && count($data['data']) > 0) {
                    $arraySend = $this->$typeTransform($data['data']);
                }
            }
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
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
                'array_verification' => estruture_types::UPLANNER_MATERIALS_ESTRUTURE,
                'data' => $data
            ]);

            //Estructure of material
            $arraySend = [
            "id" => $dataSend['id'],
            "name" => $dataSend['name'],
            "type" => $dataSend['type'],
            "url"=> $dataSend['url'],
            "fileName"=> $dataSend['fileName'],
            "fileExtension"=> $dataSend['fileExtension'],
            "parentId" => $dataSend['parentId'],
            "blackboardSectionId" => $dataSend['blackboardSectionId'],
            "size" => $dataSend['size'],
            "lastUpdatedTime" => $dataSend['lastUpdatedTime'],
            "action" => $dataSend['action'],
            "transactionId" => $dataSend['transactionId']
            ];
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $arraySend;
    }
}