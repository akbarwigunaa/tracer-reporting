<?php

namespace App\Services;

use App\Services\Interfaces\ExcelReaderServiceInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

class ExcelReaderService implements ExcelReaderServiceInterface
{
    public function read(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $config = config('tracer.excel');
        $sheet = $this->getSheet($spreadsheet, $config['sheet_name']);
        $fCodeMap = $this->buildFCodeMap($sheet, $config['header_row']);
        $highestRow = $sheet->getHighestRow();
        $dataStartRow = $config['data_start_row'];

        $result = [];

        foreach (config('tracer.parameters') as $key => $param) {
            if ($key === 'kemampuan_diri') {
                $result[$key] = $this->readKemampuanDiri($sheet, $param, $fCodeMap, $dataStartRow, $highestRow);
            } else {
                $columnIndex = $this->resolveColumn($param, $fCodeMap);
                $result[$key] = $this->readColumn($sheet, $columnIndex, $dataStartRow, $highestRow);
            }
        }

        return $result;
    }

    private function getSheet($spreadsheet, string $sheetName): Worksheet
    {
        $sheet = $spreadsheet->getSheetByName($sheetName);

        if ($sheet === null) {
            $available = implode(', ', $spreadsheet->getSheetNames());
            throw new RuntimeException(
                "Sheet \"{$sheetName}\" tidak ditemukan. Sheet tersedia: {$available}"
            );
        }

        return $sheet;
    }

    private function buildFCodeMap(Worksheet $sheet, int $headerRow): array
    {
        $map = [];
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $value = $sheet->getCell([$col, $headerRow])->getValue();

            if ($value !== null) {
                $cleaned = strtolower(trim((string) $value));
                $map[$cleaned] = $col;
            }
        }

        return $map;
    }

    private function resolveColumn(array $param, array $fCodeMap): int
    {
        $fCode = strtolower($param['f_code']);

        if (isset($fCodeMap[$fCode])) {
            return $fCodeMap[$fCode];
        }

        if (isset($param['column'])) {
            return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($param['column']);
        }

        throw new RuntimeException(
            "Kolom untuk f-code \"{$param['f_code']}\" tidak ditemukan di header Excel."
        );
    }

    private function readColumn(Worksheet $sheet, int $columnIndex, int $startRow, int $endRow): array
    {
        $values = [];

        for ($row = $startRow; $row <= $endRow; $row++) {
            $cell = $sheet->getCell([$columnIndex, $row]);
            $value = $cell->getValue();

            if ($value !== null && $value !== '') {
                $values[] = $value;
            }
        }

        return $values;
    }

    private function readKemampuanDiri(
        Worksheet $sheet,
        array $param,
        array $fCodeMap,
        int $startRow,
        int $endRow,
    ): array {
        $result = [];

        foreach ($param['competencies'] as $compKey => $comp) {
            $colA = $this->resolveFCode($comp['column_a'], $fCodeMap);
            $colB = $this->resolveFCode($comp['column_b'], $fCodeMap);

            $result[$compKey] = [
                'name' => $comp['name'],
                'values_a' => $this->readColumn($sheet, $colA, $startRow, $endRow),
                'values_b' => $this->readColumn($sheet, $colB, $startRow, $endRow),
            ];
        }

        return $result;
    }

    private function resolveFCode(string $fCode, array $fCodeMap): int
    {
        $cleaned = strtolower(trim($fCode));

        if (isset($fCodeMap[$cleaned])) {
            return $fCodeMap[$cleaned];
        }

        throw new RuntimeException(
            "Kolom untuk f-code \"{$fCode}\" tidak ditemukan di header Excel."
        );
    }
}
