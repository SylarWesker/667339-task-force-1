<?php

namespace TaskForce\Utils;

/**
 * Class Csv2SqlConverter - преобразует csv файл с данными в sql инструкции для вставки данных в таблицу.
 * @package TaskForce\Utils
 */
class Csv2SqlConverter
{
    public const COLUMN_DATA_TYPE = 'DATA_TYPE';
    public const COLUMN_STRING_TYPE = 'STRING_TYPE';


    /**
     * @var string - название таблицы куда будут добавлять данные.
     */
    private string $tableName;

    /**
     * @var array - массив с данными о колонках.
     */
    private array $columnsData;

    public function __construct(string $tableName, array $columnsData)
    {
        $this->tableName = $tableName;
        $this->columnsData = $columnsData;
    }

    public function convert(string $dataFilePath, string $sqlFilePath): ?string
    {
        // Чтение данных из файла.
        $reader = new CSVFileReader($dataFilePath, true, true);
        $dataFromFile = $reader->readData();

        // ToDo
        // Проверки. какие? здесь выполнять?
        if (is_null($dataFromFile['data'])) {
            throw new \Exception('Файл пуст или неверный формат.');
        }

        // Валидация.
        // ???

        // ToDo
        // 1. Имена переменных пересмотреть.
        // 2. Еще раз пройтись по алгоритму. Не перемудрил ли?
        // Создание sql запросов.
        $columnNamesInTable = [];
        foreach ($this->columnsData as $index => $value) {
            $key = $value['columnNameTable'];
            $columnNamesInTable[$key] = $index;
        }
        $columnNamesStr = join(',', array_keys($columnNamesInTable));

        $sql = "INSERT INTO $this->tableName($columnNamesStr) VALUES ";

        $valuesToInsert = [];
        foreach ($dataFromFile['data'] as $row) {
            $insertParams = range(0, count($columnNamesInTable) - 1); //$templateForInsert;

            foreach ($this->columnsData as $data) {
                $columnNameTable = $data['columnNameTable'];

                if (array_key_exists('columnNameFile', $data)) {
                    $columnNameFile = $data['columnNameFile'];
                } else {
                    $columnNameFile = $columnNameTable;
                }

                $insertValue = $row[$columnNameFile];

                switch ($data['columnType']) {
                    case Csv2SqlConverter::COLUMN_DATA_TYPE:
                    {
                        $insertValue = "'" . $insertValue . "'";
                        break;
                    }
                    case Csv2SqlConverter::COLUMN_STRING_TYPE:
                    {
                        // ToDo
                        // тут по идее нужно еще экранировать одинарные и двойные кавычи в строке.
                        $insertValue = "'" . $insertValue . "'";
                        break;
                    }
                }

                $index = $columnNamesInTable[$columnNameTable];
                $insertParams[$index] = $insertValue;
            }

            $insertTemplate = join(',', $insertParams);
            $valuesToInsert[] = "($insertTemplate)";
        }


        $valuesToInsertStr = join(',', $valuesToInsert);
        $sql .= $valuesToInsertStr . ";";

        return $sql;
    }
}
