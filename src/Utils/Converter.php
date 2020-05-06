<?php

namespace TaskForce\Utils;

/**
 * Class Converter - конвертер данных из файла в файл с sql кодом вставки в БД.
 *
 * @package TaskForce\Utils
 */
class Converter
{
    public const ONE_TO_ONE_RELATION = 1;
    public const ONE_TO_MANY_RELATION = 2;

    /**
     * @var array - ключ имя таблицы, значение - кол-во строк в файле с данными для этой таблицы.
     */
    private array $tablesRowCount;

    /**
     * @var int - значение для рандомизатора связей между таблицами.
     */
    private int $seedForRelations;

    /**
     * @var string - папка куда будут сохранены sql дампы.
     */
    private string $sqlSaveFolder;

    public function __construct($seedForRelations, $saveFolder = "sql_test_data")
    {
        $this->seedForRelations = $seedForRelations;
        $this->sqlSaveFolder = $saveFolder;
    }

    public function work()
    {
        $this->tablesRowCount = [];

        $filesConfigData = $this->getFileConfigData();
        $relations = $this->getRelationsConfigData();

        srand($this->seedForRelations);

        foreach ($filesConfigData as $fileConfig) {
            $table = $fileConfig['table'];
            $columnsData = $fileConfig['columns_data'];

            $dataFilePath = join(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], $fileConfig['file']]);

            $readerParams = $this->getReaderParams($columnsData);
            $firstHeader = $readerParams['firstHeader'];
            $getAssocData = $readerParams['getAssocData'];

            // Чтение данных из файла.
            $reader = new CsvFileReader($dataFilePath, $firstHeader, $getAssocData);
            $dataFromFile = $reader->readData();

            if (is_null($dataFromFile['data'])) {
                throw new \Exception('Файл пуст или неверный формат.');
            }

            // Подсчет строк в файле (потом использую для генерации связей).
            $this->tablesRowCount[$table] = count($dataFromFile['data']);

            // Генерация данных для связей.
            $dependency_data = [];

            if (!empty($relations[$table]['dependencies'])) {
                $dependencies = $relations[$table]['dependencies'];

                // Пока только один к одному.
                foreach ($dependencies as $dependency) {
                    $foreign_table = $dependency['foreign_table'];
                    $source_table_field = $dependency['source_table_field'];

                    $sourceTableRowsCount = $this->tablesRowCount[$table];
                    $dependencyTableRowsCount = $this->tablesRowCount[$foreign_table];

                    $relations_ids = [];

                    for ($counter = 0; $counter < $sourceTableRowsCount; $counter++) {
                        $relations_ids[] = rand(1, $dependencyTableRowsCount);
                    }

                    $dependency_data[$source_table_field] = $relations_ids;
                }
            }

            // Формирование sql запроса на вставку данных.
            $converter = new SqlConverter($table, $columnsData);
            $insert_sql = $converter->convert($dataFromFile['data'], $dataFromFile['header'], $dependency_data);

