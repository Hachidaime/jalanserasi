/**
 * assets/js/map.js
 */

/**
 * * Mendefinisikan variable
 */
var center = null;
var currentPopup;
var bounds = new google.maps.LatLngBounds();
var infowindow = new google.maps.InfoWindow()

/**
 * * Inisiasi Map (menampilkan map pada layar)
 * */
let initMap = () => {
    /**
     * * Map Options
     */
    map = new google.maps.Map(document.getElementById("map_canvas"), {
        center: new google.maps.LatLng(DEFAULT_LATITUDE, DEFAULT_LONGITUDE),
        gestureHandling: 'greedy',
        zoom: 11,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
            position: google.maps.ControlPosition.TOP_RIGHT
        },
        fullscreenControl: false,
        navigationControl: true,
        navigationControlOptions: {
            style: google.maps.NavigationControlStyle.SMALL
        },
        zoomControlOptions: {
            position: google.maps.ControlPosition.RIGHT_CENTER
        },
        streetViewControlOptions: {
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        }
    });
    center = bounds.getCenter();

    // Create the DIV to hold the control and call the CenterControl()
    // constructor passing in this DIV.
    let controlCentering = document.createElement('div');
    let centering = new centerControl(controlCentering, map);
    // controlDiv.index = 1;
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlCentering);

    let controlNav = document.createElement('div');
    let myNav = new controlOpenNav(controlNav, map);
    // controlDiv.index = 1;
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(controlNav);
    return map;
}

let initMap2 = () => {
    /**
     * * Map Options
     */
    map = new google.maps.Map(document.getElementById("map_canvas"), {
        center: new google.maps.LatLng(DEFAULT_LATITUDE, DEFAULT_LONGITUDE),
        gestureHandling: 'greedy',
        zoom: 11,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
            position: google.maps.ControlPosition.TOP_RIGHT
        },
        fullscreenControl: false,
        navigationControl: true,
        navigationControlOptions: {
            style: google.maps.NavigationControlStyle.SMALL
        },
        zoomControlOptions: {
            position: google.maps.ControlPosition.RIGHT_CENTER
        },
        streetViewControlOptions: {
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        }
    });
    center = bounds.getCenter();

    // Create the DIV to hold the control and call the CenterControl()
    // constructor passing in this DIV.
    let controlCentering = document.createElement('div');
    let centering = new centerControl(controlCentering, map);
    // controlDiv.index = 1;
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlCentering);

    // let controlNav = document.createElement('div');
    // let myNav = new controlOpenNav(controlNav, map);
    // // controlDiv.index = 1;
    // map.controls[google.maps.ControlPosition.TOP_LEFT].push(controlNav);
    return map;
}

let makeControl = () => {
    let controlUI = document.createElement('div');
    controlUI.style.backgroundColor = '#fff';
    controlUI.style.border = '2px solid #fff';
    controlUI.style.borderRadius = '2px';
    controlUI.style.boxShadow = 'rgba(0, 0, 0, 0.3) 0px 1px 4px -1px';
    controlUI.style.cursor = 'pointer';
    controlUI.style.margin = '10px';
    controlUI.style.textAlign = 'center';

    return controlUI;
}

function centerControl(controlDiv, map) {

    // Set CSS for the control border.
    let controlUI = makeControl();
    controlUI.title = 'Click to recenter the map';
    controlDiv.appendChild(controlUI);

    // Set CSS for the control interior.
    let controlText = document.createElement('div');
    controlText.style.color = 'rgb(25,25,25)';
    controlText.style.fontSize = '16px';
    controlText.style.lineHeight = '12px';
    controlText.style.padding = '5px';
    controlText.innerHTML = /*html*/`<i class="material-icons">filter_center_focus</i>`;
    controlUI.appendChild(controlText);

    // Setup the click event listeners: simply set the map to Chicago.
    controlUI.addEventListener('click', function () {
        map.setCenter({ lat: DEFAULT_LATITUDE, lng: DEFAULT_LONGITUDE });
    });
}

