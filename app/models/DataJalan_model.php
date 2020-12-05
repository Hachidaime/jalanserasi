<?php
class DataJalan_model extends Database
{
    public function getDataJalanThead()
    {
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', [
            '0',
            'no_jalan',
            'Nomor Ruas Jalan',
            'data-halign="center" data-align="center" data-width="150"'
        ]);
        Functions::setDataSession('thead', [
            '0',
            'nama_jalan',
            'Nama Ruas Jalan',
            'data-halign="center" data-align="left"'
        ]);
        Functions::setDataSession('thead', [
            '0',
            'kepemilikan',
            'Status',
            'data-halign="center" data-align="left" data-width="200"'
        ]);
        Functions::setDataSession('thead', [
            '0',
            'panjang',
            'Panjang<br>(km)',
            'data-halign="center" data-align="left" data-width="120"'
        ]);
        Functions::setDataSession('thead', [
            '0',
            'lebar_rata',
            'Lebar<br>Rata-Rata (m)',
            'data-halign="center" data-align="left" data-width="120"'
        ]);
        Functions::setDataSession('thead', [
            '0',
            'survei_date',
            'Tanggal Survey',
            'data-halign="center" data-align="left" data-width="120"'
        ]);
        Functions::setDataSession('thead', ['0', 'viewprint']);
        return Functions::getDataSession('thead');
    }
}
