<?php

/**
 * * app/controllers/Jembatan.php
 */
class Jembatan extends Controller
{
    /**
     * * Mendefinisikan variable
     */
    private $my_model;

    /**
     * * Jembatan::index($param, $param2)
     * @param string param1
     * ? submethod
     * @param string param2
     * ? id
     */
    public function index($param1 = null, $param2 = null)
    {
        // TODO: Set model
        $this->my_model = $this->model('Jembatan_model');
        $this->jalan_options = $this->model('Jalan_model')->getJalanOptions();

        // TODO: Select submethod
        switch ($param1) {
            case 'search': // ? Search Jembatan
                $this->JembatanSearch();
                break;
            case 'add': // ? Menampilkan halaman Add Jembatan
                $this->JembatanAdd();
                break;
            case 'edit': // ? Menampilkan halaman Edit Jembatan
                // TODO: Cek parameter id Jembatan
                if (!isset($param2)) {
                    Header('Location: ' . BASE_URL . '/StaticPage/Error404');
                } // ! Id Gallery kosong, Redirect ke Error 404

                $this->JembatanEdit($param2);
                break;
            case 'submit': // ? Sumbit Form Jembatan
                $this->JembatanSubmit();
                break;
            case 'remove': // ? Menghapus Jembatan
                $this->JembatanRemove($_POST['id']);
                break;
            default:
                // ? Menampilkan halaman Jembatan
                $this->JembatanDefault();
                break;
        }
    }

    /**
     * * Jembatan::JembatanDefault()
     * ? Menampilkan halaman Jembatan
     */
    private function JembatanDefault()
    {
        Functions::setTitle('Jembatan');

        $data = [
            'toolbar' => [$this->dofetch('Component/Button', $this->btn_add)],
            'main' => [
                $this->dofetch('Layout/Table', [
                    'data' => Functions::defaultTableData(),
                    'thead' => $this->my_model->getJembatanThead(),
                    'url' => BASE_URL . '/Jembatan/index/search'
                ])
            ]
        ];

        // TODO: Menampilkan Template
        $this->view('Layout/Default', $data);
    }

    /**
     * * Jembatan::JembatanSearch()
     * ? Mencari Jembatan dari Database
     */
    private function JembatanSearch()
    {
        // TODO: Get listing Jembatan
        list($list, $count) = $this->my_model->getJembatan();
        // TODO: Get total Jembatan
        $total = $this->my_model->totalJembatan();

        // TODO: Modify listing Jembatan
        $rows = [];
        foreach ($list as $idx => $row) {
            $row['jalan'] = $this->jalan_options[$row['no_jalan']];
            $row['row'] = Functions::getSearch()['offset'] + $idx + 1;
            array_push($rows, $row);
        }

        // TODO: Mengembalikan result
        Functions::setDataTable($rows, $count, $total);
        exit();
    }

    /**
     * * Jembatan::JembatanAdd()
     * ? Menampilkan Halaman Add Jembatan
     */
    private function JembatanAdd()
    {
        Functions::setTitle('Add Jembatan');

        // TODO: Set form element
        $data['form'] = $this->my_model->getJembatanForm();

        $this->form($data);
    }

    /**
     * * Jembatan::JembatanEdit($id)
     * @param id
     * ? id Jembatan
     */
    private function JembatanEdit($id)
    {
        Functions::setTitle('Edit Jembatan');

        // TODO: Get Jembatan dari Database
        list($detail, $count) = $this->JembatanDetail($id);

        // TODO: Cek Jembatan exist
        if ($count <= 0) {
            Header('Location: ' . BASE_URL . '/StaticPage/Error404');
        }

        // TODO: Set detail Jembatan
        $data['detail'] = $detail;

        // TODO: Set form element
        $data['form'] = $this->my_model->getJembatanForm();

        $this->form($data);
    }

    /**
     * * Jembatan::JembatanSubmit()
     * ? Submit Form Jembatan
     */
    private function JembatanSubmit()
    {
        // TODO: Get Validasi form Jembatan
        $error = $this->JembatanValidate();

        // TODO: Cek error
        if (!$error) {
            // ? No Error
            echo json_encode($this->JembatanProcess());
        } else {
            // ! Error
            echo json_encode($error);
        }
        exit();
    }

    /**
     * * Jembatan::JembatanValidate()
     * ? Validasi form Jembatan
     */
    private function JembatanValidate()
    {
        // TODO: Get form Jembatan
        $form = $this->my_model->getJembatanForm();

        foreach ($form as $row) {
            // TODO: Validasi form Jembatan
            $this->validate($_POST, $row, 'Jembatan_model', 'jembatan');
        }

        // TODO: Mengembalikan hasil validasi
        return Functions::getDataSession('alert');
    }