function controlOpenNav(controlDiv, map) {

    // ? Set CSS for the control border.
    let controlUI = makeControl();
    controlUI.title = 'Click to show navigation';
    controlDiv.appendChild(controlUI);

    // ? Set CSS for the control interior.
    let controlText = document.createElement('div');
    controlText.style.color = 'rgb(25,25,25)';
    controlText.style.fontSize = '16px';
    controlText.style.lineHeight = '12px';
    controlText.style.padding = '5px';
    controlText.innerHTML = /*html*/`<i class="material-icons">more_vert</i>`;
    controlUI.appendChild(controlText);

    // ? Setup the click event listeners
    controlUI.addEventListener('click', function () {
        // TODO: Open sidenav
        openNav();
    });

}

let makeRoadArr = coordinates => {
    let arr = [];
    $(koordinat).each(function (k, i) {
        arr.push([i[1], i.longitude]);
    });
    return arr;
}

let makePath = coordinates => {
    let path = [];
    coordinates.forEach(function (i) {
        path.push(new google.maps.LatLng(i[1], i[0]));
    });
    return new google.maps.Polyline({
        path: path
    });
}

let countLength = path => google.maps.geometry.spherical.computeLength(path.getPath());

let genSegment = () => {
    let coordinates = [];
    let importFilename = document.getElementById('upload_koordinat').value;
    let koordinat;
    let url = $table.bootstrapTable('getOptions').url;

    if (importFilename != '') {
        importFilename = /*html*/`${server_base}/upload/temp/${importFilename}`;
        coordinates = getKML(importFilename);
    }
    else {
        koordinat = getAJAX(url.replace('search', 'searchori'));
        coordinates = JSON.parse(koordinat);
    }

    let roadPath = makePath(coordinates);
    let roadLength = countLength(roadPath);

    console.log(roadLength);

    let segmentasi = document.getElementById('segmentasi').value;

    let segment = [];
    if (segmentasi > 0) {
        let seg = [];
        let i = 1;
        while (roadLength > 0) {
            let point = roadPath.GetPointAtDistance(segmentasi * i);
            if (point != null) {
                seg.push([point.lat(), point.lng()]);
                segment.push([point.lng(), point.lat(), 0]);
            }
            roadLength -= segmentasi;
            i++;
        }

        let coord = [];
        coordinates.forEach(j => {
            coord.push([j[1], j[0]]);
        });

        seg.forEach((i, k) => {
            segment[k].push(turf.nearestPointOnLine(turf.lineString(coord), turf.point(i)).properties.index);
        });

        i = 0;
        segment.forEach((x, j) => {
            let segmented = [];
            coordinates.forEach((y, k) => {
                if (k < x[3]) {
                    segmented.push(y);
                }
            });
            segmented.push(x);

            let segLength = countLength(makePath(segmented));
            x[3] += i;
            if (segLength >= segmentasi * (j + 1)) {
                x[3] += 1;
            }

            let index = x[3];
            x.pop();
            x.push(j + 1);
            coordinates.splice(index, 0, x);
            i++;
        });

        roadLength = countLength(makePath(coordinates));
    }

    console.log(roadLength);

    document.getElementById('panjang').value = roadLength.toFixed(2);
    document.getElementById('panjang_text').value = roadLength.toFixed(2);

    let params = {};
    params['coordinates'] = coordinates;
    $.post(url.replace('search', 'setsession'), $.param(params), function () {
        $table.bootstrapTable('refresh');
    });
}

let getKML = importFilename => {
    // import the file --- se related function below
    let content;
    content = getAJAX(importFilename).toString();
    content = content.replace(/gx:/g, "");

    // build an xmlObj for parsing
    xmlDocObj = $($.parseXML(content));

    let coord;
    let coordinates = [];
    if (xmlDocObj.find('coordinates').length > 0) {
        coord = xmlDocObj.find('coordinates').html().trim().split(' ');
        coord.forEach(function (el) {
            let geo = [];
            el.split(',').forEach(function (row) {
                geo.push(parseFloat(row));
            })
            coordinates.push(geo);
        });
    }
    else {
        coord = xmlDocObj.find('coord');
        coord.each(function (i, el) {
            let geo = [];
            el.textContent.split(' ').forEach(function (row) {
                geo.push(parseFloat(row));
            });
            coordinates.push(geo);
        });
    }
    return coordinates;
}

