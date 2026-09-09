<?php

namespace App\Services;

use App\Models\Report;
use App\Models\TracerStudy;
use App\Repositories\Interfaces\AnalysisResultRepositoryInterface;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Services\Interfaces\ReportGeneratorServiceInterface;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ReportGeneratorService implements ReportGeneratorServiceInterface
{
    private const COLOR_PRIMARY = '1F4E79';
    private const COLOR_HEADER_BG = 'D6E4F0';
    private const COLOR_FOOTER_BG = 'F2F2F2';

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

        $doc = new DocxBuilder();

        $this->addCoverPage($doc, $tracerStudy);
        $doc->addPageBreak();
        $this->addSummarySection($doc, $tracerStudy);
        $doc->addTextBreak(1);

        foreach ($results as $result) {
            $this->addParameterSection($doc, $result);
            $doc->addTextBreak(1);
        }

        $this->addFooter($doc);

        $filename = sprintf(
            'Laporan_Tracer_%s_%s_%s.docx',
            str_replace(' ', '_', $tracerStudy->nama_prodi),
            $tracerStudy->tahun_lulusan,
            $tracerStudy->tahun_tracer,
        );
        $relativePath = 'reports/' . $filename;
        $absolutePath = Storage::disk('local')->path($relativePath);

        $doc->save($absolutePath);
        $fileSize = filesize($absolutePath);

        return $this->reportRepository->create([
            'tracer_study_id' => $tracerStudy->id,
            'filename' => $filename,
            'file_path' => $relativePath,
            'file_size' => $fileSize,
        ]);
    }

    private function addCoverPage(DocxBuilder $doc, TracerStudy $tracerStudy): void
    {
        $doc->addTextBreak(4);

        $doc->addText(
            'LAPORAN HASIL TRACER STUDY',
            ['bold' => true, 'size' => 20, 'color' => self::COLOR_PRIMARY],
            ['alignment' => 'center', 'spaceAfter' => 200],
        );

        $doc->addText(
            $tracerStudy->nama_prodi . ' (' . $tracerStudy->jenjang . ')',
            ['bold' => true, 'size' => 16],
            ['alignment' => 'center', 'spaceAfter' => 120],
        );

        $doc->addText(
            $tracerStudy->nama_institusi,
            ['size' => 14],
            ['alignment' => 'center', 'spaceAfter' => 400],
        );

        $doc->addText(
            'Tahun Lulusan: ' . $tracerStudy->tahun_lulusan,
            ['size' => 13],
            ['alignment' => 'center', 'spaceAfter' => 60],
        );

        $doc->addText(
            'Tahun Pelaksanaan Tracer: ' . $tracerStudy->tahun_tracer,
            ['size' => 13],
            ['alignment' => 'center', 'spaceAfter' => 400],
        );

        $doc->addText(
            'Dibuat secara otomatis oleh Sistem Tracer Reporting',
            ['size' => 10, 'italic' => true, 'color' => '666666'],
            ['alignment' => 'center'],
        );
    }

    private function addSummarySection(DocxBuilder $doc, TracerStudy $tracerStudy): void
    {
        $doc->addTitle('Ringkasan', 1);

        $summaryData = [
            ['Nama Institusi', $tracerStudy->nama_institusi],
            ['Program Studi', $tracerStudy->nama_prodi],
            ['Jenjang', $tracerStudy->jenjang],
            ['Tahun Lulusan', (string) $tracerStudy->tahun_lulusan],
            ['Tahun Tracer', (string) $tracerStudy->tahun_tracer],
            ['Total Lulusan', number_format($tracerStudy->total_lulusan)],
            ['Total Responden', number_format($tracerStudy->total_responden)],
            ['Response Rate', number_format($tracerStudy->response_rate, 2) . '%'],
        ];

        $rows = [];
        foreach ($summaryData as $item) {
            $rows[] = [
                'cells' => [
                    ['text' => $item[0], 'bold' => true, 'size' => 11, 'bg' => self::COLOR_HEADER_BG],
                    ['text' => $item[1], 'size' => 11],
                ],
            ];
        }

        $doc->addTable($rows, [3000, 6000]);
    }

    private function addParameterSection(DocxBuilder $doc, $result): void
    {
        $doc->addTitle($result->order . '. ' . $result->parameter_name, 2);

        $doc->addText(
            $result->narrative,
            ['size' => 12],
            ['spaceAfter' => 120],
        );

        match ($result->statistic_type) {
            'frequency' => $this->addFrequencyTable($doc, $result->result_data),
            'mean_median' => $this->addMeanMedianTable($doc, $result->result_data),
            'mean' => $this->addMeanTable($doc, $result->result_data),
            'sum' => $this->addSumTable($doc, $result->result_data),
            'display' => $this->addDisplayList($doc, $result->result_data),
            'index' => $this->addIndexTable($doc, $result->result_data),
        };
    }

    private function addFrequencyTable(DocxBuilder $doc, array $data): void
    {
        $rows = [];

        $rows[] = [
            'cells' => [
                ['text' => 'Kategori', 'bold' => true, 'size' => 11, 'bg' => self::COLOR_HEADER_BG],
                ['text' => 'Jumlah', 'bold' => true, 'size' => 11, 'bg' => self::COLOR_HEADER_BG, 'align' => 'right'],
                ['text' => 'Persentase', 'bold' => true, 'size' => 11, 'bg' => self::COLOR_HEADER_BG, 'align' => 'right'],
            ],
        ];

        foreach ($data['frequencies'] as $freq) {
            $rows[] = [
                'cells' => [
                    ['text' => $freq['label'], 'size' => 11],
                    ['text' => (string) $freq['count'], 'size' => 11, 'align' => 'right'],
                    ['text' => number_format($freq['percentage'], 1) . '%', 'size' => 11, 'align' => 'right'],
                ],
            ];
        }

        $rows[] = [
            'cells' => [
                ['text' => 'Total', 'bold' => true, 'size' => 11, 'bg' => self::COLOR_FOOTER_BG],
                ['text' => (string) $data['total'], 'bold' => true, 'size' => 11, 'bg' => self::COLOR_FOOTER_BG, 'align' => 'right'],
                ['text' => '100%', 'bold' => true, 'size' => 11, 'bg' => self::COLOR_FOOTER_BG, 'align' => 'right'],
            ],
        ];

        $doc->addTable($rows, [4500, 2000, 2500]);
    }

    private function addMeanMedianTable(DocxBuilder $doc, array $data): void
    {
        $items = [
            ['Rata-rata (Mean)', 'Rp ' . number_format($data['mean'], 0, ',', '.')],
            ['Median', 'Rp ' . number_format($data['median'], 0, ',', '.')],
            ['Minimum', 'Rp ' . number_format($data['min'], 0, ',', '.')],
            ['Maksimum', 'Rp ' . number_format($data['max'], 0, ',', '.')],
            ['Jumlah Data', (string) $data['count']],
        ];

        $rows = [];
        foreach ($items as $item) {
            $rows[] = [
                'cells' => [
                    ['text' => $item[0], 'bold' => true, 'size' => 11, 'bg' => self::COLOR_HEADER_BG],
                    ['text' => $item[1], 'size' => 11, 'align' => 'right'],
                ],
            ];
        }

        $doc->addTable($rows, [4500, 4500]);
    }

    private function addMeanTable(DocxBuilder $doc, array $data): void
    {
        $items = [
            ['Rata-rata', $data['mean'] . ' bulan'],
            ['Jumlah Data', (string) $data['count']],
        ];

        $rows = [];
        foreach ($items as $item) {
            $rows[] = [
                'cells' => [
                    ['text' => $item[0], 'bold' => true, 'size' => 11, 'bg' => self::COLOR_HEADER_BG],
                    ['text' => $item[1], 'size' => 11, 'align' => 'right'],
                ],
            ];
        }

        $doc->addTable($rows, [4500, 4500]);
    }

    private function addSumTable(DocxBuilder $doc, array $data): void
    {
        $items = [
            ['Total', number_format($data['total_sum'])],
            ['Rata-rata', number_format($data['mean'], 1)],
            ['Jumlah Data', (string) $data['count']],
        ];

        $rows = [];
        foreach ($items as $item) {
            $rows[] = [
                'cells' => [
                    ['text' => $item[0], 'bold' => true, 'size' => 11, 'bg' => self::COLOR_HEADER_BG],
                    ['text' => $item[1], 'size' => 11, 'align' => 'right'],
                ],
            ];
        }

        $doc->addTable($rows, [4500, 4500]);
    }

    private function addDisplayList(DocxBuilder $doc, array $data): void
    {
        if (empty($data['values'])) {
            $doc->addText('Tidak ada data.', ['italic' => true, 'size' => 11]);
            return;
        }

        foreach ($data['values'] as $value) {
            $doc->addListItem($value, ['size' => 11]);
        }

        $doc->addText(
            'Total: ' . $data['count'] . ' data',
            ['italic' => true, 'size' => 10, 'color' => '666666'],
            ['spaceAfter' => 60],
        );
    }

    private function addIndexTable(DocxBuilder $doc, array $data): void
    {
        $rows = [];

        $rows[] = [
            'cells' => [
                ['text' => 'Kompetensi', 'bold' => true, 'size' => 11, 'bg' => self::COLOR_HEADER_BG],
                ['text' => 'Dikuasai', 'bold' => true, 'size' => 11, 'bg' => self::COLOR_HEADER_BG, 'align' => 'right'],
                ['text' => 'Dibutuhkan', 'bold' => true, 'size' => 11, 'bg' => self::COLOR_HEADER_BG, 'align' => 'right'],
                ['text' => 'Gap', 'bold' => true, 'size' => 11, 'bg' => self::COLOR_HEADER_BG, 'align' => 'right'],
            ],
        ];

        foreach ($data['competencies'] as $comp) {
            $rows[] = [
                'cells' => [
                    ['text' => $comp['name'], 'size' => 11],
                    ['text' => number_format($comp['index_a'], 2), 'size' => 11, 'align' => 'right'],
                    ['text' => number_format($comp['index_b'], 2), 'size' => 11, 'align' => 'right'],
                    [
                        'text' => number_format($comp['gap'], 2),
                        'size' => 11,
                        'align' => 'right',
                        'color' => $comp['gap'] < 0 ? 'CC0000' : '008800',
                    ],
                ],
            ];
        }

        $doc->addTable($rows, [3500, 1800, 1800, 1900]);
    }

    private function addFooter(DocxBuilder $doc): void
    {
        $doc->addTextBreak(2);
        $doc->addText(
            'Laporan ini dihasilkan secara otomatis oleh Sistem Web Tracer Reporting.',
            ['italic' => true, 'size' => 10, 'color' => '999999'],
            ['alignment' => 'center'],
        );
    }
}
