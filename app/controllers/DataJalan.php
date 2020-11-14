<?php

/**
 * * app/controllers/Data.php
 */
class DataJalan extends Controller
{
    public function index(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('DataJalan_model');
        $this->jalan_model = $this->model('Jalan_model');

        switch ($param1) {
            case 'search':
                $this->DataSearch();
                break;

            default:
                $this->DataJalanDefault();
                break;
        }
    }

    private function DataJalanDefault()
    {
        Functions::setTitle('Data Jalan');

        $data['thead'] = $this->my_model->getDataJalanThead();
        $data['data'] = Functions::defaultTableData();
        $data['search'] = false;
        $data['url'] = BASE_URL . '/DataJalan/index/search';
        $data['main'][] = $this->dofetch('Layout/Table', $data);
        $this->view('Layout/Default', $data);
    }

    private function DataSearch()
    {
        // TODO: Search Jalan on database: list & total
        [$list, $count] = $this->jalan_model->getJalan();
        $total = $this->jalan_model->totalJalan();

        // TODO: Load Kepemilikan Options
        $kepemilikan_opt = $this->options('kepemilikan_opt');

        // TODO: Prepare data to load on template
        $rows = [];
        foreach ($list as $idx => $row) {
            $row['kepemilikan'] = $kepemilikan_opt[$row['kepemilikan']];
            $row['row'] = Functions::getSearch()['offset'] + $idx + 1;
            $row['survei_date'] = !is_null($row['survei_date'])
                ? Functions::formatDatetime($row['survei_date'], 'd/m/Y')
                : $row['survei_date'];

            array_push($rows, $row);
        }

        // TODO: Echoing data as JSON
        Functions::setDataTable($rows, $count, $total);
        exit();
    }

    public function perawatan(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('Perawatan_model');

        switch ($param1) {
            case 'search':
                $this->perawatanSearch();
                break;
            case 'add':
                $this->perawatanAdd();
                break;
            case 'edit':
                if (!isset($param2)) {
                    Header('Location: ' . BASE_URL . '/StaticPage/Error404');
                }
                $this->perawatanEdit($param2);
                break;
            case 'submit':
                $this->perawatanSubmit();
                break;
            case 'remove':
                $this->perawatanRemove();
                break;
            default:
                $this->perawatanDefault();
        }
    }

    private function perawatanDefault()
    {
        // TODO: Clear coordinates session
        Functions::clearDataSession('coordinates');

        // TODO: Set title
        Functions::setTitle('Perawatan Jalan');

        // TODO: Load template
        $data = [
            'toolbar' => [
                $this->dofetch('Component/Button', $this->btn_add), // ? Add button
            ],
            'main' => [
                $this->dofetch('Layout/Table', [
                    'data' => Functions::defaultTableData(), // ? Table data
                    'thead' => $this->my_model->getJalanThead(), // ? Column name
                    'url' => BASE_URL . '/DataJalan/perawatan/search', // ? data-url
                ]),
            ],
        ];

        $this->view('Layout/Default', $data);
    }

    private function perawatanSearch()
    {
        // TODO: Search Jalan on database: list & total
        [$list, $count] = $this->my_model->getJalan();
        $total = $this->my_model->totalJalan();

        // TODO: Load Kepemilikan Options
        $kepemilikan_opt = $this->options('kepemilikan_opt');

        // TODO: Prepare data to load on template
        $rows = [];
        foreach ($list as $idx => $row) {
            $row['kepemilikan'] = $kepemilikan_opt[$row['kepemilikan']];
            $row['row'] = Functions::getSearch()['offset'] + $idx + 1;
            array_push($rows, $row);
        }

        // TODO: Echoing data as JSON
        Functions::setDataTable($rows, $count, $total);
        exit();
    }

    private function perawatanForm($param)
    {
        $data = [
            'main' => [
                $this->dofetch('Layout/Form', [
                    'form' => $this->my_model->getJalanForm(),
                    'detail' => $param['detail'],
                ]),
                $this->dofetch('Layout/Table', [
                    'url' => $param['url'],
                    'data' => Functions::defaultTableData(),
                    'thead' => $this->my_model->getKoordinatThead(),
                    'search' => 'false',
                ]),
            ],
            'toolbar' => [
                $this->dofetch(
                    'Component/Button',
                    Functions::makeButton(
                        'button',
                        'genCoord',
                        '<i class="fas fa-route"></i>&nbsp;Generate Koordinat',
                        'warning',
                        'btn-gen-coord',
                        200,
                    ),
                ),
                $this->dofetch(
                    'Component/Button',
                    Functions::makeButton(
                        'button',
                        'addPoint',
                        '<i class="fas fa-map-marker-alt"></i>&nbsp;Tambah Koordinat',
                        'success',
                        'btn-add-point',
                        180,
                    ),
                ),
            ],
            'modal' => [
                [
                    'modalId' => 'koordinatModal',
                    'modalBody' => [
                        $this->dofetch('Layout/Form', [
                            'formClass' => 'koordinatForm',
                            'form' => $this->my_model->getKoordinatForm(),
                        ]),
                    ],
                    'modalFoot' => [
                        $this->dofetch(
                            'Component/Button',
                            Functions::makeButton(
                                'button',
                                'cancel-koordinat',
                                'Cancel',
                                'danger',
                                'btn-cancel-koordinat',
                            ),
                        ),
                        $this->dofetch(
                            'Component/Button',
                            Functions::makeButton(
                                'button',
                                'submit-koordinat',
                                'Submit',
                                'success',
                                'btn-submit-koordinat',
                            ),
                        ),
                    ],
                ],
                [
                    'modalId' => 'addKoordinatModal',
                    'modalLabel' => 'Tambah Koordinat',
                    'modalSize' => 'md',
                    'modalBody' => [
                        $this->dofetch('Layout/Form', [
                            'formClass' => 'addKoordinatForm',
                            'form' => $this->my_model->getAddKoordinatForm(),
                        ]),
                    ],
                    'modalFoot' => [
                        $this->dofetch(
                            'Component/Button',
                            Functions::makeButton(
                                'button',
                                'cancel-add-point',
                                'Cancel',
                                'danger',
                                'btn-cancel-add-point',
                            ),
                        ),
                        $this->dofetch(
                            'Component/Button',
                            Functions::makeButton(
                                'button',
                                'submit-add-point',
                                'Submit',
                                'success',
                                'btn-submit-add-point',
                            ),
                        ),
                    ],
                ],
            ],
        ];
        $this->form($data);
    }

    private function perawatanAdd()
    {
        Functions::clearDataSession('coordinates');
        Functions::setTitle('Tambah Perawatan Jalan');

        $data['url'] = BASE_URL . '/DataJalan/koordinatPerawatan/search';
        $this->perawatanForm($data);
    }

    private function perawatanEdit($id)
    {
        Functions::clearDataSession('coordinates');
        Functions::setTitle('Edit Jalan');

        [$detail, $count] = $this->perawatanDetail($id);
        if ($count <= 0) {
            Header('Location: ' . BASE_URL . '/StaticPage/Error404');
        }
        $data['detail'] = $detail;

        $data['url'] =
            BASE_URL . "/Jalan/Koordinat/search/{$detail['no_jalan']}";
        $this->perawatanForm($data);
    }

    private function perawatanSubmit()
    {
        # code...
    }

    private function perawatanRemove()
    {
        # code...
    }

    private function perawatanDetail($id)
    {
        return $this->my_model->getJalanDetail($id);
    }
}
