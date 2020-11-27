<?php

/**
 * * app/controllers/Pengaduan.php
 */
class Pengaduan extends Controller
{
    /**
     * * Mendefinisikan variable
     * @var object $my_model;
     */
    private $my_model;
    private $jalan_options;

    /**
     * * Pengaduan::index($param, $param2)
     * ? Method yang dapat diakses dari browser
     * @param param1
     * ? submethod
     * @param param2
     * ? id
     */
    public function index(string $param1 = null, int $param2 = null)
    {
        // TODO: Set model
        $this->my_model = $this->model('Pengaduan_model');
        $this->jalan_options = $this->model('Jalan_model')->getJalanOptions();

        // TODO: Select submethod
        switch ($param1) {
            case 'search': // ? Search Pengaduan yang masuk
                $this->PengaduanSearch();
                break;
            case 'submit': // ? Sumbit Form Pengaduan
                $this->PengaduanSubmit();
                break;
            case 'view':
                // TODO: Cek parameter id Pengaduan
                if (!isset($param2)) {
                    Header('Location: ' . BASE_URL . '/StaticPage/Error404');
                } // ! Id Gallery kosong, Redirect ke Error 404

                $this->PengaduanView($param2);
                break;
            case 'edit': // ? Menampilkan halaman Edit Pengaduan
                // TODO: Cek parameter id Pengaduan
                if (!isset($param2)) {
                    Header('Location: ' . BASE_URL . '/StaticPage/Error404');
                } // ! Id Gallery kosong, Redirect ke Error 404

                $this->PengaduanEdit($param2);
                break;
            case 'respon':
                $this->responSearch();
                break;
            default:
                // ? Menampilkan halaman Pengaduan
                // TODO: Cek session Admin & User
                if (isset($_SESSION['admin']) && isset($_SESSION['USER'])) {
                    // ? Session Admin & User exist
                    // TODO: Menampilkan halaman Pengaduan pada Session Admin & User
                    $this->PengaduanAdmin();
                } else {
                    // TODO: Menampilkan halaman Pengaduan pada Public
                    $this->PengaduanPublic();
                }
                break;
        }
    }

    /**
     * * Pengaduan::PengaduanPublic()
     * ? Menampilkan halaman Pengaduan tanpa login
     */
    private function PengaduanPublic()
    {
        Functions::setTitle('Pengaduan');
        $this->model('Otentifikasi_model')->clearToken();

        // TODO: Set form Element
        $data['form'] = $this->my_model->getPengaduanForm();
        //$data['foot'][] = $this->dofetch('Component/Button', ['button' => 'back']);
        $data['foot'][] = $this->dofetch('Component/Button', $this->btn_submit);

        // TODO: Menampilkan form
        $data['main'][] = $this->dofetch('Layout/Form', $data);

        // TODO: Menampilkan Template
        $this->view('Layout/Default', $data);
    }

    /**
     * * Pengaduan::PengaduanSubmit()
     * ? Submit Form Menu
     */
    private function PengaduanSubmit()
    {
        // TODO: Cek input id
        if ($_POST['id'] > 0) {
            // ? Id Pengaduan exist
            // TODO: Get Validasi form Pengaduan
            $error = $this->responValidate();
        } else {
            // TODO: Get Validasi form Pengaduan
            $error = $this->PengaduanValidate();
        }

        // TODO: Cek error
        if (!$error) {
            // ? No Error
            echo json_encode($this->PengaduanProcess());
        } else {
            // ! Error
            echo json_encode($error);
        }
        exit();
    }

    /**
     * * Pengaduan::PengaduanValidate()
     * ? Validasi form Menu
     */
    private function PengaduanValidate()
    {
        // TODO: Get form Pengaduan
        $form = $this->my_model->getPengaduanForm();

        foreach ($form as $idx => $row) {
            // TODO: Validasi form Menu
            $this->validate($_POST, $row, 'Pengaduan_model', 'pengaduan');
        }

        if (!empty($_POST['token'])) {
            if ($_POST['token'] == $_POST['my_token']) {
                $check_token = $this->model('Otentifikasi_model')->checkToken(
                    $_POST['token']
                );
                if (!$check_token) {
                    Functions::setDataSession('alert', [
                        '<strong>Token</strong> expired.',
                        'danger'
                    ]);
                }
            } else {
                Functions::setDataSession('alert', [
                    '<strong>Token</strong> tidak cocok',
                    'danger'
                ]);
            }
        }

        // TODO: Mengembalikan hasil validasi
        return Functions::getDataSession('alert');
    }

