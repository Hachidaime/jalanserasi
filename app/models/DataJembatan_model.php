<?php
class DataJembatan_model extends Database
{
    public function getDataJalanThead()
    {
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', [
            '0',
            'no_jembatan',
            'Nomor Jembatan',
            'data-halign="center" data-align="center" data-width="150"'
        ]);
        Functions::setDataSession('thead', [
            '0',
            'nama_jembatan',
            'Nama Jembatan',
            'data-halign="center" data-align="left"'
        ]);
        Functions::setDataSession('thead', [
            '0',
            'nama_jalan',
            'Nama Jalan',
            'data-halign="center" data-align="left" data-width="200"'
        ]);
        Functions::setDataSession('thead', [
            '0',
            'nama_sungai',
            'Nama Sungai',
            'data-halign="center" data-align="left" data-width="120"'
        ]);
        Functions::setDataSession('thead', [
            '0',
            'bms',
            'BMS',
            'data-halign="center" data-align="left" data-width="50"'
        ]);
        Functions::setDataSession('thead', [
            '0',
            'keterangan',
            'Keterangan',
            'data-halign="center" data-align="left" data-width="120"'
        ]);
        Functions::setDataSession('thead', ['0', 'viewjembatan']);
        return Functions::getDataSession('thead');
    }
}