            // Сохранение в файл.
            $fileName = "$this->sqlSaveFolder" . DIRECTORY_SEPARATOR . "data to insert in table - $table.sql";
            $saveFilePath = join(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], $fileName]);
            SqlFileWriter::save($insert_sql, $saveFilePath);
        }
    }

    /**
     *  В зависимости от данных переданных в конвертер выставляем режим чтения файла.
     *
     * @param array $columnsData - данные о колонках в файле.
     * @return array - настроечные параметры для класса чтения данных из файла.
     */
    private function getReaderParams($columnsData)
    {
        $firstHeader = false;
        $getAssocData = false;

        if (array_key_exists('columnInFile', $columnsData[0])) {
            $columnValue = $columnsData[0]['columnInFile'];

            if (gettype($columnValue) == 'integer') {
                $firstHeader = false;
                $getAssocData = false;
            } else if (gettype($columnValue) == 'string') {
                $firstHeader = true;
                $getAssocData = true;
            }
        } else {
            $firstHeader = false;
            $getAssocData = false;
        }

        return compact('firstHeader', 'getAssocData');
    }

    /**
     * Возвращает данные с параметрами csv файлов для создания sql файлов.
     *
     * @return array[]
     */
    private function getFileConfigData()
    {
        $categoriesInitData = [
            'file' => 'data/categories.csv',
            'table' => 'category',
            'columns_data' => [
                ['columnNameTable' => 'name',       'columnInFile' => 'name', 'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'icon_name',  'columnInFile' => 'icon', 'columnType' => SqlConverter::COLUMN_STRING_TYPE],
            ]
        ];
        $localitiesInitData = [
            'file' => 'data/cities.csv',
            'table' => 'locality',
            'columns_data' => [
                ['columnNameTable' => 'name',       'columnInFile' => 'city',   'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'latitude',   'columnInFile' => 'lat',    'columnType' => SqlConverter::COLUMN_NUMBER_TYPE],
                ['columnNameTable' => 'longitude',  'columnInFile' => 'long',   'columnType' => SqlConverter::COLUMN_NUMBER_TYPE],
            ]
        ];
        $usersInitData = [
            'file' => 'data/users.csv',
            'table' => 'user',
            'columns_data' => [
                ['columnNameTable' => 'email',      'columnInFile' => 'email',      'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'full_name',  'columnInFile' => 'name',       'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'password',   'columnInFile' => 'password',   'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'add_data',   'columnInFile' => 'dt_add',     'columnType' => SqlConverter::COLUMN_DATE_TYPE],
            ]
        ];
        $taskStatusInitData = [
            'file' => 'data/task_status.csv',
            'table' => 'task_status',
            'columns_data' => [
                ['columnNameTable' => 'name',  'columnInFile' => 'name',    'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'text',  'columnInFile' => 'text',    'columnType' => SqlConverter::COLUMN_STRING_TYPE],
            ]
        ];
        $profilesInitData = [
            'file' => 'data/profiles.csv',
            'table' => 'profile',
            'columns_data' => [
                ['columnNameTable' => 'address',  'columnInFile' => 'address', 'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'birthday', 'columnInFile' => 'bd', 'columnType' => SqlConverter::COLUMN_DATE_TYPE],
                ['columnNameTable' => 'about', 'columnInFile' => 'about', 'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'phone', 'columnInFile' => 'phone', 'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'skype', 'columnInFile' => 'skype', 'columnType' => SqlConverter::COLUMN_STRING_TYPE],
            ]
        ];
        $tasksInitData = [
            'file' => 'data/tasks.csv',
            'table' => 'task',
            'columns_data' => [
                ['columnNameTable' => 'creation_date',  'columnInFile' => 'dt_add',         'columnType' => SqlConverter::COLUMN_DATE_TYPE],
                ['columnNameTable' => 'category_id',    'columnInFile' => 'category_id',    'columnType' => SqlConverter::COLUMN_NUMBER_TYPE],
                ['columnNameTable' => 'description',    'columnInFile' => 'name',           'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'finish_date',    'columnInFile' => 'expire',         'columnType' => SqlConverter::COLUMN_DATE_TYPE],
                ['columnNameTable' => 'details',        'columnInFile' => 'description',    'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'address',        'columnInFile' => 'address',        'columnType' => SqlConverter::COLUMN_STRING_TYPE],
                ['columnNameTable' => 'budget',         'columnInFile' => 'budget',         'columnType' => SqlConverter::COLUMN_NUMBER_TYPE],
                ['columnNameTable' => 'latitude',       'columnInFile' => 'lat',            'columnType' => SqlConverter::COLUMN_NUMBER_TYPE],
                ['columnNameTable' => 'longitude',      'columnInFile' => 'long',           'columnType' => SqlConverter::COLUMN_NUMBER_TYPE],
            ]
        ];
        $responseInitData = [
            'file' => 'data/replies.csv',
            'table' => 'response',
            'columns_data' => [
                ['columnNameTable' => 'add_date', 'columnInFile' => 'dt_add', 'columnType' => SqlConverter::COLUMN_DATE_TYPE],
                ['columnNameTable' => 'comment', 'columnInFile' => 'description', 'columnType' => SqlConverter::COLUMN_STRING_TYPE],
            ]
        ];
        $reviewInitData = [
            'file' => 'data/opinions.csv',
            'table' => 'review',
            'columns_data' => [
                ['columnNameTable' => 'add_date',   'columnInFile' => 'dt_add', 'columnType' => SqlConverter::COLUMN_DATE_TYPE],
                ['columnNameTable' => 'rate',       'columnInFile' => 'rate', 'columnType' => SqlConverter::COLUMN_NUMBER_TYPE],
                ['columnNameTable' => 'comment',    'columnInFile' => 'description', 'columnType' => SqlConverter::COLUMN_STRING_TYPE],
            ]
        ];

        return [
            $categoriesInitData,
            $localitiesInitData,
            $usersInitData,
            $taskStatusInitData,
            $profilesInitData,
            $tasksInitData,
            $responseInitData,
            $reviewInitData,
        ];
    }

    /**
     * Возвращает данные для создания связей между таблицами.
     *
     * @return array[]
     */
    private function getRelationsConfigData(): array
    {
        // ключ - имя таблицы в БД.
        $relations = [
            'category' => ['dependencies' => []],
            'locality' => ['dependencies' => []],
            'task_status' => ['dependencies' => []],
            'user' => ['dependencies' => [
                ['foreign_table' => 'locality', 'source_table_field' => 'locality_id', 'relation_type' => Converter::ONE_TO_ONE_RELATION],
                // ['foreign_table' => 'category', 'source_table_field' => 'user_id', 'via_table' => 'user_specialization', 'relation_type' => Converter::ONE_TO_MANY_RELATION],
            ]],
            'profile' => ['dependencies' => [
                ['foreign_table' => 'user', 'source_table_field' => 'user_id', 'relation_type' => Converter::ONE_TO_ONE_RELATION],
            ]],
            'task' => ['dependencies' => [
                ['foreign_table' => 'user', 'source_table_field' => 'client_id', 'relation_type' => Converter::ONE_TO_ONE_RELATION],
                ['foreign_table' => 'user', 'source_table_field' => 'performer_id', 'relation_type' => Converter::ONE_TO_ONE_RELATION],
                ['foreign_table' => 'task_status', 'source_table_field' => 'status_id', 'relation_type' => Converter::ONE_TO_ONE_RELATION],
                ['foreign_table' => 'locality', 'source_table_field' => 'locality_id', 'relation_type' => Converter::ONE_TO_ONE_RELATION],
            ]],
            'response' => ['dependencies' => [
                ['foreign_table' => 'user', 'source_table_field' => 'candidate_id', 'relation_type' => Converter::ONE_TO_ONE_RELATION],
                ['foreign_table' => 'task', 'source_table_field' => 'task_id', 'relation_type' => Converter::ONE_TO_ONE_RELATION],
            ]],
            'review' => ['dependencies' => [
                ['foreign_table' => 'task', 'source_table_field' => 'task_id', 'relation_type' => Converter::ONE_TO_ONE_RELATION],
            ]],
        ];

        return $relations;
    }
}
