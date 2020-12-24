<?php

/**
 * * app/model/Data_model.php
 */
class Data_model extends Database
{
    public function getAllDataJalan()
    {
        $jalan_model = $this->model('Jalan_model');
        $jalan_table = $jalan_model->getTable('jalan');
        $koordinat_table = $jalan_model->getTable('koordinat');
        $detail_table = $jalan_model->getTable('detail');
        $foto_table = $jalan_model->getTable('foto');
        $panjang_table = $jalan_model->getTable('panjang');
        $jembatan_table = 'tjembatan';

        $data = [];

        $plain = [
            'select' => [
                "{$jalan_table}.no_jalan",
                "{$jalan_table}.nama_jalan",
                "{$jalan_table}.kepemilikan",
                "{$jalan_table}.panjang",
                "{$jalan_table}.lebar_rata",
                "{$koordinat_table}.ori",
                "{$koordinat_table}.segmented",
                "{$panjang_table}.perkerasan as perkerasan_panjang",
                "{$panjang_table}.kondisi as kondisi_panjang"
            ],
            'join' => [
                "LEFT JOIN {$koordinat_table} ON {$koordinat_table}.no_jalan = {$jalan_table}.no_jalan",
                "LEFT JOIN {$panjang_table} ON {$panjang_table}.no_jalan = {$jalan_table}.no_jalan"
            ],
            'sort' => [
                "{$jalan_table}.kepemilikan ASC",
                "{$jalan_table}.no_jalan ASC"
            ],
            'filter' => ["{$jalan_table}.nama_jalan NOT LIKE '%test%'"]
        ];

        $query = $this->getSelectQuery(
            $jalan_table,
            Functions::getParams($plain)
        );
        $this->execute($query);
        [$data['jalan']] = $this->multiarray();

        $detail = [
            'select' => [
                "{$jalan_table}.no_jalan",
                "{$jalan_table}.nama_jalan",
                "{$jalan_table}.kepemilikan",
                "FORMAT({$jalan_table}.panjang/1000, 2) as panjang",
                "{$jalan_table}.lebar_rata",
                "{$jalan_table}.segmentasi",
                "{$panjang_table}.perkerasan as perkerasan_panjang",
                "{$panjang_table}.kondisi as kondisi_panjang",
                "{$detail_table}.no_detail",
                "{$detail_table}.latitude",
                "{$detail_table}.longitude",
                "{$detail_table}.perkerasan",
                "{$detail_table}.kondisi",
                "{$detail_table}.segment",
                "{$detail_table}.koordinat",
                "{$detail_table}.data",
                "{$koordinat_table}.jml_segmented"
            ],
            'join' => [
                "LEFT JOIN {$jalan_table} ON {$jalan_table}.no_jalan = {$detail_table}.no_jalan",
                "LEFT JOIN {$koordinat_table} ON {$koordinat_table}.no_jalan = {$detail_table}.no_jalan",
                "LEFT JOIN {$panjang_table} ON {$panjang_table}.no_jalan = {$jalan_table}.no_jalan"
                // "LEFT JOIN {$foto_table} ON ({$foto_table}.latitude = {$detail_table}.latitude AND {$foto_table}.longitude = {$detail_table}.longitude)"
            ],
            'sort' => [
                "{$detail_table}.no_jalan ASC",
                // "{$detail_table}.no_detail ASC",
                // "{$detail_table}.perkerasan ASC",
                // "{$detail_table}.kondisi ASC",
                "{$detail_table}.segment ASC"
            ],
            'filter' => ["{$jalan_table}.nama_jalan NOT LIKE '%test%'"]
        ];
        $query = $this->getSelectQuery(
            $detail_table,
            Functions::getParams($detail)
        );
        $this->execute($query);
        [$data['detail']] = $this->multiarray();

        $jembatan = [
            'select' => [
                "{$jalan_table}.no_jalan",
                "{$jalan_table}.nama_jalan",
                "{$jalan_table}.kepemilikan",
                "{$jalan_table}.panjang",
                "{$jalan_table}.lebar_rata",
                "{$panjang_table}.perkerasan as perkerasan_panjang",
                "{$panjang_table}.kondisi as kondisi_panjang",
                "{$jembatan_table}.id",
                "{$jembatan_table}.no_jembatan",
                "{$jembatan_table}.nama_jembatan",
                "{$jembatan_table}.latitude",
                "{$jembatan_table}.longitude",
                "{$jembatan_table}.lebar",
                "{$jembatan_table}.panjang",
                "{$jembatan_table}.bentang",
                "{$jembatan_table}.bms",
                "{$jembatan_table}.survei",
                "{$jembatan_table}.keterangan",
                "{$jembatan_table}.tipe_bangunan_atas",
                "{$jembatan_table}.tipe_bangunan_bawah",
                "{$jembatan_table}.tipe_fondasi",
                "{$jembatan_table}.tipe_lantai",
                "{$jembatan_table}.kondisi_bangunan_atas",
                "{$jembatan_table}.kondisi_bangunan_bawah",
                "{$jembatan_table}.kondisi_fondasi",
                "{$jembatan_table}.kondisi_lantai",
                "{$jembatan_table}.foto_bangunan_atas",
                "{$jembatan_table}.foto_bangunan_bawah",
                "{$jembatan_table}.foto_fondasi",
                "{$jembatan_table}.foto_lantai",
                "{$jembatan_table}.bms_bangunan_atas",
                "{$jembatan_table}.bms_bangunan_bawah",
                "{$jembatan_table}.bms_fondasi",
                "{$jembatan_table}.bms_lantai",
                "{$jembatan_table}.keterangan_bangunan_atas",
                "{$jembatan_table}.keterangan_bangunan_bawah",
                "{$jembatan_table}.keterangan_fondasi",
                "{$jembatan_table}.keterangan_lantai"
            ],
            'join' => [
                "LEFT JOIN {$jalan_table} ON {$jalan_table}.no_jalan = {$jembatan_table}.no_jalan",
                "LEFT JOIN {$panjang_table} ON {$panjang_table}.no_jalan = {$jalan_table}.no_jalan"
            ],
            'sort' => [
                "{$jembatan_table}.no_jalan ASC",
                "{$jembatan_table}.no_jembatan ASC"
            ],
            'filter' => ["{$jalan_table}.nama_jalan NOT LIKE '%test%'"]
        ];
        $query = $this->getSelectQuery(
            $jembatan_table,
            Functions::getParams($jembatan)
        );
        $this->execute($query);
        [$data['jembatan']] = $this->multiarray();

        return $data;
    }

