<?php

namespace App\Services;

use App\Models\Report;
use App\Models\TracerStudy;
use App\Repositories\Interfaces\AnalysisResultRepositoryInterface;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Services\Interfaces\ReportGeneratorServiceInterface;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Font;
use RuntimeException;

class ReportGeneratorService implements ReportGeneratorServiceInterface
{
    private const FONT_NAME = 'Times New Roman';
    private const COLOR_PRIMARY = '1F4E79';
    private const COLOR_HEADER_BG = 'D6E4F0';

    public function __construct(
        private AnalysisResultRepositoryInterface $analysisResultRepository,
        private ReportRepositoryInterface $reportRepository,
    ) {}

    public function generate(TracerStudy $tracerStudy): Report
    {
        $results = $this->analysisResultRepository->findByTracerStudy($tracerStudy->id);

        if ($results->isEmpty()) {
            throw new RuntimeException('Tidak ada hasil analisis untuk tracer study ini.');
        }

        $errorLevel = error_reporting(E_ERROR);

        $phpWord = new PhpWord();
        $this->setupStyles($phpWord);

        $section = $phpWord->addSection([
            'marginTop' => 1440,
            'marginBottom' => 1440,
            'marginLeft' => 1440,
            'marginRight' => 1440,
        ]);

        $this->addCoverPage($section, $tracerStudy);
        $section->addPageBreak();
        $this->addSummarySection($section, $tracerStudy);
        $section->addTextBreak(1);

        foreach ($results as $result) {
            $this->addParameterSection($section, $result);
            $section->addTextBreak(1);
        }

        $this->addFooter($section);

        $filename = sprintf(
            'Laporan_Tracer_%s_%s_%s.docx',
            str_replace(' ', '_', $tracerStudy->nama_prodi),
            $tracerStudy->tahun_lulusan,
            $tracerStudy->tahun_tracer,
        );
        $relativePath = 'reports/' . $filename;
        $absolutePath = Storage::disk('local')->path($relativePath);

        $dir = dirname($absolutePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $phpWord->save($absolutePath);
        error_reporting($errorLevel);
        $fileSize = filesize($absolutePath);

        return $this->reportRepository->create([
            'tracer_study_id' => $tracerStudy->id,
            'filename' => $filename,
            'file_path' => $relativePath,
            'file_size' => $fileSize,
        ]);
    }

    private function setupStyles(PhpWord $phpWord): void
    {
        $phpWord->setDefaultFontName(self::FONT_NAME);
        $phpWord->setDefaultFontSize(12);

        $phpWord->addTitleStyle(1, [
            'bold' => true,
            'size' => 16,
            'color' => self::COLOR_PRIMARY,
        ], ['spaceAfter' => 120]);

        $phpWord->addTitleStyle(2, [
            'bold' => true,
            'size' => 13,
            'color' => self::COLOR_PRIMARY,
        ], ['spaceAfter' => 80, 'spaceBefore' => 240]);
    }

    private function addCoverPage($section, TracerStudy $tracerStudy): void
    {
        $section->addTextBreak(4);

        $section->addText(
            'LAPORAN HASIL TRACER STUDY',
            ['bold' => true, 'size' => 20, 'color' => self::COLOR_PRIMARY],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 200],
        );

        $section->addText(
            $tracerStudy->nama_prodi . ' (' . $tracerStudy->jenjang . ')',
            ['bold' => true, 'size' => 16],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 120],
        );

