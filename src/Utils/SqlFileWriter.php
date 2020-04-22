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
        $fileWriter = new \SplFileObject($filePath, 'w');
        $writeResult = $fileWriter->fwrite($sql);
        $fileWriter = null;
    }
}
