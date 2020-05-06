<?php

namespace TaskForce\Utils;

use SplFileObject;
use TaskForce\Exception\FileException;

/**
 * Class CSVFileReader - предназначен для чтения данных из csv файла.
 *
 * @package TaskForce\Utils
 */
class CsvFileReader
{
    /**
     * @var string - путь к файлу.
     */
    private string $filePath;

    /**
     * @var bool - содержит ли первая строка в файле заголовки колонок.
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
     *
     * @param string $filePath - путь к файлу.
     * @param bool $firstRowHeader - является ли первая строка заголовком данных.
     * @param bool $getAssocData - будет ли возвращаемый массив с данными ассоциативным (ключ - название колонки).
     */
    public function __construct(string $filePath, bool $firstRowHeader = false, bool $getAssocData = false)
    {
        $this->filePath = $filePath;
        $this->firstRowHeader = $firstRowHeader;

        if ($this->firstRowHeader) {
            $this->getAssocData = $getAssocData;
        } else {
            $this->getAssocData = false;
        }
    }

    /**
     * Метод чтения данных из CSV файла.
     *
     * @return array - ассоциативный массив (по ключу 'header' содержаться заголовки данных, по ключу 'data' - сами данные).
     * @throws FileException
     */
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