    /**
     * * Start Generate Data Jalan
     */
    public function generateData()
    {
        // TODO: Remove old data
        array_map('unlink', glob(DOC_ROOT . '/data/*.json'));

        // * Setup from database
        $cond[] = 'jenis = 1'; // ? Setup for jalan
        // TODO: Get setup from database
        [$setup_jalan] = $this->model('Setup_model')->getSetup($cond);

        // TODO: Formating setup as JSON
        [$style, $lineStyle, $iconStyle] = Functions::getStyle($setup_jalan);

        // TODO: Get All Jalan Data
        $list = $this->getAllDataJalan();

        $jalan = Functions::getLineFromJalan($list['jalan'], $lineStyle);

        $jembatan = Functions::getPointFromJembatan(
            $list['jembatan'],
            $iconStyle
        );

        $jembatanCount = [];
        foreach ($jembatan as $row) {
            $jembatanCount[$row['no_jalan']] += 1;
        }

        list(
            $segment,
            $complete,
            $perkerasan,
            $kondisi,
            $awal,
            $akhir
        ) = Functions::getLineFromDetail(
            $list['detail'],
            $lineStyle,
            $iconStyle
        );

        $awalOpt = [];
        foreach ($awal as $row) {
            $awalOpt[$row['no_jalan']] = $row['koordinat'];
        }

        $akhirOpt = [];
        foreach ($akhir as $row) {
            $akhirOpt[$row['no_jalan']] = $row['koordinat'];
        }

        foreach ($jalan as $idx => $row) {
            // var_dump($jembatanCount[$row['no_jalan']]);
            $perkerasanPanjang = array_map(function ($p) {
                return number_format($p / 1000, 2);
            }, json_decode($row['perkerasan_panjang'], true));

            $row = array_merge($row, [
                'koordinat_awal' => $awalOpt[$row['no_jalan']],
                'koordinat_akhir' => $akhirOpt[$row['no_jalan']],
                'jml_jembatan' => $jembatanCount[$row['no_jalan']],
                'panjang' => array_sum($perkerasanPanjang)
            ]);
            $jalan[$idx] = $row;
        }

        $kondisi_opt = $this->options('kondisi_opt');

        foreach ($jembatan as $idx => $row) {
            $row = array_merge($row, [
                'koordinat_awal' => $awalOpt[$row['no_jalan']],
                'koordinat_akhir' => $akhirOpt[$row['no_jalan']],
                'jml_jembatan' => $jembatanCount[$row['no_jalan']],
                'kondisi_bangunan_atas' =>
                    $kondisi_opt[$row['kondisi_bangunan_atas']],
                'kondisi_bangunan_bawah' =>
                    $kondisi_opt[$row['kondisi_bangunan_bawah']],
                'kondisi_fondasi' => $kondisi_opt[$row['kondisi_fondasi']],
                'kondisi_lantai' => $kondisi_opt[$row['kondisi_lantai']]
            ]);
            $jembatan[$idx] = $row;
        }

        foreach ($segment as $idx => $row) {
            $row = array_merge($row, [
                'koordinat_awal' => $awalOpt[$row['no_jalan']],
                'koordinat_akhir' => $akhirOpt[$row['no_jalan']],
                'jml_jembatan' => $jembatanCount[$row['no_jalan']]
            ]);
            $segment[$idx] = $row;
        }

        foreach ($complete as $idx => $row) {
            $row = array_merge($row, [
                'koordinat_awal' => $awalOpt[$row['no_jalan']],
                'koordinat_akhir' => $akhirOpt[$row['no_jalan']],
                'jml_jembatan' => $jembatanCount[$row['no_jalan']]
            ]);
            $complete[$idx] = $row;
        }

        foreach ($perkerasan as $idx => $row) {
            $row = array_merge($row, [
                'koordinat_awal' => $awalOpt[$row['no_jalan']],
                'koordinat_akhir' => $akhirOpt[$row['no_jalan']],
                'jml_jembatan' => $jembatanCount[$row['no_jalan']]
            ]);
            $perkerasan[$idx] = $row;
        }

        foreach ($kondisi as $idx => $row) {
            $row = array_merge($row, [
                'koordinat_awal' => $awalOpt[$row['no_jalan']],
                'koordinat_akhir' => $akhirOpt[$row['no_jalan']],
                'jml_jembatan' => $jembatanCount[$row['no_jalan']]
            ]);
            $kondisi[$idx] = $row;
        }

        foreach ($awal as $idx => $row) {
            $row = array_merge($row, [
                'koordinat_awal' => $awalOpt[$row['no_jalan']],
                'koordinat_akhir' => $akhirOpt[$row['no_jalan']],
                'jml_jembatan' => $jembatanCount[$row['no_jalan']]
            ]);
            $awal[$idx] = $row;
        }

        foreach ($akhir as $idx => $row) {
            $row = array_merge($row, [
                'koordinat_awal' => $awalOpt[$row['no_jalan']],
                'koordinat_akhir' => $akhirOpt[$row['no_jalan']],
                'jml_jembatan' => $jembatanCount[$row['no_jalan']]
            ]);
            $akhir[$idx] = $row;
        }

        $dd1 = $this->generateDataLaporanDd1($list['jalan']);
        $dd2 = $this->generateDataLaporanDd2($list['jembatan']);

        $info = $this->generateDataInfo($list);

        $data = [
            'lines' => [
                ['Jalan', $jalan],
                ['Complete', $complete],
                ['Perkerasan', $perkerasan],
                ['Kondisi', $kondisi]
            ],
            'points' => [
                ['Segment', $segment],
                ['Awal', $awal],
                ['Akhir', $akhir],
                ['Jembatan', $jembatan]
            ],
            'other' => [
                ['LaporanDD1', $dd1],
                ['LaporanDD2', $dd2],
                ['Info', $info]
            ]
        ];
        $this->generateDataFile($data, $style);
    }

