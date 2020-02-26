<?php

namespace TaskForce\Utils;

use SplFileObject;

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

class CsvToSqlFileConverter
{
    /**
     * @var string - путь к файлу.
     */
    private string $filePath;

    /**
     * @var bool - является ли первая строка в файле заголовком колонок?
     */
    private bool $firstRowHeader;

    /**
     * @var SplFileObject
     */
    private ?SplFileObject $fileReader;

    /**
     * @var array - данные прочитанные из файла.
     */
    private array $fileData;

    /**
     * CsvToSqlFileConverter constructor.
     * @param string $filePath - путь к файлу.
     * @param bool $firstRowHeader - является ли первая строка заголовком данных.
     */
    public function __construct(string $filePath, bool $firstRowHeader = false)
    {
        $this->filePath = $filePath;
        $this->firstRowHeader = $firstRowHeader;
    }

    public function readData(): array
    {
        if (!file_exists($this->filePath)) {
            throw new \Exception("Файл не существует");
        }

        $this->fileReader = new SplFileObject($this->filePath, 'r');
        $this->fileReader->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        $header = $this->getHeader();
        $data = $this->getData();

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

    private function getData(): ?array
    {
        $data = null;

        foreach ($this->getNextLine() as $line) {
            if ($line) {
                $data[] = $line;
            }
        }

        return $data;
    }

    private function getNextLine(): ?iterable
    {
        $line = null;
        while (!$this->fileReader->eof()) {
            $line = $this->fileReader->fgetcsv();
            yield $line;
        }
    }
}
