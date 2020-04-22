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

    private array $tablesRowCount;
    private int $seedForRelations;
    private string $sqlSaveFolder;

    public function __construct($seedForRelations, $saveFolder = "sql_test_data")
    {
        $this->seedForRelations = $seedForRelations;
        $this->sqlSaveFolder = $saveFolder;
    }

    public function work()
    {
        // Зависимости между данными.
        //
        // Населенные пункты - ни от кого не зависит.
        // Категории - ни от кого не зависит.
        // Пользователи - зависит от "Населенные пункты", "Категории".
        // Профили - зависит от "Пользователи".
        // Задачи - зависит от "Категории", "Населенные пункты", "Пользователи".
        // Отклики - зависит от "Задачи", "Пользователи".
        // Отзывы зависит от "Задачи", "Пользователи".
        //
        // Нужно выстроить приоритет загрузки данных.
        // Потом пройтись по всем зависимостям (таблица.поле источника, таблица.поле внешнего ключа, тип зависимости (один к одному, один ко многим)
        // и создать связи.

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

                    /*
                    switch ($table) {
                        case 'category':
                        case 'locality':
                        case 'user':
                        case 'task_status': {
                            // могут повторяться id.
                            for ($counter = 0; $counter < $sourceTableRowsCount; $counter++) {
                                $relations_ids[] = rand(1, $dependencyTableRowsCount);
                            }
                            break;
                        }
                        case 'profile': {
                            // Не должны повторяться id.

                            $all_ids = range(1, $dependencyTableRowsCount); // Генерируем все возможные id.

                            for ($counter = 0; $counter < $sourceTableRowsCount; $counter++) {
                                $index = rand(0, count($all_ids) - 1);
                                $id = $all_ids[$index];

                                $relations_ids[] = $id;

                                array_splice($all_ids, $index, 1);
                                // или еще вариант. не знаю что быстрее работает.
                                // unset($all_ids[$index]);
                                // $all_ids = array_values($all_ids);
                            }
                            break;
                        }
                        case 'task': {
                            // тут самое сложное.
                            // в зависимости от поля меняется правило.
                        }
                    }
                    */

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

        $relations_sql = [];

        /*foreach ($relations as $relation) {
            $dependencies = $relation['dependencies'];
            $table = $relation['table'];

            // ToDo
            // Следует ли передавать в качестве параметра метода поле этого класса.
            if (!empty($dependencies) || !is_null($dependencies)) {
                $relations_sql[] = $this->generateRelations($table, $dependencies, $this->seedForRelations);
            }
        }

        $all_relations_sql = join(PHP_EOL, $relations_sql);
        $relationsFileName = "$this->sqlSaveFolder/relations.sql";
        SqlFileWriter::save($all_relations_sql, $relationsFileName);*/
    }

    /**
     * Создает sql код взаимосвязей данных между таблицами (прописывает значение внешних ключей с помощью UPDATE).
     *
     * @param string $table
     * @param array $dependencies
     * @param int $seed
     *
     * @return string
     */
    private function generateRelations(string $table, array $dependencies, int $seed): string
    {
        $sql = [];
        $source_table_rows_count = $this->tablesRowCount[$table];
        srand($seed);

        for ($row_counter = 1; $row_counter <= $source_table_rows_count; $row_counter++) {
            $set_sql = [];

            foreach ($dependencies as $dependency) {
                $foreign_table = $dependency['foreign_table'];
                $foreign_table_rows_count = $this->tablesRowCount[$foreign_table];

                $field = $dependency['source_table_field'];
                $foreign_key_value = rand(1, $foreign_table_rows_count);

                $set_sql[] = "$field = $foreign_key_value";
            }

            $field_value = join(', ', $set_sql);
            $sql[] = "UPDATE $table SET $field_value WHERE id = $row_counter;";

            //$rel_type = $dependency['relation_type'];

            // Генерация значений внешних ключей.
            /* $foreign_key_values = [];
            if ($rel_type == TEST::ONE_TO_MANY_RELATION) {
            $min_rel_count = $dependency['min_rel_count'];
            $max_rel_count = $dependency['max_rel_count'];

            $rel_count = rand($min_rel_count, $max_rel_count);
            $all_foreign_ids = range(1, $foreign_table_rows_count);

            for($rel_counter = 0; $rel_counter < $rel_count; $rel_counter++) {
            $index = rand(1, count($all_foreign_ids));
            $foreign_key_values[] = $all_foreign_ids[$index];

            $all_foreign_ids = array_slice($all_foreign_ids, $index,1);
            }

            } elseif ($rel_type == TEST::ONE_TO_ONE_RELATION) {
            $foreign_key_values[] = rand(1, $foreign_table_rows_count);
            }*/
        }

        $result = join(PHP_EOL, $sql);
        return $result;
    }

    /**
     *  В зависимости от данных переданных в конвертер выставляем режим чтения файла.
     *
     * @param array $columnsData
     * @return array
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
                ['foreign_table' => 'category', 'source_table_field' => 'category_id', 'relation_type' => Converter::ONE_TO_ONE_RELATION],
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
