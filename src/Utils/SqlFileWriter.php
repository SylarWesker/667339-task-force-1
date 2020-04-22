<?php

namespace TaskForce\Utils;

/**
 * Class SqlFileWriter - записывает sql код в файл.
 *
 * @package TaskForce\Utils
 */
class SqlFileWriter
{
    /**
     * @param string $sql - строка с sql кодом.
     * @param string $filePath - путь к файлу, куда сохраняем sql код.
     */
    public static function save(string $sql, string $filePath)
    {
        $file_directory = dirname($filePath);
        if(!is_dir($file_directory)) {
            mkdir($file_directory);
        }

        $fileWriter = new \SplFileObject($filePath, 'w');
        $writeResult = $fileWriter->fwrite($sql);
        $fileWriter = null;
    }
}
