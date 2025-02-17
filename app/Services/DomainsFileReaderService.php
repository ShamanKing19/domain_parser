<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;

class DomainsFileReaderService
{
    private UploadedFile $file;

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * @throws Exception
     */
    public function parse(): array
    {
        switch ($this->file->extension()) {
            case 'txt':
                return $this->parseTxt();
            case 'csv':
                return $this->parseCsv();
            case 'xlsx':
                return $this->parseXlsx();
            default:
                throw new Exception('Неверное расширение файла');
        }
    }

    private function parseTxt(): array
    {
        $fh = fopen($this->file->path(), 'r');
        $rows = [];
        while ($line = fgets($fh)) {
            $rows[] = trim($line);
        }

        return $rows;
    }

    private function parseCsv(string $separator = ';'): array
    {
        if (($handle = fopen($this->file->path(), 'r', $separator)) === false) {
            return [];
        }

        $domains = [];
        while (($row = fgetcsv($handle, 1000)) !== false) {
            $data = explode($separator, $row);
            $domains[] = $data[0];
        }

        fclose($handle);

        return $domains;
    }

    // TODO: Implement
    private function parseXlsx(): array
    {
        return [];
    }
}
