<?php

return [

    'excel' => [
        'sheet_name' => 'data tracer',
        'header_row' => 3,
        'data_start_row' => 4,
    ],

    'parameters' => [

        'status_setelah_lulus' => [
            'order' => 1,
            'name' => 'Status Setelah Lulus',
            'column' => 'M',
            'f_code' => 'f8',
            'statistic_type' => 'frequency',
            'chart_type' => 'pie',
            'categories' => [
                1 => 'Bekerja',
                2 => 'Tidak Bekerja',
                3 => 'Wirausaha',
                4 => 'Studi Lanjut',
                5 => 'Mencari Kerja',
            ],
            'group' => 'status_alumni',
        ],

        'lama_mendapatkan_pekerjaan' => [
            'order' => 2,
            'name' => 'Lama Mendapatkan Pekerjaan',
            'column' => 'BX',
            'f_code' => 'f502',
            'statistic_type' => 'frequency',
            'chart_type' => 'bar',
            'categories' => [
                1 => '< 6 bulan',
                2 => '6–12 bulan',
                3 => '> 12 bulan',
            ],
            'group' => 'bekerja',
        ],

        'tingkat_tempat_kerja' => [
            'order' => 3,
            'name' => 'Tingkat Tempat Kerja',
            'column' => 'BI',
            'f_code' => 'f5d',
            'statistic_type' => 'frequency',
            'chart_type' => 'bar',
            'categories' => [
                1 => 'Lokal',
                2 => 'Nasional',
                3 => 'Multinasional',
            ],
            'group' => 'bekerja',
        ],

        'bidang_kerja_lulusan' => [
            'order' => 4,
            'name' => 'Bidang Kerja Lulusan',
            'column' => 'T',
            'f_code' => 'f1101',
            'statistic_type' => 'frequency',
            'chart_type' => 'bar',
            'categories' => [
                1 => 'Pemerintah',
                2 => 'Non-profit',
                3 => 'Swasta',
                4 => 'Wiraswasta',
                5 => 'Lainnya',
                6 => 'BUMN',
                7 => 'Multilateral',
            ],
            'group' => 'bekerja',
        ],

        'kesesuaian_bidang_studi' => [
            'order' => 5,
            'name' => 'Kesesuaian Bidang Studi',
            'column' => 'BC',
            'f_code' => 'f14',
            'statistic_type' => 'frequency',
            'chart_type' => 'bar',
            'categories' => [
                1 => 'Sangat Erat',
                2 => 'Erat',
                3 => 'Cukup Erat',
                4 => 'Kurang Erat',
                5 => 'Tidak Sama Sekali',
            ],
            'group' => 'bekerja',
        ],

        'tingkat_pendidikan_pekerjaan' => [
            'order' => 6,
            'name' => 'Tingkat Pendidikan pada Pekerjaan',
            'column' => 'AM',
            'f_code' => 'f15',
            'statistic_type' => 'frequency',
            'chart_type' => 'bar',
            'categories' => [
                1 => 'Lebih Tinggi',
                2 => 'Sama',
                3 => 'Lebih Rendah',
                4 => 'Tidak Perlu PT',
            ],
            'group' => 'bekerja',
        ],

        'pendapatan_pertama' => [
            'order' => 7,
            'name' => 'Pendapatan Pertama Lulusan',
            'column' => 'BP',
            'f_code' => 'f505',
            'statistic_type' => 'mean_median',
            'chart_type' => null,
            'categories' => null,
            'group' => 'bekerja',
        ],

        'penghasilan_lulusan' => [
            'order' => 8,
            'name' => 'Penghasilan Lulusan',
            'column' => 'BP',
            'f_code' => 'f505',
            'statistic_type' => 'frequency',
            'chart_type' => 'bar',
            'categories' => [
                1 => '< 1 juta',
                2 => '1–3 juta',
                3 => '3–5 juta',
                4 => '> 5 juta',
            ],
            'category_ranges' => [
                1 => [0, 999999],
                2 => [1000000, 3000000],
                3 => [3000001, 5000000],
                4 => [5000001, null],
            ],
            'group' => 'bekerja',
        ],

        'mulai_mencari_pekerjaan' => [
            'order' => 9,
            'name' => 'Mulai Mencari Pekerjaan',
            'column' => 'AY',
            'f_code' => 'f301',
            'statistic_type' => 'mean',
            'chart_type' => null,
            'categories' => null,
            'group' => 'mencari_kerja',
        ],

        'melamar_pekerjaan' => [
            'order' => 10,
            'name' => 'Melamar Pekerjaan',
            'column' => 'CG',
            'f_code' => 'f6',
            'statistic_type' => 'sum',
            'chart_type' => null,
            'categories' => null,
            'group' => 'mencari_kerja',
        ],

        'diundang_wawancara' => [
            'order' => 11,
            'name' => 'Diundang Wawancara',
            'column' => 'BD',
            'f_code' => 'f7a',
            'statistic_type' => 'sum',
            'chart_type' => null,
            'categories' => null,
            'group' => 'mencari_kerja',
        ],

        'tempat_pendidikan_lanjut' => [
            'order' => 12,
            'name' => 'Tempat Melanjutkan Pendidikan',
            'column' => 'AK',
            'f_code' => 'f18c',
            'statistic_type' => 'display',
            'chart_type' => null,
            'categories' => null,
            'group' => 'studi_lanjut',
        ],

        'kemampuan_diri' => [
            'order' => 13,
            'name' => 'Penilaian Kemampuan Diri',
            'f_code' => 'f1761-f1774',
            'statistic_type' => 'index',
            'chart_type' => 'grouped_bar',
            'competencies' => [
                'etika' => [
                    'name' => 'Etika',
                    'column_a' => 'f1761',
                    'column_b' => 'f1762',
                ],
                'keahlian_bidang' => [
                    'name' => 'Keahlian Berdasarkan Bidang Ilmu',
                    'column_a' => 'f1763',
                    'column_b' => 'f1764',
                ],
                'bahasa_inggris' => [
                    'name' => 'Kemampuan Bahasa Inggris',
                    'column_a' => 'f1765',
                    'column_b' => 'f1766',
                ],
                'teknologi_informasi' => [
                    'name' => 'Penggunaan Teknologi Informasi',
                    'column_a' => 'f1767',
                    'column_b' => 'f1768',
                ],
                'komunikasi' => [
                    'name' => 'Kemampuan Berkomunikasi',
                    'column_a' => 'f1769',
                    'column_b' => 'f1770',
                ],
                'kerjasama' => [
                    'name' => 'Kemampuan Kerjasama Tim',
                    'column_a' => 'f1771',
                    'column_b' => 'f1772',
                ],
                'pengembangan_diri' => [
                    'name' => 'Kemampuan Pengembangan Diri',
                    'column_a' => 'f1773',
                    'column_b' => 'f1774',
                ],
            ],
            'likert_scale' => [1, 2, 3, 4, 5],
            'group' => 'kompetensi',
        ],

    ],

];
