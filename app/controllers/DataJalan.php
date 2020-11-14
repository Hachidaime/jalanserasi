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
}
