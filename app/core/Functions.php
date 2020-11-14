<?php

/**
 * * app/core/Functions.php
 */
class Functions
{
    /**
     * * Functions::encrtypt
     * ? Encrypt data
     * @param string $data
     * ? Data to encrypt
     * @param string $key
     * ? Encryption key
     */
    public static function encrypt($data, $key = MY_KEY)
    {
        // TODO: Remove the base64 encoding from our key
        $encryption_key = base64_decode($key);
        // TODO: Generate an initialization vector
        $iv = openssl_random_pseudo_bytes(
            openssl_cipher_iv_length('aes-256-cbc'),
        );
        // TODO: Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
        $encrypted = openssl_encrypt(
            $data,
            'aes-256-cbc',
            $encryption_key,
            0,
            $iv,
        );
        // TODO: The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * * Functions::decrtypt
     * ? Decrypt data
     * @param string data
     * ? Data to decrypt
     * @param string $key
     * ? Encryption key
     */
    public static function decrypt($data, $key = MY_KEY)
    {
        // TODO: Remove the base64 encoding from our key
        $encryption_key = base64_decode($key);
        // TODO: To decrypt, split the encrypted data from our IV - our unique separator used was "::"
        [$encrypted_data, $iv] = explode('::', base64_decode($data), 2);
        return openssl_decrypt(
            $encrypted_data,
            'aes-256-cbc',
            $encryption_key,
            0,
            $iv,
        );
    }

    /**
     * * Functions::setTitle
     * ? Set Title & Title Bar
     * @param string $string
     * ? Title
     */
    public static function setTitle(string $string = 'Untitled')
    {
        // Todo: Cek session Admin dan User
        if (isset($_SESSION['admin']) && isset($_SESSION['USER'])) {
            // TODO: Set Title Bar Admin
            $_SESSION['title_bar'] = "{$string} - Admin Console";
        } else {
            // TODO: Set Title Bar Public
            $_SESSION['title_bar'] = "{$string} - " . PROJECT_NAME;
        }

        // TODO: Set Judul halaman
        $_SESSION['title'] = $string;
    }

    /**
     * * Functions::setList
     * ? Set list
     * @param array $array
     * ? array
     */
    public static function setList($array)
    {
        $list = [];
        foreach ($array as $idx => $row) {
            $row['row'] = $idx + 1;
            $list[$idx] = $row;
        }
        return $list;
    }

    /**
     * * Functions::getSearch
     * ? Get parameter from Bootstrap Table search
     */
    public static function getSearch()
    {
        // TODO: Get parameter
        $params = ltrim(strstr($_SERVER['REQUEST_URI'], '?'), '?');

        // TODO: Format parameter
        $rows = [];
        foreach (explode('&', $params) as $idx => $row) {
            [$key, $val] = explode('=', $row);
            $rows[$key] = urldecode($val);
        }

        // TODO: Return result
        return $rows;
    }

    /**
     * * Functions::setDataSession
     * ? Set $_SESSION values
     * @param string $type
     * ? type
     * @param string $params
     * ? parameter
     */
    public static function setDataSession(string $type, $params = [])
    {
        switch ($type) {
            case 'alert':
                /**
                 * TODO: Set $_SESSION['alert']
                 * ? Use for alert
                 * @param string message
                 * ? Alert Message
                 * @param string alert
                 * ? Alert Type: Primary, Success, Danger, Warning, Secondary, Info
                 */
                [$message, $alert] = $params;
                $_SESSION[$type][$alert][] = $message;
                break;
            case 'form':
                /**
                 * TODO: Set $_SESSION['form']
                 * ? Use for form input
                 * @param string formtype
                 * ? Input type
                 * @param string id
                 * ? Input id
                 * @param string name
                 * ? Input name
                 * @param string label
                 * ? Input label
                 * @param boolean required
                 * ? Input is required
                 * @param boolean unique
                 * ? Input is unique
                 * @param string helper
                 * ? Input helper
                 */
                [
                    $formtype,
                    $id,
                    $name,
                    $label,
                    $options,
                    $required,
                    $unique,
                    $helper,
                ] = $params;
                $_SESSION[$type][] = [
                    'type' => $formtype,
                    'id' => $id,
                    'name' => $name,
                    'label' => $label,
                    'options' => $options,
                    'required' => $required,
                    'unique' => $unique,
                    'helper' => $helper,
                ];
                break;
            case 'thead':
                /**
                 * TODO: Set $_SESSION['thead']
                 * ? Use for Bootstrap Table column name
                 * @param int row
                 * ? Row position, start from 0.
                 * @param string field
                 * ? Field name
                 * @param string title
                 * ? Table column title
                 * @param string data
                 * ? Column properties
                 */
                [$row, $field, $title, $data] = $params;
                $_SESSION[$type][$row][] = [
                    'field' => $field,
                    'title' => $title,
                    'data' => $data,
                ];
                break;
            default:
                /**
                 * TODO: Set $_SESSION[$type]
                 * ? Set session
                 */
                $_SESSION[$type] = $params;
                break;
        }
    }

    /**
     * * Functions::getDataSession
     * ? Get $_SESSION values
     * @param string $type
     * ? type
     * @param boolean $clear
     * ? Check clear session. Default: TRUE.
     */
    public static function getDataSession(string $type, bool $clear = true)
    {
        $data = $_SESSION[$type];
        // TODO: Cek clear session.
        if ($clear) {
            self::clearDataSession($type);
        }
        return $data;
    }

    /**
     * * Functions::clearDataSession
     * ? Get $_SESSION values
     * @param string type
     * ? type
     */
    public static function clearDataSession(string $type)
    {
        unset($_SESSION[$type]);
    }

    /**
     * * Functions::parseURL
     * ? Parsing URL dari $_GET['url];
     */
    public static function parseURL()
    {
        // TODO: Cek URL Admin
        if (strpos($_GET['url'], 'Admin') !== false) {
            // TODO: Set Session Admin
            self::setDataSession('admin', true);
        } else {
            // TODO: Unset Session Admin
            self::clearDataSession('admin');
        }

        // TODO: Cek $_GET['url'] exist
        if (isset($_GET['url'])) {
            $url = ltrim($_GET['url'], 'Admin');
            $url = ltrim($url, '/');
            $url = rtrim($url, '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }

    /**
     * * Functions::formatDatetime
     * ? Formatting Datetime
     * @param string $dt
     * ? Input datatime. Format date: 'd/m/Y', 'Y-m-d'.
     * @param string $format
     * ? Format Output datetime
     */
    public static function formatDatetime(string $dt, string $format)
    {
        if (strpos($dt, '/') !== false) {
            $dt = implode('-', array_reverse(explode('/', $dt)));
        }
        return date($format, strtotime($dt));
    }

    /**
     * * Functions::genOptions
     * ? Change 3D Array to 2D Array
     * @param array $array
     * ? 3D Array
     * @param string $key
     * ? Variable as array key
     * @param string $value
     * ? Variable as array value
     */
    public static function genOptions(
        array $array,
        string $value,
        string $key = null
    ) {
        $options = [];
        foreach ($array as $row) {
            if ($key) {
                $options[$row[$key]] = $row[$value];
            } else {
                array_push($options, $row[$value]);
            }
        }

        return $options;
    }

    /**
     * * Functions::getPopupLink
     * ? Get Popup Link
     * @param string $directory
     * @param string $filename
     * @param string $title
     * @param string $footer
     */
    public static function getPopupLink(
        $directory,
        $filename,
        $title,
        $footer,
        $icon = null
    ) {
        $data_title = !empty($title) ? "data-title=\"{$title}\"" : '';
        $data_footer = !empty($footer) ? "data-footer=\"{$footer}\"" : '';

        $file_url = UPLOAD_URL . "{$directory}/{$filename}";

        if (!is_null($icon)) {
            $text = "<i class=\"{$icon}\"></i>";
        } else {
            $text = $filename;
        }

        return "<a href=\"{$file_url}\" data-toggle=\"lightbox\" {$data_title} {$data_footer}>{$text}</a>";
    }

    /**
     * * Functions::makeButton
     * ? Making button
     * @param string $tag
     * ? HTML tag as button
     * @param string $id
     * ? Button id
     * @param string $html
     * ? Button content
     * @param string $color
     * ? Button color, use bootstrap Color
     * @param string $class
     * ? Button class
     * @param int $width
     * ? Button width
     */
    public static function makeButton(
        string $tag,
        string $id,
        string $html,
        string $color = null,
        string $class = null,
        int $width = 150
    ) {
        $array = [
            'tag' => $tag,
            'id' => $id,
            'html' => $html,
            'color' => $color,
            'class' => $class,
            'width' => $width,
        ];

        return $array;
    }

    /**
     * * Functions::getTable
     * ? Get table name from table list
     * @param array $tables
     * ? Table list
     * @param string $type
     * ? Type
     */
    public static function getTable(array $tables, string $type = null)
    {
        if (!is_null($type)) {
            $table = $tables[$type];
        } else {
            $table = $tables;
        }
        return $table;
    }

    /**
     * * Functions::setDataTable
     * ? Set data to Bootstrap Table
     * @param array $rows
     * ? Table rows
     * @param int $count
     * @param int $total
     */
    public static function setDataTable(array $rows, int $count, int $total)
    {
        $result = [];
        $search = self::getSearch();

        if ($count >= $search['limit']) {
            $result['total'] = $count;
        }
        $result['totalNotFiltered'] = $total;
        $result['rows'] = $rows;
        echo json_encode($result);
    }

    /**
     * * Functions::getStringBetween
     * ? Get string between 2 string
     * @param string $str
     * @param string $from
     * @param string $to
     */
    public static function getStringBetween(
        string $str,
        string $from,
        string $to
    ) {
        $string = substr($str, strpos($str, $from) + strlen($from));

        if (strstr($string, $to, true) != false) {
            $string = strstr($string, $to, true);
        }

        return $string;
    }

    /**
     * * Functions::makeTableData
     * ? Setting data properties on DataTable
     * @param array $data
     */
    public static function makeTableData(array $data)
    {
        foreach ($data as $key => $value) {
            $result[] = "data-{$key}=\"{$value}\"";
        }

        return implode(' ', $result);
    }

    /**
     * * Functions::defaultTableData
     * ? Get default data properties on DataTable
     */
    public static function defaultTableData()
    {
        $data = DEFAULT_TABLE_DATA;
        // var_dump($data);
        return self::makeTableData($data);
    }

    public static function makeMapPoint(array $point, $raw = false)
    {
        $point = $raw ? $point : array_reverse($point);
        unset($point[2]);
        foreach ($point as $key => $value) {
            $point[$key] = (float) $value;
        }
        // array_push($point, 0);
        return $point;
    }

    public static function getParams(array $data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, ['select', 'sort'])) {
                $params[$key] = implode(', ', $value);
            } else {
                $params[$key] = implode(' ', $value);
            }
        }
        return $params;
    }

    public static function getPointFromJembatan(
        array $data,
        array $style,
        string $kepemilikan = null
    ) {
        $point = [];
        foreach ($data as $row) {
            // if (!is_null($kepemilikan)) {
            //     if ($row['kepemilikan'] != $kepemilikan) continue;
            // } else {
            //     if ($row['kepemilikan'] == 1) continue;
            // }
            $row['no_point'] = $row['no_jembatan'];
            $row['nama_point'] = $row['nama_jembatan'];
            $row['koordinat'] = [
                (float) $row['longitude'],
                (float) $row['latitude'],
            ];
            $point[] = $row;
        }

        return $point;
    }

    public static function getLineFromJalan(
        array $data,
        array $style,
        string $kepemilikan = null
    ) {
        foreach ($data as $row) {
            // if (!is_null($kepemilikan)) {
            //     if ($row['kepemilikan'] != $kepemilikan) continue;
            // } else {
            //     if ($row['kepemilikan'] == 1) continue;
            // }

            $coord = $row['segmented'];
            if (empty($coord)) {
                $coord = $row['ori'];
            }

            unset($row['ori']);
            unset($row['segmented']);

            $koordinat = [];
            foreach (json_decode($coord, true) as $value) {
                $koordinat[] = self::makeMapPoint($value, true);
            }

            $row['koordinat'] = $koordinat;
            $row['style'] = $style[$row['kepemilikan']][0][0];

            $line[] = $row;
        }

        return $line;
    }

    public static function getLineFromDetail(
        array $detail,
        array $lineStyle,
        array $iconStyle,
        string $kepemilikan = null
    ) {
        $awal = [];
        $akhir = [];
        $segment = [];
        $complete = [];
        $perkerasan = [];
        $kondisi = [];
        $f = 0;
        $g = 0;
        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;

        $row_id = 0;
        foreach ($detail as $idx => $row) {
            // if (!is_null($kepemilikan)) {
            //     if ($row['kepemilikan'] != $kepemilikan) continue;
            // } else {
            //     if ($row['kepemilikan'] == 1) continue;
            // }

            // $koordinat = implode(' ', array_map("Functions::makeMapPoint", json_decode($row['koordinat'], true)));
            $koordinat = array_map(
                'self::makeMapPoint',
                json_decode($row['koordinat'], true),
            );
            $latitude = $row['latitude'];
            $longitude = $row['longitude'];

            $data = json_decode($row['data'], true);
            $row['foto'] = $data[0][6];

            unset($row['koordinat']);
            unset($row['data']);
            unset($row['latitude']);
            unset($row['longitude']);

            if ($row['segment'] == 0) {
                // $row['style'] = $iconStyle[1];
                $row['koordinat'] = [(float) $longitude, (float) $latitude];
                $row['row'] = 1;
                $awal[$f] = $row;
                $f++;
            }

            if (
                $row['segment'] != $detail[$idx - 1]['segment'] &&
                $row['segment'] > 0
            ) {
                // $row['style'] = $iconStyle[1];
                $row['koordinat'] = [(float) $longitude, (float) $latitude];
                $row['row'] = $row_id + 1;
                // $row['row'] = $row['row_id'];
                $row['segment'] = Functions::formatSegment(
                    $row['segment'],
                    $row['segmentasi'],
                );
                $segment[$i] = $row;
                $i++;
            }
            $row_id = $row_id + count($data);

            if (
                $detail[$idx + 1]['segment'] == 0 ||
                is_null($detail[$idx + 1])
            ) {
                $row['koordinat'] = end($koordinat);
                $row['row'] = $row_id;
                $akhir[$g] = $row;
                $g++;
            }

            // print '<pre>';
            // print_r([$row['jml_segmented'], $row_id]);
            // print '</pre>';
            if ($row_id == $row['jml_segmented']) {
                $row_id = 0;
            }

            unset($row['row']);
            unset($row['foto']);

            $row['koordinat'] = $koordinat;

            if ($row['perkerasan'] > 0 || $row['kondisi'] > 0) {
                if (
                    $row['no_detail'] > 0 &&
                    $row['no_detail'] - 1 == $detail[$idx - 1]['no_detail']
                ) {
                    if (
                        $row['perkerasan'] == $detail[$idx - 1]['perkerasan'] &&
                        $row['kondisi'] == $detail[$idx - 1]['kondisi']
                    ) {
                        $complete[$j - 1]['koordinat'] = array_merge(
                            $complete[$j - 1]['koordinat'],
                            $koordinat,
                        );
                    } else {
                        $row['style'] =
                            $lineStyle[$row['kepemilikan']][$row['perkerasan']][
                                $row['kondisi']
                            ];
                        $complete[$j] = $row;
                        $j++;
                    }
                } else {
                    $row['style'] =
                        $lineStyle[$row['kepemilikan']][$row['perkerasan']][
                            $row['kondisi']
                        ];
                    $complete[$j] = $row;
                    $j++;
                }
            }

            if ($row['perkerasan'] > 0) {
                if (
                    $row['no_detail'] > 0 &&
                    $row['no_detail'] - 1 == $detail[$idx - 1]['no_detail']
                ) {
                    if ($row['perkerasan'] == $detail[$idx - 1]['perkerasan']) {
                        $perkerasan[$k - 1]['koordinat'] = array_merge(
                            $perkerasan[$k - 1]['koordinat'],
                            $koordinat,
                        );
                    } else {
                        $row['style'] =
                            $lineStyle[$row['kepemilikan']][
                                $row['perkerasan']
                            ][0];
                        $perkerasan[$k] = $row;
                        $k++;
                    }
                } else {
                    $row['style'] =
                        $lineStyle[$row['kepemilikan']][$row['perkerasan']][0];
                    $perkerasan[$k] = $row;
                    $k++;
                }
            }

            if ($row['kondisi'] > 0) {
                if (
                    $row['no_detail'] > 0 &&
                    $row['no_detail'] - 1 == $detail[$idx - 1]['no_detail']
                ) {
                    if ($row['kondisi'] == $detail[$idx - 1]['kondisi']) {
                        $kondisi[$l - 1]['koordinat'] = array_merge(
                            $kondisi[$l - 1]['koordinat'],
                            $koordinat,
                        );
                    } else {
                        $row['style'] =
                            $lineStyle[$row['kepemilikan']][0][$row['kondisi']];
                        $kondisi[$l] = $row;
                        $l++;
                    }
                } else {
                    $row['style'] =
                        $lineStyle[$row['kepemilikan']][0][$row['kondisi']];
                    $kondisi[$l] = $row;
                    $l++;
                }
            }
        }

        return [$segment, $complete, $perkerasan, $kondisi, $awal, $akhir];
    }

    public static function getStyle(array $setup)
    {
        $m = 1;
        foreach (DEFAULT_ICONSTYLE as $idx => $row) {
            $id = "iconstyle{$m}";
            $style[$id] = [
                'type' => $row['type'],
                'href' => $row['href'],
            ];
            $iconStyle[$idx] = "#{$id}";
            $m++;
        }

        $n = 1;
        foreach (DEFAULT_LINESTYLE as $idx => $row) {
            $id = "linestyle{$n}";
            $style[$id] = [
                'color' => $row['color'],
                'opacity' => number_format($row['opacity'] / 100, 2),
                'weight' => $row['width'],
            ];
            $lineStyle[$idx][0][0] = "#{$id}";
            $n++;
        }

        foreach ($setup as $idx => $row) {
            if ($row['simbol'] == 1) {
                $id = "linestyle{$n}";
                $style[$id] = [
                    'color' => $row['warna'],
                    'opacity' => number_format($row['opacity'] / 100, 2),
                    'weight' => !is_null($row['line_width'])
                        ? $row['line_width']
                        : 0,
                ];
                $lineStyle[$row['kepemilikan']][$row['perkerasan']][
                    $row['kondisi']
                ] = "#{$id}";
                $n++;
            }
        }

        return [$style, $lineStyle, $iconStyle];
    }

    public static function createFeatureCollection($style, $content, $simbol)
    {
        if (empty($content)) {
            return null;
        }
        $list['type'] = 'FeatureCollection';
        $list['features'] = [];
        foreach ($content as $idx => $row) {
            $list['features'][$idx] = self::createFeature(
                $style,
                $row,
                $simbol,
            );
        }

        return $list;
    }

    public static function createFeature($style, $data, $simbol)
    {
        $properties = [
            'no_jalan' => $data['no_jalan'],
            'nama_jalan' => $data['nama_jalan'],
            'kepemilikan' => $data['kepemilikan_text'],
        ];
        $mystyle = str_replace('#', '', $data['style']);
        switch ($simbol) {
            case '1':
                $type = 'LineString';
                $properties = array_merge($properties, [
                    'strokeColor' => $style[$mystyle]['color'],
                    'strokeWeight' => $style[$mystyle]['weight'],
                    'strokeOpacity' => $style[$mystyle]['opacity'],
                    'fillColor' => $style[$mystyle]['color'],
                    'fillOpacity' => $style[$mystyle]['opacity'],
                ]);
                break;
            case '2':
                $type = 'Point';
                $properties = array_merge($properties, [
                    'no_point' => $data['no_point'],
                    'nama_point' => $data['nama_point'],
                    'segment' => $data['segment'],
                    'row' => $data['row'],
                    'foto' => $data['foto'],
                ]);
                break;
        }

        $feature = [
            'type' => 'Feature',
            'geometry' => [
                'type' => $type,
                'coordinates' => $data['koordinat'],
            ],
            'properties' => $properties,
        ];

        return $feature;
    }

    public static function saveGeoJSON(
        string $filename,
        $style,
        $content,
        $simbol
    ) {
        $content = self::createFeatureCollection($style, $content, $simbol);
        self::saveJSON($filename, $content);
    }

    public static function saveJSON(string $filename, $content)
    {
        $filedir = DOC_ROOT . 'data';
        FileHandler::createWritableFolder($filedir);

        ($myfile = fopen("{$filedir}/{$filename}", 'w')) or
            die('Unable to open file!');
        fwrite($myfile, json_encode($content));
        fclose($myfile);
    }

    public static function readJSON(string $filepath)
    {
        $strJsonFileContents = file_get_contents($filepath);
        $array = json_decode($strJsonFileContents, true);

        return $array;
    }

    public static function buildGeo(array $coordinate, bool $raw)
    {
        $result = [];
        array_push($result, (float) $coordinate[0], (float) $coordinate[1], 0);

        if (!$raw) {
            $result = [];
            array_push(
                $result,
                (float) $coordinate[1],
                (float) $coordinate[0],
                0,
            );
        }
        return $result;
    }

    public static function buildLine(array $params, string $type)
    {
        $result = [];
        $data = [];
        $index = -1;

        foreach ($params as $idx => $row) {
            $point = [];
            array_push(
                $point,
                (float) $row['longitude'],
                (float) $row['latitude'],
                0,
            );
            $data[$idx] = $idx > 0 ? $data[$idx - 1] : 0;
            if ($row[$type] > 0) {
                // if ($index >= 0) $result[$index][$data[$idx]][] = $point;
                if ($index >= 0) {
                    $result[$data[$idx]][$index][] = $point;
                }

                $data[$idx] = $row[$type];
                $index++;
            }

            // $result[$index][$data[$idx]][] = $point;
            $result[$data[$idx]][$index][] = $point;
        }

        unset($result[0]);
        return $result;
    }

    public static function getGeo($remote_ip)
    {
        if (
            !in_array($remote_ip, ['127.0.0.1', '::1']) &&
            strpos($remote_ip, '192.168') === false
        ) {
            $geo = unserialize(
                file_get_contents(
                    "http://www.geoplugin.net/php.gp?ip={$remote_ip}",
                ),
            );
            return [
                (float) $geo['geoplugin_longitude'],
                (float) $geo['geoplugin_latitude'],
            ];
        }
        return [110.4037533, -7.1186337];
    }

    public static function getTags($string)
    {
        preg_match_all('~<([^/][^>]*?)>~', $string, $arr);

        return $arr[1];
    }

    public static function readKml(string $filepath)
    {
        $kml = [];

        // $xmldata = simplexml_load_file($filepath) or die("Failed to load"); // ? return object
        ($xmldata = file_get_contents($filepath)) or die('Failed to load'); // ? return string
        if (strpos($xmldata, 'coordinates') !== false) {
            $str = self::getStringBetween(
                $xmldata,
                '<coordinates>',
                '</coordinates>',
            );
            $str = trim($str);

            $kml = array_map(function ($val) {
                return explode(',', $val);
            }, explode(' ', $str));
        } else {
            // $str = self::getStringBetween($xmldata, "<gx:coord>", "</gx:coord>");
            $xml = new SimpleXMLElement(str_replace('gx:', '', $xmldata));
            $kml = array_map(function ($val) {
                $point = explode(' ', $val);
                array_splice($point, 2, 1, 0);
                return $point;
            }, (array) $xml->Document->Placemark->MultiTrack->Track->coord);
        }
        return $kml;
    }

    public static function formatSegment($segment, $segmentasi = 200)
    {
        $segLoc = number_format($segment * $segmentasi, 0, '', '+');
        return "{$segment} (STA {$segLoc})";
    }
}
