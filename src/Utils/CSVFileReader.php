<?php

namespace TaskForce\Utils;

use SplFileObject;
use TaskForce\Exception\FileException;

// Общий план.
// 1. Открыть и прочитать csv файл.
// 2. Конвертировать данные из файла в sql скрипт (учитывая структуру таблицы).
// 3. После прочтения и формирования всех файлов нужно создать взаимосвязи между загружаеммыми данными.

// Важен порядок загрузки файлов (понять и записать порядок).
// Разобраться с классами для загрузки. сколько, кто за что отвечает.
// решить в лоб. потом попробовать решить красиво.

// Классы.
//
// Класс преобразования в sql код.
// нужны: данные из csv файла, имя таблицы, карта отображения на колонки таблицы
//
// Класс определяющий порядок чтения и формирования sql файлов.
// знает порядок формирования файлов, опционально может строить связи (также знает о связях).
//
// Класс построитель связей.
// Содержит карту связей, также имеет данные для их построения.

/**
 * Class CSVFileReader - предназначен для чтения данных из csv файла.
 * @package TaskForce\Utils
 */
class CSVFileReader
{
    /**
     * @var string - путь к файлу.
     */
    private string $filePath;

    /**
     * @var bool - содердит ли первая строка в файле заголовки колонок.
     */
    private bool $firstRowHeader;

    /**
     * @var bool - вернуть данные в виде ассоциативного массива (ключ - название колонки).
     */
    private bool $getAssocData;

    /**
     * @var SplFileObject - используется для чтения csv файла.
     */
    private ?SplFileObject $fileReader;

    /**
     * CsvToSqlFileConverter constructor.
     * @param string $filePath - путь к файлу.
     * @param bool $firstRowHeader - является ли первая строка заголовком данных.
     * @param bool $getAssocData - будет ли возвращаемый массив с данными ассоциативным (ключ - название колонки).
     */
    public function __construct(string $filePath, bool $firstRowHeader = false, bool $getAssocData = false)
    {
        $this->filePath = $filePath;
        $this->firstRowHeader = $firstRowHeader;
        $this->getAssocData = $getAssocData;
    }

    public function readData(): array
    {
        if (!file_exists($this->filePath)) {
            throw new FileException("Файл не существует.");
        }

        try {
            $this->fileReader = new SplFileObject($this->filePath, 'r');
        } catch (\RuntimeException $run_exc) {
            throw new FileException("Не получилось открыть файл для чтения.");
        } catch (\LogicException $logic_exc) {
            throw new FileException("Указанный путь является директорией, а не файлом.");
        }

        $this->fileReader->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        $header = $this->getHeader();
        $data = $this->getData($header);

        $this->fileReader = null;

        return ['header' => $header, 'data' => $data];
    }

    private function getHeader(): ?array
    {
        if (!$this->firstRowHeader) {
            return null;
        }

        $this->fileReader->rewind();
        $line = $this->fileReader->fgetcsv();

        return $line;
    }

    private function getData($header): ?array
    {
        $data = null;

        foreach ($this->getNextLine() as $line) {
            if ($line) {
                if ($this->getAssocData) {
                    $newLine = [];

                    foreach ($line as $key => $value) {
                        $columnName = $header[$key];
                        $newLine[$columnName] = $value;
                    }

                    $line = $newLine;
                }

                $data[] = $line;
            }
        }

        return $data;
    }

    private function getNextLine(): ?iterable
    {
        while (!$this->fileReader->eof()) {
            $line = $this->fileReader->fgetcsv();
            yield $line;
        }
    }
}