    /**
     * * Jembatan::JembatanProcess()
     * ? Proses form input
     */
    private function JembatanProcess()
    {
        $form = $this->my_model->getJembatanForm();

        // TODO: Cek input id
        if ($_POST['id'] > 0) {
            // ? Id Jembatan exist
            // TODO: Proses edit Jembatan
            $result = $this->my_model->updateJembatan();
            $id = $_POST['id'];
            $tag = 'Edit';
        } else {
            // ! Id Jembatan not exist
            // TODO: Proses add Jembatan
            $result = $this->my_model->createJembatan();
            $id = $this->my_model->insert_id();
            $tag = 'Add';
        }

        // TODO: Cek hasil proses
        if ($result) {
            // ? Proses success
            Functions::setDataSession('alert', [
                "{$tag} Jembatan success.",
                'success'
            ]);
            foreach ($form as $row) {
                switch ($row['type']) {
                    case 'pdf':
                        if (!empty($_POST[$row['name']])) {
                            FileHandler::MoveFromTemp(
                                "pdf/jembatan/{$id}",
                                $_POST[$row['name']]
                            );
                        }
                        break;
                    case 'img':
                        if (!empty($_POST[$row['name']])) {
                            FileHandler::MoveFromTemp(
                                "img/jembatan/{$id}",
                                $_POST[$row['name']]
                            );
                        }
                        break;
                }
            }
            $this->model('Data_model')->generateData();
        } else {
            // ! Proses gagal
            Functions::setDataSession('alert', [
                "{$tag} Jembatan failed.",
                'danger'
            ]);
        }

        // TODO: Mengembalikan hasil proses
        return Functions::getDataSession('alert');
    }

    /**
     * * Jembatan::JembatanDetail($id)
     * ? Get Jembatan detail by id
     * @param id
     * ? Id Jembatan
     */
    private function JembatanDetail($id)
    {
        return $this->my_model->getJembatanDetail($id);
    }

    /**
     * * Jembatan::JembatanRemove($id)
     * ? Menghapus Jembatan by id
     * @param id
     * ? Id Jembatan
     */
    public function JembatanRemove($id)
    {
        // TODO: Proses hapus data
        $result = $this->my_model->deleteJembatan($id);
        $tag = 'Remove';

        // TODO: Cek hasil proses hapus
        if ($result) {
            // ? Hapus Jembatan success
            Functions::setDataSession('alert', [
                "{$tag} Jembatan success.",
                'success'
            ]);
        } else {
            // ! Hapus Jembatan gagal
            Functions::setDataSession('alert', [
                "{$tag} Jembatan failed.",
                'danger'
            ]);
        }

        // TODO: Mengembalikan hasil proses
        return Functions::getDataSession('alert');
    }

    public function DataJembatan(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('DataJembatan_model');
        $this->jembatan_model = $this->model('Jembatan_model');
        $this->jalan_model = $this->model('Jalan_model');

        switch ($param1) {
            case 'search':
                $this->DataSearch();
                break;

            default:
                $this->DataJembatanDefault();
                break;
        }
    }

    private function DataJembatanDefault()
    {
        Functions::setTitle('Data Jembatan');

        $data = [
            'main' => [
                $this->dofetch('Layout/Table', [
                    'data' => Functions::defaultTableData(),
                    'thead' => $this->my_model->getDataJalanThead(),
                    'url' => BASE_URL . '/Jembatan/DataJembatan/search'
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

    private function DataSearch()
    {
        // TODO: Search Jalan on database: list & total
        [$list, $count] = $this->jembatan_model->getJembatan();
        $total = $this->jembatan_model->totalJembatan();

        $jalan_opt = $this->jalan_model->getJalanOptions();
        $kondisi_opt = $this->options('kondisi_opt');

        // TODO: Prepare data to load on template
        $rows = [];
        foreach ($list as $idx => $row) {
            $row = array_merge($row, [
                'row' => Functions::getSearch()['offset'] + $idx + 1,
                'nama_jalan' => $jalan_opt[$row['no_jalan']],
                'kondisi_bangunan_atas' =>
                    $kondisi_opt[$row['kondisi_bangunan_atas']],
                'kondisi_bangunan_bawah' =>
                    $kondisi_opt[$row['kondisi_bangunan_bawah']],
                'kondisi_fondasi' => $kondisi_opt[$row['kondisi_fondasi']],
                'kondisi_lantai' => $kondisi_opt[$row['kondisi_lantai']]
            ]);

            array_push($rows, $row);
        }

        // TODO: Echoing data as JSON
        Functions::setDataTable($rows, $count, $total);
        exit();
    }
}