    private function responValidate()
    {
        $form = $this->my_model->getResponForm();

        foreach ($form as $row) {
            // TODO: Validasi form Menu
            $this->validate($_POST, $row, 'Pengaduan_model', 'pengaduan');
        }

        // TODO: Mengembalikan hasil validasi
        return Functions::getDataSession('alert');
    }

    /**
     * * Pengaduan::PengaduanProcess()
     * ? Proses form input
     */
    private function PengaduanProcess()
    {
        // TODO: Cek input id
        if ($_POST['id'] > 0) {
            // ? Id Pengaduan exist
            $form = $this->my_model->getResponForm();
            // TODO: Proses edit Pengaduan
            $result = $this->my_model->updatePengaduan();
            $id = $_POST['id'];
            $tag = 'Edit';
        } else {
            // ! Id Pengaduan not exist
            $form = $this->my_model->getPengaduanForm();
            // TODO: Proses add Pengaduan
            $result = $this->my_model->createPengaduan();
            $id = $this->my_model->insert_id();
            $tag = 'Add';
        }

        // TODO: Cek hasil proses
        if ($result) {
            // ? Process success
            if ($tag == 'Edit') {
                Functions::setDataSession('alert', [
                    "{$tag} Pengaduan Berhasil.",
                    'success'
                ]);
            } elseif ($tag == 'Add') {
                Functions::setDataSession('alert', [
                    'Pengaduan Anda Berhasil.',
                    'success'
                ]);
            }

            // TODO: Pindah foto dari temporary directory ke direktory Pengaduan
            foreach ($form as $row) {
                if ($row['type'] == 'img') {
                    if (!empty($_POST[$row['name']])) {
                        FileHandler::MoveFromTemp(
                            "img/pengaduan/{$id}",
                            $_POST[$row['name']]
                        );
                    }
                }
            }
        } else {
            // ! Process gagal
            if ($tag == 'Edit') {
                Functions::setDataSession('alert', [
                    "{$tag} Pengaduan Gagal.",
                    'danger'
                ]);
            } elseif ($tag == 'Add') {
                Functions::setDataSession('alert', [
                    'Pengaduan Anda Gagal.',
                    'danger'
                ]);
            }
        }

        return Functions::getDataSession('alert');
    }

    /**
     * * Pengaduan::PengduanAdmin()
     * ? Menampilkan halaman Pengaduan saat session Admin
     */
    private function PengaduanAdmin()
    {
        Functions::setTitle('Pengaduan');

        $data = [
            'main' => [
                $this->dofetch('Layout/Table', [
                    'data' => Functions::defaultTableData(),
                    'thead' => $this->my_model->getPengaduanThead(),
                    'url' => BASE_URL . '/Pengaduan/index/search'
                ])
            ],
            'modal' => [
                [
                    'modalId' => 'myModal'
                ]
            ]
        ];

        // TODO: Menampilkan Template
        $this->view('Layout/Default', $data);
    }

    /**
     * * Pengaduan::PengaduanSearch()
     * ? Mencari Pengaduan yang masuk
     */
    private function PengaduanSearch()
    {
        // TODO: Get listing Pengaduan
        [$list, $count] = $this->my_model->getPengaduan();
        // TODO: Get total Pengaduan
        $total = $this->my_model->totalPengaduan();

        // TODO: Modify listing Pengaduan
        $rows = [];
        foreach ($list as $idx => $row) {
            $row['tanggal'] = Functions::formatDatetime(
                $row['insert_dt'],
                'd/m/Y H:i'
            );
            $row['nama_jalan'] = $this->jalan_options[$row['no_jalan']];
            $row['koordinat'] = $row['latitude']
                ? "{$row['latitude']}, {$row['longitude']}"
                : '-';
            $row['foto1'] = !empty($row['foto1'])
                ? UPLOAD_URL . "img/pengaduan/{$row['id']}/{$row['foto1']}"
                : '';
            $row['foto2'] = !empty($row['foto2'])
                ? UPLOAD_URL . "img/pengaduan/{$row['id']}/{$row['foto2']}"
                : '';
            $row['foto3'] = !empty($row['foto3'])
                ? UPLOAD_URL . "img/pengaduan/{$row['id']}/{$row['foto3']}"
                : '';
            $row['view'] = $this->PengaduanView($row['id']);
            $row['row'] = Functions::getSearch()['offset'] + $idx + 1;
            array_push($rows, $row);
        }

        // TODO: Mengembalikan result
        Functions::setDataTable($rows, $count, $total);
        exit();
    }

