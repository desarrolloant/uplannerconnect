<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure;

/**
 *  @package  uPlannerConnect
 *  @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 *  @author Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @description Csv file management
 */
class file
{
    /**
     * Directory path files
     *
     * @var string $filename
     */
    private string $directory = __DIR__ . '/email/files/';

    /**
     * File name
     *
     * @var string $filename
     */
    private string $filename;

    /**
     * Construct
     *
     * @param $filename
     */
    public function __construct(
        $filename
    ) {
        $millis = round(microtime(true) * 1000);
        $this->filename = str_replace("date", $millis, $filename);
    }

    /**
     * Get path file
     *
     * @return string
     */
    public function get_path_file()
    {
        return $this->directory . $this->filename;
    }

    /**
     * Create CSV file
     *
     * @param $headers
     * @return bool
     */
    public function create_csv($headers)
    {
        try {
            if (!file_exists($this->directory)) {
                mkdir($this->directory, 0777, true);
            }
            $csv_file = $this->get_path_file();
            if (!file_exists($csv_file)) {
                $fp = fopen($csv_file, 'w');
                fputcsv($fp, $headers);
                fclose($fp);
                return true;
            }
        } catch (\Exception $e) {
            error_log('create_csv: ',  $e->getMessage(), "\n");
        }

        return false;
    }

    /**
     * Add row
     *
     * @param $data
     * @return bool
     */
    public function add_row($data)
    {
        try {
            $csv_file = $this->get_path_file();
            if (file_exists($csv_file)) {
                $fp = fopen($csv_file, 'a');
                fputcsv($fp, $data);
                fclose($fp);
                return true;
            }
        } catch (\Exception $e) {
            error_log('add_row: ',  $e->getMessage(), "\n");
        }

        return false;
    }

    /**
     * Reset CSV file
     *
     * @param $headers
     * @return bool
     */
    public function reset_csv($headers)
    {
        try {
            $csv_file = $this->get_path_file();
            if (file_exists($csv_file)) {
                unlink($csv_file);
            }
        } catch (\Exception $e) {
            error_log('reset_csv: ',  $e->getMessage(), "\n");
        }

        return $this->create_csv($headers);
    }

    /**
     * Delete CSV file
     *
     * @return void
     */
    public function delete_csv()
    {
        try {
            $csv_file = $this->get_path_file();
            if (file_exists($csv_file)) {
                unlink($csv_file);
            }
        } catch (\Exception $e) {
            error_log('delete_csv: ',  $e->getMessage(), "\n");
        }
    }
}