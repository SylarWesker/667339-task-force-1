<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TaskForce\Utils\Converter;

// Тестовый файл для запуска конвертации файлов в sql дампы.
$converter = new Converter(101, 'sql_test_data');
$converter->work();