let getKepemilikan = () => {
    let kepemilikan = document.getElementById('kepemilikan').value;
    if (kepemilikan != 0) {
        switch (kepemilikan) {
            case '2':
                kepemilikan = 'JalanKotaKabupaten';
                break;
            case '3':
                kepemilikan = 'JalanPorosDesa';
                break;
            default:
                kepemilikan = 'JalanSemua';
                break;
        }
    }

    return kepemilikan;
}

let loadData = (map_data, type, jenis, simbol = null) => {
    features = new google.maps.Data();
    features.loadGeoJson(map_data);
    if (jenis != 'batas') {
        features.addListener('click', function (event) {
            // var myHTML = event.feature.getProperty("nama_jalan");
            // infowindow.setContent("<div style='width:300px;'>" + myHTML + "</div>");
            let myHTML = getFeatureInfo(event, jenis);
            infowindow.setContent(myHTML);

            // position the infowindow
            infowindow.setPosition(event.latLng);
            infowindow.open(map);
        });
    }
    features.setStyle(function (features) {
        switch (type) {
            case 'points':
                return ({
                    icon: {
                        url: `${server_base}/assets/img/${simbol}.png`,
                        scaledSize: new google.maps.Size(10, 10),
                        anchor: new google.maps.Point(5, 5),
                    },
                });
                break;
            case 'lines':
                return ({
                    fillColor: features.getProperty('fillColor'),
                    fillOpacity: features.getProperty('fillOpacity'),
                    strokeColor: features.getProperty('strokeColor'),
                    strokeWeight: features.getProperty('strokeWeight'),
                    strokeOpacity: features.getProperty('strokeOpacity'),
                });
                break;
            case 'border':
                let batasColors = { "Batas Kabupaten": "#0d0d0d", "Batas Kecamatan": "#808080", "Batas Desa": "#997300" };
                let batasWeight = { "Batas Kabupaten": "3", "Batas Kecamatan": "2", "Batas Desa": "1" };
                return ({
                    fillColor: batasColors[features.getProperty('fillColor')],
                    strokeColor: batasColors[features.getProperty('strokeColor')],
                    strokeWeight: batasWeight[features.getProperty('strokeWeight')],
                });
                break;
        }
    });
    features.setMap(map);

    return features;
}

let getFeatureInfo = (param, jenis) => {
    let type;
    let nomor;
    let nama;
    let segment;

    let html = [
        /*html*/`<div style="width:450px;">`,
        /*html*/`<table class="table table-bordered table-striped table-sm">`
    ];

    switch (jenis) {
        case 'jalan':
            type = "Ruas Jalan";
            nomor = param.feature.getProperty('no_jalan');
            nama = param.feature.getProperty('nama_jalan');
            break;
        case 'segment':
            type = "Ruas Jalan";
            nomor = param.feature.getProperty('no_jalan');
            nama = param.feature.getProperty('nama_jalan');
            segment = param.feature.getProperty('segment');
            break;
        case 'awal':
            type = "Ruas Jalan";
            nomor = param.feature.getProperty('no_jalan');
            nama = param.feature.getProperty('nama_jalan');
            break;
        case 'akhir':
            type = "Ruas Jalan";
            nomor = param.feature.getProperty('no_jalan');
            nama = param.feature.getProperty('nama_jalan');
            break;
        case 'jembatan':
            type = "Jembatan";
            nomor = param.feature.getProperty('no_jembatan');
            nama = param.feature.getProperty('nama_jembatan');
            break;
        case 'saluran':
            type = "Saluran Air";
            break;
        case 'gorong':
            type = "Gorong-gorong";
            break;
    }

    html.push(
        /*html*/`
        <tr>
            <td width="130px">No ${type}</td>
            <td width="*">${nomor}</td>
        </tr>
        `
    );

    html.push(
        /*html*/`
        <tr>
            <td>Nama ${type}</td>
            <td>${nama}</td>
        </tr>
        `
    );

    if (jenis == 'segment') {
        html.push(
            /*html*/`
            <tr>
                <td>Segment</td>
                <td>${segment}</td>
            </tr>
            `
        );
    }

    if (jenis == 'jembatan') {
        nomor = param.feature.getProperty('no_jalan');
        nama = param.feature.getProperty('nama_jalan');
        html.push(
            /*html*/`
            <tr>
                <td>No Ruas Jalan</td>
                <td>${nomor}</td>
            </tr>
            `
        );
        html.push(
            /*html*/`
            <tr>
                <td>Nama Ruas Jalan</td>
                <td>${nama}</td>
            </tr>
            `
        );
    }

    html.push(/*html*/`</table>`);
    html.push(/*html*/`</div>`);
    // console.log(html);
    return (html.join(''));
}

