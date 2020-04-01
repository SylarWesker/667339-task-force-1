<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TaskForce\Utils\CsvFileReader;
use TaskForce\Utils\Csv2SqlConverter;

/*

echo __DIR__;
echo "<br>";
echo  $_SERVER['DOCUMENT_ROOT'];
exit;

*/

$usersCsvFile = join(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], 'data/users.csv']);
$categoriesCsvFile = join(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], 'data/categories.csv']);
$citiesCsvFile = join(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], 'data/cities.csv']);

/*
$filePath = $citiesCsvFile;
$test = new CsvFileReader($filePath, true, true);
$data = $test->readData();

echo '<pre>';
var_dump($data);
echo '</pre>';

exit;
*/


// Пользователи.
/*
$columnsData = [
    ['columnNameTable' => 'email', 'columnNameFile' => 'email', 'columnType' => Csv2SqlConverter::COLUMN_STRING_TYPE],
    ['columnNameTable' => 'full_name', 'columnNameFile' => 'name', 'columnType' => Csv2SqlConverter::COLUMN_STRING_TYPE],
    ['columnNameTable' => 'password', 'columnNameFile' => 'password', 'columnType' => Csv2SqlConverter::COLUMN_STRING_TYPE],
    ['columnNameTable' => 'adding_date', 'columnNameFile' => 'dt_add', 'columnType' => Csv2SqlConverter::COLUMN_DATE_TYPE],
];

$tableName = 'User';

$converter = new Csv2SqlConverter($tableName, $columnsData);
$sql = $converter->convert($usersCsvFile, 'bla-bla-bla');

echo '<pre>';
var_dump($sql);
echo '</pre>';
*/

// Категории
/*
$columnsData = [
    ['columnNameTable' => 'name', 'columnNameFile' => 'name', 'columnType' => Csv2SqlConverter::COLUMN_STRING_TYPE],
    ['columnNameTable' => 'icon_name', 'columnNameFile' => 'icon', 'columnType' => Csv2SqlConverter::COLUMN_STRING_TYPE],
];

$tableName = 'Category';

$converter = new Csv2SqlConverter($tableName, $columnsData);
$sql = $converter->convert($categoriesCsvFile, 'bla-bla-bla');

echo '<pre>';
var_dump($sql);
echo '</pre>';
*/

// Населенные пункты
$columnsData = [
    ['columnNameTable' => 'name', 'columnNameFile' => 'city', 'columnType' => Csv2SqlConverter::COLUMN_STRING_TYPE],
    ['columnNameTable' => 'latitude', 'columnNameFile' => 'lat', 'columnType' => Csv2SqlConverter::COLUMN_NUMBER_TYPE],
    ['columnNameTable' => 'longitude', 'columnNameFile' => 'long', 'columnType' => Csv2SqlConverter::COLUMN_NUMBER_TYPE],
];

$tableName = 'Locality';

$converter = new Csv2SqlConverter($tableName, $columnsData);
$sql = $converter->convert($citiesCsvFile, 'bla-bla-bla');

echo '<pre>';
var_dump($sql);
echo '</pre>';