    public function generateDataLaporanDd1($jalan)
    {
        $laporan = [];
        $field = [];
        foreach ($this->model('Laporan_model')->getDd1Thead()[3] as $row) {
            $row['field'] =
                $row['field'] == 'perkerasan_1'
                    ? 'perkerasan_2'
                    : ($row['field'] == 'perkerasan_2'
                        ? 'perkerasan_1'
                        : $row['field']);
            if (!empty($row['field'])) {
                $field[] = $row['field'];
            }
        }

        foreach ($jalan as $idx => $row) {
            $row['row'] = $idx + 1;
            // $row['panjang_km'] = number_format($row['panjang'] / 1000, 2);

            $perkerasanPanjang = json_decode($row['perkerasan_panjang'], true);

            $perkerasanPanjang = array_map(function ($p) {
                return number_format($p / 1000, 2);
            }, $perkerasanPanjang);

            foreach ($perkerasanPanjang as $key => $value) {
                // $row["perkerasan_{$key}"] = number_format($value / 1000, 2);
                $row["perkerasan_{$key}"] = $value;
            }

            $panjang = array_sum($perkerasanPanjang);

            $kondisiPanjang = json_decode($row['kondisi_panjang'], true);
            $kondisiPanjang = array_map(function ($p) {
                return number_format($p / 1000, 2);
            }, $kondisiPanjang);

            foreach ($kondisiPanjang as $key => $value) {
                // $row["kondisi_{$key}"] = number_format($value / 1000, 2);
                $row["kondisi_{$key}"] = $value;
                $row["kondisi_{$key}_percent"] = number_format(
                    // ($value / $row['panjang']) * 100,
                    ((float) $value / (float) $panjang) * 100,
                    2
                );
            }

            $row['panjang_km'] = $panjang;

            $laporan[$idx]['kepemilikan'] = $row['kepemilikan'];
            foreach ($field as $value) {
                $laporan[$idx][$value] = $row[$value];
            }
        }

        return $laporan;
    }