let Lines;

let loadLines = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}.json?t=${cur_time}`;
    Lines = loadData(map_data, 'lines', 'jalan');
}

let clearLines = () => {
    if (Lines !== undefined) {
        Lines.setMap(null);
    }
}

let JalanProvinsiLines;
let CompleteLines;
let PerkerasanLines;
let KondisiLines;
let SegmentasiPoints;
let AwalPoints;
let AkhirPoints;
let JembatanPoints;

let loadSwitch = () => {
    let jlnProvinsi = document.getElementById('jalan_provinsi').checked;
    if (jlnProvinsi) {
        loadJalanProvinsi();
    }

    let perkerasan = document.getElementById('perkerasan').checked;
    let kondisi = document.getElementById('kondisi').checked;

    if (perkerasan && kondisi) {
        clearPerkerasan();
        clearKondisi();
        loadComplete();
    }
    else {
        clearComplete();

        if (perkerasan) {
            loadPerkerasan()
        }
        else {
            clearPerkerasan();
        }

        if (kondisi) {
            loadKondisi();
        }
        else {
            clearKondisi();
        }
    }
}

let loadJalanProvinsi = () => {
    // kepemilikan = "JalanProvinsi";
    map_data = `${server_base}/data/JalanProvinsi.json?t=${cur_time}`;
    JalanProvinsiLines = loadData(map_data, 'lines', 'jalan');
}

let loadComplete = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Complete.json?t=${cur_time}`;
    CompleteLines = loadData(map_data, 'lines', 'jalan');
}

let loadPerkerasan = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Perkerasan.json?t=${cur_time}`;
    PerkerasanLines = loadData(map_data, 'lines', 'jalan');
}

let loadKondisi = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Kondisi.json?t=${cur_time}`;
    KondisiLines = loadData(map_data, 'lines', 'jalan');
}

let loadSegmentasi = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Segment.json?t=${cur_time}`;
    SegmentasiPoints = loadData(map_data, 'points', 'segment', 'circle');
}

let loadAwal = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Awal.json?t=${cur_time}`;
    AwalPoints = loadData(map_data, 'points', 'awal', 'triangle');
}

let loadAkhir = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Akhir.json?t=${cur_time}`;
    AkhirPoints = loadData(map_data, 'points', 'akhir', 'rhombus');
}

let loadJembatan = () => {
    kepemilikan = getKepemilikan();
    map_data = `${server_base}/data/${kepemilikan}Jembatan.json?t=${cur_time}`;
    JembatanPoints = loadData(map_data, 'points', 'jembatan', 'bridge');
}

let clearJalanProvinsi = () => {
    if (JalanProvinsiLines !== undefined) {
        JalanProvinsiLines.setMap(null);
    }
}

let clearComplete = () => {
    if (CompleteLines !== undefined) {
        CompleteLines.setMap(null);
    }
}

let clearPerkerasan = () => {
    if (PerkerasanLines !== undefined) {
        PerkerasanLines.setMap(null);
    }
}

let clearKondisi = () => {
    if (KondisiLines !== undefined) {
        KondisiLines.setMap(null);
    }
}

let clearSegmentasi = () => {
    if (SegmentasiPoints !== undefined) {
        SegmentasiPoints.setMap(null);
    }
}

let clearAwal = () => {
    if (AwalPoints !== undefined) {
        AwalPoints.setMap(null);
    }
}

let clearAkhir = () => {
    if (AkhirPoints !== undefined) {
        AkhirPoints.setMap(null);
    }
}

let clearJembatan = () => {
    if (JembatanPoints !== undefined) {
        JembatanPoints.setMap(null);
    }
}

let BatasLines;
let loadBatas = () => {
    map_data = `${server_base}/data/Batas.json?t=${cur_time}`;
    BatasLines = loadData(map_data, 'border', 'batas');
}

let DataJalan;
let JalanLines;
let loadDataJalan = no_jalan => {
    DataJalan = getAJAX(`${base_url}/Gis/index/datajalan/${no_jalan}`);
}

let clearDataJalan = () => {

}