    private function PengaduanView(int $id)
    {
        // TODO: Get Gallery dari Database
        [$detail, $count] = $this->PengaduanDetail($id);
        $detail['nama_jalan'] = $this->jalan_options[$detail['no_jalan']];
        $detail['koordinat'] = !empty($detail['latitude'])
            ? "{$detail['latitude']}, {$detail['longitude']}"
            : '-';

        // TODO: Set detail Gallery
        $data['detail'] = $detail;

        // TODO: Cek Gallery exist
        if ($count <= 0) {
            Header('Location: ' . BASE_URL . '/StaticPage/Error404');
        }

        // TODO: Get form Gallery
        $form = $this->my_model->getPengaduanViewForm();

        // TODO: Set Form Element
        $data['form'] = $form;

        // TODO: Menampilkan Form
        return $this->dofetch('Layout/Form', $data);
    }

    private function PengaduanDetail(int $id)
    {
        return $this->my_model->getPengaduanDetail($id);
    }

    private function PengaduanEdit($id)
    {
        Functions::setTitle('Edit Menu');

        // TODO: Get Menu dari Database
        [$detail, $count] = $this->PengaduanDetail($id);

        // TODO: Set detail Menu
        $data['detail'] = $detail;

        // TODO: Cek Menu exist
        if ($count <= 0) {
            Header('Location: ' . BASE_URL . '/StaticPage/Error404');
        }

        $data['form'] = $this->my_model->getResponForm();
        $this->form($data);
    }

    public function aturan()
    {
        Functions::setTitle('Prosedur Pengaduan');

        $this->view('Pengaduan/Aturan');
    }

    public function lapor()
    {
        Header('Location: ' . BASE_URL . '/Pengaduan');
    }

    public function tindaklanjut()
    {
        Functions::setTitle('Tindak Lanjut');

        $data = [
            'main' => [
                $this->dofetch('Layout/Table', [
                    'data' => Functions::defaultTableData(),
                    'thead' => $this->model(
                        'Pengaduan_model'
                    )->getResponThead(),
                    'url' => BASE_URL . '/Pengaduan/index/respon'
                ])
            ],
            'modal' => [
                [
                    'modalId' => 'myModal'
                ]
            ]
        ];

        // TODO: Menampilkan Template
        $this->view('Layout/Default', $data);
    }

    public function responSearch()
    {
        // TODO: Get listing Pengaduan
        [$list, $count] = $this->my_model->getPengaduan();
        // TODO: Get total Pengaduan
        $total = $this->my_model->totalPengaduan();

        // TODO: Modify listing Pengaduan
        $rows = [];
        foreach ($list as $idx => $row) {
            $newRow['tanggal'] = Functions::formatDatetime(
                $row['insert_dt'],
                'd/m/Y H:i'
            );
            $newRow['nama'] = $row['nama'];
            $newRow['alamat'] = $row['alamat'];
            $newRow['telepon'] = substr_replace($row['telepon'], 'xxx', -3, 3);
            $newRow['keterangan'] = $row['keterangan'];
            $newRow['respon'] = $row['respon'];
            $newRow['view'] = $this->responView($row['id']);
            $newRow['row'] = Functions::getSearch()['offset'] + $idx + 1;
            array_push($rows, $newRow);
        }

        // TODO: Mengembalikan result
        Functions::setDataTable($rows, $count, $total);
        exit();
    }

    public function responView(int $id)
    {
        // TODO: Get Gallery dari Database
        [$detail, $count] = $this->PengaduanDetail($id);
        $detail['nama_jalan'] = $this->jalan_options[$detail['no_jalan']];
        $detail['koordinat'] = !empty($detail['latitude'])
            ? "{$detail['latitude']}, {$detail['longitude']}"
            : '-';

        // TODO: Set detail Gallery
        $data['detail'] = $detail;

        // TODO: Cek Gallery exist
        if ($count <= 0) {
            Header('Location: ' . BASE_URL . '/StaticPage/Error404');
        }

        // TODO: Get form Gallery
        $form = $this->my_model->getResponViewForm();

        // TODO: Set Form Element
        $data['form'] = $form;

        // TODO: Menampilkan Form
        return $this->dofetch('Layout/Form', $data);
    }
}
