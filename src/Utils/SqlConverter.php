<?php

namespace TaskForce\Utils;

/**
 * Class SqlConverter - преобразует csv файл с данными в sql инструкции для вставки данных в таблицу.
 *
 * @package TaskForce\Utils
 */
class SqlConverter
{
    public const COLUMN_DATE_TYPE = 'DATE_TYPE';
    public const COLUMN_STRING_TYPE = 'STRING_TYPE';
    public const COLUMN_NUMBER_TYPE = 'OTHER_TYPE'; // это для тех значений, которые не нужно оборачивать в кавычки

    /**
     * @var string - название таблицы куда будут добавляться данные.
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

    /**
     * Метод преобразования данных из массива в sql код для вставки в БД.
     *
     * @param array $data - данные.
     * @param array $header - заголовки данных.
     * @param array $relationData
     *
     * @return string|null - sql код вставки данных в БД.
     * @throws \Exception
     */
    public function convert(array $data, array $header, array $relationData = null): ?string
    {
        // Валидация.
        $validateResult = $this->validate($header, $data);
        if (!$validateResult) {
            throw new \Exception('В файле отсутсвуют необходимые колонки');
        }

        // Преобразование в sql.
        $sql = $this->convertDataToSql($data, $relationData);
        return $sql;
    }

    /**
     * Проверяеть есть ли в файле все необходимые колонки.
     *
     * @param $header - названия колонок в файле.
     * @param $data - данные из файла.
     *
     * @return bool - результат проверки.
     */
    private function validate(array $header, array $data): bool
    {
        $result = true;

        if (is_null($header)) {
            $result = count($data[0]) == count($this->columnsData);
            return $result;
        }

        foreach ($this->columnsData as $columnData) {
            if (!in_array($columnData['columnInFile'], $header)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     *
     * @param $data - двумерный массив с данными, которые нужно добавить в БД.
     * @param $relationData
     *
     * @return string|null - sql запрос с командой INSERT и данными из $data.
     */
    private function convertDataToSql(array $data, array $relationData = null): ?string
    {
        // $columnNamesInTable нужно для подстановки данных из файла в правильные позиции в sql запросе.
        $columnNamesInTable = []; // ключ - имя колонки в таблице, значение - индекс параметра в sql запросе.
        foreach ($this->columnsData as $index => $value) {
            $key = $value['columnNameTable'];
            $columnNamesInTable[$key] = $index;
        }
        $columnNamesStr = join(',', array_keys($columnNamesInTable));

        // Имена колонок внешних ключей.
        if(!empty($relationData)) {
            $relationColumns = array_keys($relationData);
            $columnNamesStr .= ',' . join(',', $relationColumns);
        }

        $sql = "INSERT INTO $this->tableName($columnNamesStr) VALUES ";

        $rowIndex = 0;
        $valuesToInsert = [];
        foreach ($data as $row) { // цикл по строкам из файла.
            $insertParams = range(0, count($columnNamesInTable) - 1);

            foreach ($this->columnsData as $columnData) { // цикл по колонкам, которые нужны из файла.
                // Если нет columnInFile, то значит предполагаем, что в файле имена колонок такие же как в таблице в БД.
                if (array_key_exists('columnInFile', $columnData)) {
                    $columnName = $columnData['columnInFile'];
                } else {
                    $columnName = $columnData['columnNameTable'];
                }

                $insertValue = $row[$columnName];

                switch ($columnData['columnType']) {
                    case SqlConverter::COLUMN_DATE_TYPE:
                    {
                        $insertValue = "'" . $insertValue . "'";
                        break;
                    }
                    case SqlConverter::COLUMN_STRING_TYPE:
                    {
                        $insertValue = "'" . addslashes ($insertValue) . "'";
                        break;
                    }
                }

                $index = $columnNamesInTable[$columnData['columnNameTable']];
                $insertParams[$index] = $insertValue;
            }

            // данные внешних ключей.
            if(!empty($relationData)) {
                foreach ($relationData as $relationColumnData) {
                    $insertParams[] = $relationColumnData[$rowIndex];
                }
            }
            $rowIndex++;

            $insertTemplate = join(',', $insertParams);
            $valuesToInsert[] = "($insertTemplate)";
        }

        $valuesToInsertStr = join(',' . PHP_EOL, $valuesToInsert);
        $sql .= $valuesToInsertStr . ";";

        return $sql;
    }
}