        $section->addText(
            $tracerStudy->nama_institusi,
            ['size' => 14],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 400],
        );

        $section->addText(
            'Tahun Lulusan: ' . $tracerStudy->tahun_lulusan,
            ['size' => 13],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 60],
        );

        $section->addText(
            'Tahun Pelaksanaan Tracer: ' . $tracerStudy->tahun_tracer,
            ['size' => 13],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 400],
        );

        $section->addText(
            'Dibuat secara otomatis oleh Sistem Tracer Reporting',
            ['size' => 10, 'italic' => true, 'color' => '666666'],
            ['alignment' => Jc::CENTER],
        );
    }

    private function addSummarySection($section, TracerStudy $tracerStudy): void
    {
        $section->addTitle('Ringkasan', 1);

        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => 'CCCCCC',
            'cellMargin' => 80,
            'unit' => TblWidth::TWIP,
        ]);

        $summaryData = [
            ['Nama Institusi', $tracerStudy->nama_institusi],
            ['Program Studi', $tracerStudy->nama_prodi],
            ['Jenjang', $tracerStudy->jenjang],
            ['Tahun Lulusan', $tracerStudy->tahun_lulusan],
            ['Tahun Tracer', $tracerStudy->tahun_tracer],
            ['Total Lulusan', number_format($tracerStudy->total_lulusan)],
            ['Total Responden', number_format($tracerStudy->total_responden)],
            ['Response Rate', number_format($tracerStudy->response_rate, 2) . '%'],
        ];

        foreach ($summaryData as $row) {
            $tableRow = $table->addRow();
            $tableRow->addCell(3000, ['bgColor' => self::COLOR_HEADER_BG])
                ->addText($row[0], ['bold' => true, 'size' => 11]);
            $tableRow->addCell(6000)
                ->addText($row[1], ['size' => 11]);
        }
    }

    private function addParameterSection($section, $result): void
    {
        $section->addTitle($result->order . '. ' . $result->parameter_name, 2);

        $section->addText(
            $result->narrative,
            ['size' => 12],
            ['spaceAfter' => 120],
        );

        match ($result->statistic_type) {
            'frequency' => $this->addFrequencyTable($section, $result->result_data),
            'mean_median' => $this->addMeanMedianTable($section, $result->result_data),
            'mean' => $this->addMeanTable($section, $result->result_data),
            'sum' => $this->addSumTable($section, $result->result_data),
            'display' => $this->addDisplayList($section, $result->result_data),
            'index' => $this->addIndexTable($section, $result->result_data),
        };
    }

    private function addFrequencyTable($section, array $data): void
    {
        $table = $this->createTable($section);

        $headerRow = $table->addRow();
        $this->addHeaderCell($headerRow, 'Kategori', 4500);
        $this->addHeaderCell($headerRow, 'Jumlah', 2000);
        $this->addHeaderCell($headerRow, 'Persentase', 2500);

        foreach ($data['frequencies'] as $freq) {
            $row = $table->addRow();
            $row->addCell(4500)->addText($freq['label'], ['size' => 11]);
            $row->addCell(2000)->addText(
                (string) $freq['count'],
                ['size' => 11],
                ['alignment' => Jc::END],
            );
            $row->addCell(2500)->addText(
                number_format($freq['percentage'], 1) . '%',
                ['size' => 11],
                ['alignment' => Jc::END],
            );
        }

        $footerRow = $table->addRow();
        $footerRow->addCell(4500, ['bgColor' => 'F2F2F2'])
            ->addText('Total', ['bold' => true, 'size' => 11]);
        $footerRow->addCell(2000, ['bgColor' => 'F2F2F2'])
            ->addText((string) $data['total'], ['bold' => true, 'size' => 11], ['alignment' => Jc::END]);
        $footerRow->addCell(2500, ['bgColor' => 'F2F2F2'])
            ->addText('100%', ['bold' => true, 'size' => 11], ['alignment' => Jc::END]);
    }

    private function addMeanMedianTable($section, array $data): void
    {
        $table = $this->createTable($section);

        $rows = [
            ['Rata-rata (Mean)', 'Rp ' . number_format($data['mean'], 0, ',', '.')],
            ['Median', 'Rp ' . number_format($data['median'], 0, ',', '.')],
            ['Minimum', 'Rp ' . number_format($data['min'], 0, ',', '.')],
            ['Maksimum', 'Rp ' . number_format($data['max'], 0, ',', '.')],
            ['Jumlah Data', (string) $data['count']],
        ];

        foreach ($rows as $row) {
            $tableRow = $table->addRow();
            $tableRow->addCell(4500, ['bgColor' => self::COLOR_HEADER_BG])
                ->addText($row[0], ['bold' => true, 'size' => 11]);
            $tableRow->addCell(4500)
                ->addText($row[1], ['size' => 11], ['alignment' => Jc::END]);
        }
    }

    private function addMeanTable($section, array $data): void
    {
        $table = $this->createTable($section);

        $rows = [
            ['Rata-rata', $data['mean'] . ' bulan'],
            ['Jumlah Data', (string) $data['count']],
        ];

        foreach ($rows as $row) {
            $tableRow = $table->addRow();
            $tableRow->addCell(4500, ['bgColor' => self::COLOR_HEADER_BG])
                ->addText($row[0], ['bold' => true, 'size' => 11]);
            $tableRow->addCell(4500)
                ->addText($row[1], ['size' => 11], ['alignment' => Jc::END]);
        }
    }

    private function addSumTable($section, array $data): void
    {
        $table = $this->createTable($section);

        $rows = [
            ['Total', number_format($data['total_sum'])],
            ['Rata-rata', number_format($data['mean'], 1)],
            ['Jumlah Data', (string) $data['count']],
        ];

        foreach ($rows as $row) {
            $tableRow = $table->addRow();
            $tableRow->addCell(4500, ['bgColor' => self::COLOR_HEADER_BG])
                ->addText($row[0], ['bold' => true, 'size' => 11]);
            $tableRow->addCell(4500)
                ->addText($row[1], ['size' => 11], ['alignment' => Jc::END]);
        }
    }

    private function addDisplayList($section, array $data): void
    {
        if (empty($data['values'])) {
            $section->addText('Tidak ada data.', ['italic' => true, 'size' => 11]);
            return;
        }

        foreach ($data['values'] as $i => $value) {
            $section->addListItem(
                $value,
                0,
                ['size' => 11],
            );
        }

        $section->addText(
            'Total: ' . $data['count'] . ' data',
            ['italic' => true, 'size' => 10, 'color' => '666666'],
            ['spaceAfter' => 60],
        );
    }

    private function addIndexTable($section, array $data): void
    {
        $table = $this->createTable($section);

        $headerRow = $table->addRow();
        $this->addHeaderCell($headerRow, 'Kompetensi', 3500);
        $this->addHeaderCell($headerRow, 'Dikuasai', 1800);
        $this->addHeaderCell($headerRow, 'Dibutuhkan', 1800);
        $this->addHeaderCell($headerRow, 'Gap', 1900);

        foreach ($data['competencies'] as $comp) {
            $row = $table->addRow();
            $row->addCell(3500)->addText($comp['name'], ['size' => 11]);
            $row->addCell(1800)->addText(
                number_format($comp['index_a'], 2),
                ['size' => 11],
                ['alignment' => Jc::END],
            );
            $row->addCell(1800)->addText(
                number_format($comp['index_b'], 2),
                ['size' => 11],
                ['alignment' => Jc::END],
            );
            $row->addCell(1900)->addText(
                number_format($comp['gap'], 2),
                ['size' => 11, 'color' => $comp['gap'] < 0 ? 'CC0000' : '008800'],
                ['alignment' => Jc::END],
            );
        }
    }

    private function createTable($section): \PhpOffice\PhpWord\Element\Table
    {
        return $section->addTable([
            'borderSize' => 6,
            'borderColor' => 'CCCCCC',
            'cellMargin' => 80,
            'unit' => TblWidth::TWIP,
        ]);
    }

    private function addHeaderCell($row, string $text, int $width): void
    {
        $row->addCell($width, ['bgColor' => self::COLOR_HEADER_BG])
            ->addText($text, ['bold' => true, 'size' => 11]);
    }

    private function addFooter($section): void
    {
        $section->addTextBreak(2);
        $section->addText(
            'Laporan ini dihasilkan secara otomatis oleh Sistem Web Tracer Reporting.',
            ['italic' => true, 'size' => 10, 'color' => '999999'],
            ['alignment' => Jc::CENTER],
        );
    }
}