    public function generateDataLaporanDd2($jembatan)
    {
        $kondisi_opt = $this->options('kondisi_opt');

        $laporan = [];
        $field = [];
        foreach ($this->model('Laporan_model')->getDd2Thead()[3] as $row) {
            if (!empty($row['field'])) {
                $field[] = $row['field'];
            }
        }

        foreach ($jembatan as $idx => $row) {
            $row['row'] = $idx + 1;
            $laporan[$idx]['kepemilikan'] = $row['kepemilikan'];

            foreach ($row as $key => $value) {
                if (strpos($value, 'kondisi')) {
                    $value = $kondisi_opt[$value];
                }
                $row[$key] = !empty($value) ? $value : null;
            }

            foreach ($field as $value) {
                $laporan[$idx][$value] = $row[$value];
            }
        }

        return $laporan;
    }

    public function generateDataInfo($list)
    {
        $kepemilikan_opt = $this->options('kepemilikan_opt'); // TODO: Get Kepemilikan Options

        $panjang = []; // ? in metres
        foreach ($list['jalan'] as $row) {
            $kepemilikan = 'JalanSemua';
            $panjang[$kepemilikan]['total'] += $row['panjang'];
            foreach (json_decode($row['perkerasan'], true) as $key => $value) {
                $panjang[$kepemilikan]['perkerasan'][$key] += $value;
            }
            foreach (json_decode($row['kondisi'], true) as $key => $value) {
                $panjang[$kepemilikan]['kondisi'][$key] += $value;
            }

            $kepemilikan = preg_replace(
                '/[^A-Za-z0-9]/',
                '',
                $kepemilikan_opt[$row['kepemilikan']]
            );
            $panjang[$kepemilikan]['total'] += $row['panjang'];
            foreach (json_decode($row['perkerasan'], true) as $key => $value) {
                $panjang[$kepemilikan]['perkerasan'][$key] += $value;
            }
            foreach (json_decode($row['kondisi'], true) as $key => $value) {
                $panjang[$kepemilikan]['kondisi'][$key] += $value;
            }
        }

        $jembatan = [];
        foreach ($list['jembatan'] as $row) {
            $kepemilikan = 'JalanSemua';
            $jembatan[$kepemilikan]['total'] += 1;

            $kepemilikan = preg_replace(
                '/[^A-Za-z0-9]/',
                '',
                $kepemilikan_opt[$row['kepemilikan']]
            );
            $jembatan[$kepemilikan]['total'] += 1;
        }

        $info['panjang'] = $panjang;
        $info['jembatan'] = $jembatan;
        return $info;
    }

    public function generateDataFile($data, $style)
    {
        foreach ($data as $key => $values) {
            foreach ($values as $row) {
                [$filename, $value] = $row;
                switch ($key) {
                    case 'lines':
                        if (!empty($value)) {
                            Functions::saveGeoJSON(
                                "{$filename}.json",
                                $style,
                                $value,
                                1
                            );
                        } else {
                            Functions::saveJSON("{$filename}.json", $value);
                        }
                        break;
                    case 'points':
                        if (!empty($value)) {
                            Functions::saveGeoJSON(
                                "{$filename}.json",
                                $style,
                                $value,
                                2
                            );
                        } else {
                            Functions::saveJSON("{$filename}.json", $value);
                        }
                        break;
                    default:
                        Functions::saveJSON("{$filename}.json", $value);
                        break;
                }
            }
        }
    }
}
