/**
 * assets/js/map.js
 */

/**
 * * Mendefinisikan variable
 */
var center = null
var currentPopup
var bounds = new google.maps.LatLngBounds()
var infowindow = new google.maps.InfoWindow({
  maxWidth: 600,
})

/**
 * * Inisiasi Map (menampilkan map pada layar)
 * */
let initMap = () => {
  /**
   * * Map Options
   */
  map = new google.maps.Map(document.getElementById('map_canvas'), {
    center: new google.maps.LatLng(DEFAULT_LATITUDE, DEFAULT_LONGITUDE),
    gestureHandling: 'greedy',
    zoom: 11,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    mapTypeControl: true,
    mapTypeControlOptions: {
      style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
      position: google.maps.ControlPosition.TOP_RIGHT,
    },
    fullscreenControl: false,
    navigationControl: true,
    navigationControlOptions: {
      style: google.maps.NavigationControlStyle.SMALL,
    },
    zoomControlOptions: {
      position: google.maps.ControlPosition.RIGHT_CENTER,
    },
    streetViewControlOptions: {
      position: google.maps.ControlPosition.RIGHT_BOTTOM,
    },
  })
  center = bounds.getCenter()

  // Create the DIV to hold the control and call the CenterControl()
  // constructor passing in this DIV.
  let controlCentering = document.createElement('div')
  let centering = new centerControl(controlCentering, map)
  // controlDiv.index = 1;
  map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlCentering)

  let controlNav = document.createElement('div')
  let myNav = new controlOpenNav(controlNav, map)
  // controlDiv.index = 1;
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(controlNav)
  return map
}

let initMap2 = () => {
  /**
   * * Map Options
   */
  map = new google.maps.Map(document.getElementById('map_canvas'), {
    center: new google.maps.LatLng(DEFAULT_LATITUDE, DEFAULT_LONGITUDE),
    gestureHandling: 'greedy',
    zoom: 13,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    mapTypeControl: true,
    mapTypeControlOptions: {
      style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
      position: google.maps.ControlPosition.TOP_RIGHT,
    },
    fullscreenControl: false,
    navigationControl: true,
    navigationControlOptions: {
      style: google.maps.NavigationControlStyle.SMALL,
    },
    zoomControlOptions: {
      position: google.maps.ControlPosition.RIGHT_CENTER,
    },
    streetViewControlOptions: {
      position: google.maps.ControlPosition.RIGHT_BOTTOM,
    },
  })
  center = bounds.getCenter()

  // Create the DIV to hold the control and call the CenterControl()
  // constructor passing in this DIV.
  let controlCentering = document.createElement('div')
  let centering = new centerControl(controlCentering, map)
  // controlDiv.index = 1;
  map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlCentering)

  // let controlNav = document.createElement('div');
  // let myNav = new controlOpenNav(controlNav, map);
  // // controlDiv.index = 1;
  // map.controls[google.maps.ControlPosition.TOP_LEFT].push(controlNav);
  return map
}

let makeControl = () => {
  let controlUI = document.createElement('div')
  controlUI.style.backgroundColor = '#fff'
  controlUI.style.border = '2px solid #fff'
  controlUI.style.borderRadius = '2px'
  controlUI.style.boxShadow = 'rgba(0, 0, 0, 0.3) 0px 1px 4px -1px'
  controlUI.style.cursor = 'pointer'
  controlUI.style.margin = '10px'
  controlUI.style.textAlign = 'center'

  return controlUI
}

let default_center = { lat: DEFAULT_LATITUDE, lng: DEFAULT_LONGITUDE }
let map_center = default_center

function centerControl(controlDiv, map) {
  // Set CSS for the control border.
  let controlUI = makeControl()
  controlUI.title = 'Click to recenter the map'
  controlDiv.appendChild(controlUI)

  // Set CSS for the control interior.
  let controlText = document.createElement('div')
  controlText.style.color = 'rgb(25,25,25)'
  controlText.style.fontSize = '16px'
  controlText.style.lineHeight = '12px'
  controlText.style.padding = '5px'
  controlText.innerHTML = /*html*/ `<i class="material-icons">filter_center_focus</i>`
  controlUI.appendChild(controlText)

  // Setup the click event listeners: simply set the map to Chicago.
  controlUI.addEventListener('click', function () {
    map.setCenter(map_center)
  })
}

function controlOpenNav(controlDiv, map) {
  // ? Set CSS for the control border.
  let controlUI = makeControl()
  controlUI.title = 'Click to show navigation'
  controlDiv.appendChild(controlUI)

  // ? Set CSS for the control interior.
  let controlText = document.createElement('div')
  controlText.style.color = 'rgb(25,25,25)'
  controlText.style.fontSize = '16px'
  controlText.style.lineHeight = '12px'
  controlText.style.padding = '5px'
  controlText.innerHTML = /*html*/ `<i class="material-icons">more_vert</i>`
  controlUI.appendChild(controlText)

  // ? Setup the click event listeners
  controlUI.addEventListener('click', function () {
    // TODO: Open sidenav
    openNav()
  })
}

let makeRoadArr = (coordinates) => {
  let arr = []
  $(koordinat).each(function (k, i) {
    arr.push([i[1], i.longitude])
  })
  return arr
}

let makePath = (coordinates) => {
  let path = []
  coordinates.forEach(function (i) {
    path.push(new google.maps.LatLng(i[1], i[0]))
  })
  return new google.maps.Polyline({
    path: path,
  })
}

let countLength = (path) =>
  google.maps.geometry.spherical.computeLength(path.getPath())

let roadPath
let roadLength
let coordinates = []
let koordinat
let tableUrl

let getCoordinates = (type = 'ori') => {
  let importFilename = document.getElementById('upload_koordinat').value
  tableUrl = $table.bootstrapTable('getOptions').url

  if (importFilename != '') {
    importFilename = /*html*/ `${server_base}/upload/temp/${importFilename}`
    coordinates = getKML(importFilename)
    // console.log("from file");
  } else {
    koordinat = getAJAX(tableUrl.replace('search', `search${type}`))
    coordinates = JSON.parse(koordinat)
    // console.log("from table");
  }

  roadPath = makePath(coordinates)
  roadLength = countLength(roadPath)
  // console.log(roadLength);
}

let genSegment = () => {
  getCoordinates()

  document.getElementById('panjang').value = roadLength.toFixed(2)
  document.getElementById('panjang_text').value = roadLength.toFixed(2)

  let segmentasi = document.getElementById('segmentasi').value

  let segment = []
  let segPosition = []
  if (segmentasi > 0) {
    let seg = []
    let i = 1
    while (roadLength > 0) {
      let point = roadPath.GetPointAtDistance(segmentasi * i)
      if (point != null) {
        seg.push([point.lat(), point.lng()])
        segment.push([point.lng(), point.lat(), 0])
      }
      roadLength -= segmentasi
      i++
    }

    let coord = []
    coordinates.forEach((j) => {
      coord.push([j[1], j[0]])
    })

    seg.forEach((i, k) => {
      segment[k].push(
        turf.nearestPointOnLine(turf.lineString(coord), turf.point(i))
          .properties.index
      )
    })

    i = 0

    segment.forEach((x, j) => {
      let segmented = []
      coordinates.forEach((y, k) => {
        if (k < x[3]) {
          segmented.push(y)
        }
      })
      segmented.push(x)

      let segLength = countLength(makePath(segmented))
      x[3] += i
      if (segLength >= segmentasi * (j + 1)) {
        x[3] += 1
      }

      let index = x[3]
      x.pop()
      x.push(j + 1)
      coordinates.splice(index, 0, x)
      // console.log(`${j + 1} ${index}`);
      segPosition[j] = index
      i++
    })

    // roadLength = countLength(makePath(coordinates));
  }

  let params = {}
  // params["coordinates"] = coordinates;
  params['segment'] = segment
  params['filename'] = document.getElementById('upload_koordinat').value
  params['segPosition'] = segPosition
  params['segmentasi'] = segmentasi

  // console.log(coordinates.length);
  // console.log(segment.length);
  // console.log(params);
  // console.log(segPosition);

  // return false;
  $.post(
    tableUrl.replace('search', 'setsession'),
    $.param(params),
    function () {
      $table.bootstrapTable('refresh')
    }
  )
}

let addPoint = (distance) => {
  getCoordinates('segmented')

  let coord = []
  coordinates.forEach((j) => {
    coord.push([j[1], j[0]])
  })

  if (distance <= roadLength) {
    let point
    let newPoint
    point = roadPath.GetPointAtDistance(distance)
    newPoint = [point.lng(), point.lat(), 0]
    point = [point.lat(), point.lng()]
    let index = turf.nearestPointOnLine(
      turf.lineString(coord),
      turf.point(point)
    ).properties.index
    newPoint.push('new')

    let points = []
    coordinates.forEach((y, k) => {
      if (k < index) {
        points.push(y)
      }
    })
    points.push(newPoint)

    let pointsLength = countLength(makePath(points))

    if (pointsLength >= distance) {
      index += 1
    }

    coordinates.splice(index, 0, newPoint)

    let params = {}
    // params["coordinates"] = coordinates;
    params['newCoord'] = [newPoint]
    params['newPosition'] = [index]

    $.post(
      tableUrl.replace('search', 'setsession'),
      $.param(params),
      function () {
        let modal = $('#addKoordinatModal')
        modal.modal('hide')
        $table.bootstrapTable('refresh')
      }
    )
  } else {
    makeAlert(
      JSON.parse('{"danger":["Jarak lebih besar dari Panjang Ruas Jalan."]}')
    )
  }
}

let getKML = (importFilename) => {
  // import the file --- se related function below
  let content
  content = getAJAX(importFilename).toString()
  content = content.replace(/gx:/g, '')

  // build an xmlObj for parsing
  xmlDocObj = $($.parseXML(content))

  let coord
  let coordinates = []
  if (xmlDocObj.find('coordinates').length > 0) {
    coord = xmlDocObj.find('coordinates').html().trim().split(' ')
    coord.forEach(function (el) {
      let geo = []
      el.split(',').forEach(function (row) {
        geo.push(parseFloat(row))
      })
      geo[2] = 0
      coordinates.push(geo)
    })
  } else {
    coord = xmlDocObj.find('coord')
    coord.each(function (i, el) {
      let geo = []
      el.textContent.split(' ').forEach(function (row) {
        geo.push(parseFloat(row))
      })
      geo[2] = 0
      coordinates.push(geo)
    })
  }
  return coordinates
}

let getKepemilikan = () => {
  let kepemilikan = document.getElementById('kepemilikan').value
  if (kepemilikan != 0) {
    switch (kepemilikan) {
      case '2':
        kepemilikan = 'JalanKotaKabupaten'
        break
      case '3':
        kepemilikan = 'JalanPorosDesa'
        break
      default:
        kepemilikan = 'JalanSemua'
        break
    }
  }

  return kepemilikan
}

let loadData = (map_data, type, jenis, icon = null, noInfo = false) => {
  features = new google.maps.Data()
  features.addGeoJson(map_data)

  if (['batas', 'position'].indexOf(jenis) == -1) {
    features.addListener('click', function (event) {
      // var myHTML = event.feature.getProperty("nama_jalan");
      // infowindow.setContent("<div style='width:300px;'>" + myHTML + "</div>");
      if (!noInfo) {
        let myHTML = getFeatureInfo(event, jenis)

        infowindow.setContent(myHTML)

        // position the infowindow
        infowindow.setPosition(event.latLng)
        infowindow.open(map)
      }
    })
  }

  features.setStyle(function (features) {
    switch (type) {
      case 'points':
        return pointStyle(icon)
        break
      case 'lines':
        return lineStyle(features)
        break
      case 'border':
        return borderStyle(features)
        break
    }
  })
  features.setMap(map)

  return features
}

let pointStyle = (icon) => {
  return {
    icon: icon,
  }
}

let lineStyle = (features) => {
  return {
    fillColor: features.getProperty('fillColor'),
    fillOpacity: features.getProperty('fillOpacity'),
    strokeColor: features.getProperty('strokeColor'),
    strokeWeight: features.getProperty('strokeWeight'),
    strokeOpacity: features.getProperty('strokeOpacity'),
  }
}

let borderStyle = (features) => {
  let batasColors = {
    'Batas Kabupaten': '#0d0d0d',
    'Batas Kecamatan': '#808080',
    'Batas Desa': '#997300',
  }
  let batasWeight = {
    'Batas Kabupaten': '3',
    'Batas Kecamatan': '2',
    'Batas Desa': '1',
  }
  return {
    fillColor: batasColors[features.getProperty('fillColor')],
    strokeColor: batasColors[features.getProperty('strokeColor')],
    strokeWeight: batasWeight[features.getProperty('strokeWeight')],
  }
}

let jalandir = `${server_base}/upload/img/jalan/`

let getFeatureInfo = (param, jenis) => {
  if (jenis == 'jembatan') {
    return jembatanInfo(param)
  } else {
    let jml_jembatan = param.feature.getProperty('jml_jembatan')
    jml_jembatan = jml_jembatan != undefined ? jml_jembatan : 0

    let html = [
      /*html*/ `<div style="width:600px;">`,
      /*html*/ `<table class="table table-bordered table-striped table-sm" width="100%">`,
    ]

    switch (jenis) {
      case 'jalan':
        html = html.concat(jalanInfo(param))
        break
      case 'segment':
        html = html.concat(jalanInfo(param), segmentInfo(param))
        break
      case 'awal':
        html = html.concat(jalanInfo(param), ujungInfo(param))
        break
      case 'akhir':
        html = html.concat(jalanInfo(param), ujungInfo(param))
        break

      case 'saluran':
        type = 'Saluran Air'
        break
      case 'gorong':
        type = 'Gorong-gorong'
        break
      case 'position':
        html = html.concat(positionInfo())
        break
    }

    html = html.concat([
      /*html*/ `
      <tr>
        <td>JML JEMBATAN</td>
        <td colspan="3">${jml_jembatan}</td>
      </tr>
      `,
      /*html*/ `</table>`,
      /*html*/ `</div>`,
    ])
    // console.log(html);
    return html.join('')
  }
}

let jalanInfo = (param) => {
  let no_jalan = param.feature.getProperty('no_jalan')
  let nama_jalan = param.feature.getProperty('nama_jalan')
  let panjang = param.feature.getProperty('panjang')
  let lebar_rata = param.feature.getProperty('lebar_rata')
  let perkerasan = param.feature.getProperty('perkerasan')
  let kondisi = param.feature.getProperty('kondisi')
  let koordinat_awal = param.feature.getProperty('koordinat_awal')
  let koordinat_akhir = param.feature.getProperty('koordinat_akhir')

  return [
    /*html*/ `
      <tr>
          <td width="25%">NO. RUAS JALAN</td>
          <td width="*" colspan="3">${no_jalan}</td>
      </tr>
    `,
    /*html*/ `
      <tr>
          <td>NAMA RUAS JALAN</td>
          <td colspan="3">${nama_jalan}</td>
      </tr>
    `,
    /*html*/ `
      <tr>
          <td>KOORDINAT AWAL</td>
          <td colspan="3">Lat: ${koordinat_awal[1]} Long: ${koordinat_awal[0]}</td>
      </tr>
    `,
    /*html*/ `
      <tr>
          <td>KOORDINAT AKHIR</td>
          <td colspan="3">Lat: ${koordinat_akhir[1]} Long: ${koordinat_akhir[0]}</td>
      </tr>
    `,
    /*html*/ `
      <tr>
          <td>PANJANG (km)</td>
          <td colspan="3">${panjang}</td>
      </tr>
    `,
    /*html*/ `
      <tr>
          <td>LEBAR RATA-RATA (m)</td>
          <td colspan="3">${lebar_rata}</td>
      </tr>
    `,
    /*html*/ `
      <tr>
          <td align="center" colspan="4">PERMUKAAN</td>
      </tr>
    `,
    /*html*/ `
      <tr>
        <td align="center" width="25%">ASPAL (km)</td>
        <td align="center" width="25%">BETON (km)</td>
        <td align="center" width="25%">KERIKIL (km)</td>
        <td align="center" width="25%">TANAH (km)</td>
      </tr>
    `,
    /*html*/ `
      <tr>
        <td align="center" width="25%">${perkerasan[2]}</td>
        <td align="center" width="25%">${perkerasan[1]}</td>
        <td align="center" width="25%">${perkerasan[3]}</td>
        <td align="center" width="25%">${perkerasan[4]}</td>
      </tr>
    `,
    /*html*/ `
      <tr>
        <td align="center" colspan="4">KONDISI</td>
      </tr>
    `,
    /*html*/ `
      <tr>
        <td align="center" width="25%">BAIK (km)</td>
        <td align="center" width="25%">SEDANG (km)</td>
        <td align="center" width="25%">RUSAK RINGAN (km)</td>
        <td align="center" width="25%">RUSAK BERAT (km)</td>
      </tr>
    `,
    /*html*/ `
      <tr>
        <td align="center" width="25%">${kondisi[1]}</td>
        <td align="center" width="25%">${kondisi[2]}</td>
        <td align="center" width="25%">${kondisi[3]}</td>
        <td align="center" width="25%">${kondisi[4]}</td>
      </tr>
    `,
  ]
}

let segmentInfo = (param) => {
  let no_jalan = param.feature.getProperty('no_jalan')
  let segment = param.feature.getProperty('segment')
  let row = param.feature.getProperty('row')
  let foto = param.feature.getProperty('foto')
  let img =
    foto != null
      ? `<img src="${jalandir}${no_jalan}/${row}/${foto}" width="300px" >`
      : ''

  return [
    /*html*/ `
      <tr>
          <td align="center" colspan="4"><div>SEGMENTASI: ${segment}</div>${img}</td>
      </tr>
      `,
  ]
}

let ujungInfo = (param) => {
  let no_jalan = param.feature.getProperty('no_jalan')
  let row = param.feature.getProperty('row')
  let foto = param.feature.getProperty('foto')
  console.log(row)
  console.log(foto)
  let img =
    foto != null
      ? `<img src="${jalandir}${no_jalan}/${row}/${foto}" width="300px" >`
      : ''
  console.log(img)
  return [
    /*html*/ `
      <tr>
        <td align="center" colspan="4">${img}</td>
      </tr>
    `,
  ]
}

let jembatanInfo = (param) => {
  let row = {
    id: param.feature.getProperty('id') ?? '-',
    no_jembatan: param.feature.getProperty('no_point') ?? '-',
    nama_jembatan: param.feature.getProperty('nama_point') ?? '-',
    nama_jalan: param.feature.getProperty('nama_jalan') ?? '-',
    nama_sungai: param.feature.getProperty('nama_sungai') ?? '-',
    panjang: param.feature.getProperty('panjang') ?? '-',
    lebar: param.feature.getProperty('lebar') ?? '-',
    bentang: param.feature.getProperty('bentang') ?? '-',
    latitude: param.feature.getProperty('latitude') ?? '-',
    longitude: param.feature.getProperty('longitude') ?? '-',
    survei: param.feature.getProperty('survei') ?? '-',
    bms: param.feature.getProperty('bms') ?? '-',
    keterangan: param.feature.getProperty('keterangan') ?? '-',
    tipe_bangunan_atas: param.feature.getProperty('tipe_bangunan_atas') ?? '-',
    bms_bangunan_atas: param.feature.getProperty('bms_bangunan_atas') ?? '-',
    keterangan_bangunan_atas:
      param.feature.getProperty('keterangan_bangunan_atas') ?? '-',
    kondisi_bangunan_atas:
      param.feature.getProperty('kondisi_bangunan_atas') ?? '-',
    foto_bangunan_atas: param.feature.getProperty('foto_bangunan_atas') ?? '-',
    tipe_bangunan_bawah:
      param.feature.getProperty('tipe_bangunan_bawah') ?? '-',
    bms_bangunan_bawah: param.feature.getProperty('bms_bangunan_bawah') ?? '-',
    keterangan_bangunan_bawah:
      param.feature.getProperty('keterangan_bangunan_bawah') ?? '-',
    kondisi_bangunan_bawah:
      param.feature.getProperty('kondisi_bangunan_bawah') ?? '-',
    foto_bangunan_bawah:
      param.feature.getProperty('foto_bangunan_bawah') ?? '-',
    tipe_fondasi: param.feature.getProperty('tipe_fondasi') ?? '-',
    bms_fondasi: param.feature.getProperty('bms_fondasi') ?? '-',
    keterangan_fondasi: param.feature.getProperty('keterangan_fondasi') ?? '-',
    kondisi_fondasi: param.feature.getProperty('kondisi_fondasi') ?? '-',
    foto_fondasi: param.feature.getProperty('foto_fondasi') ?? '-',
    tipe_lantai: param.feature.getProperty('tipe_lantai') ?? '-',
    bms_lantai: param.feature.getProperty('bms_lantai') ?? '-',
    keterangan_lantai: param.feature.getProperty('keterangan_lantai') ?? '-',
    kondisi_lantai: param.feature.getProperty('kondisi_lantai') ?? '-',
    foto_lantai: param.feature.getProperty('foto_lantai') ?? '-',
  }

  let table, rowNoJembatan, rowNamaJembatan, rowUkuran, rowKoordinat, rowSurvei

  table = document.createElement('table')
  table.setAttribute('width', '100%')
  table.id = 'detail-content'
  table.classList.add('table', 'table-bordered', 'table-striped', 'table-sm')

  rowNoJembatan = document.createElement('tr')
  rowNoJembatan.innerHTML = /*html*/ `
    <td width="25%">Nomor Jembatan</td>
    <td width="*">${row.no_jembatan}</td>
  `

  rowNamaJembatan = document.createElement('tr')
  rowNamaJembatan.innerHTML = /*html*/ `
    <td>Nama Jembatan</td>
    <td>${row.nama_jembatan}</td>
  `
  rowUkuran = document.createElement('tr')
  rowUkuran.innerHTML = /*html*/ `
    <td>Ukuran</td>
    <td>Panjang: ${row.panjang} m&nbsp;&nbsp;&nbsp;Lebar: ${row.lebar} m&nbsp;&nbsp;&nbsp;Jml Bentang: ${row.bentang}</td>
  `
  rowKoordinat = document.createElement('tr')
  rowKoordinat.innerHTML = /*html*/ `
    <td>Koordinat</td>
    <td>Lat: ${row.latitude}&nbsp;&nbsp;&nbsp;Long: ${row.longitude}</td>
  `
  rowSurvei = document.createElement('tr')
  rowSurvei.innerHTML = /*html*/ `
    <td>Dokumen Survei</td>
    <td><a href="${server_base}/upload/pdf/jembatan/${row.id}/${
    row.survei
  }" target="_blank">${row.survei ?? '-'}</a></td>
  `

  table.append(
    rowNoJembatan,
    rowNamaJembatan,
    rowUkuran,
    rowKoordinat,
    rowSurvei
  )

  let tab, tabNav, tabContent

  tab = document.createElement('div')

  tabNav = document.createElement('ul')
  tabNav.classList.add('nav', 'nav-tabs')
  tabNav.innerHTML = /*html*/ `
    <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#menu1">Bangunan Atas</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#menu2">Bangunan Bawah</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#menu3">Fondasi</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#menu4">Lantai</a>
    </li>
  `

  tabContent = document.createElement('div')
  tabContent.classList.add('tab-content', 'border', 'border-top-0')

  let menus = [
    {
      tipe: row.tipe_bangunan_atas ?? '-',
      bms: row.bms_bangunan_atas ?? '-',
      keterangan: row.keterangan_bangunan_atas ?? '-',
      kondisi: row.kondisi_bangunan_atas ?? '-',
      foto: row.foto_bangunan_atas ?? '-',
    },
    {
      tipe: row.tipe_bangunan_bawah ?? '-',
      bms: row.bms_bangunan_bawah ?? '-',
      keterangan: row.keterangan_bangunan_bawah ?? '-',
      kondisi: row.kondisi_bangunan_bawah ?? '-',
      foto: row.foto_bangunan_bawah ?? '-',
    },
    {
      tipe: row.tipe_fondasi ?? '-',
      bms: row.bms_fondasi ?? '-',
      keterangan: row.keterangan_fondasi ?? '-',
      kondisi: row.kondisi_fondasi ?? '-',
      foto: row.foto_fondasi ?? '-',
    },
    {
      tipe: row.tipe_lantai ?? '-',
      bms: row.bms_lantai ?? '-',
      keterangan: row.keterangan_lantai ?? '-',
      kondisi: row.kondisi_lantai ?? '-',
      foto: row.foto_lantai ?? '-',
    },
  ]

  let label = {
    tipe: /*html*/ `
      <div class="col-3 d-flex justify-content-between">
        <div>Tipe</div>
        <div>:</div>
      </div>
    `,
    bms: /*html*/ `
      <div class="col-3 d-flex justify-content-between">
        <div>BMS</div>
        <div>:</div>
      </div>
    `,
    keterangan: /*html*/ `
      <div class="col-3 d-flex justify-content-between">
        <div>Keterangan</div>
        <div>:</div>
      </div>
    `,
    kondisi: /*html*/ `
      <div class="col-3 d-flex justify-content-between">
        <div>Kondisi</div>
        <div>:</div>
      </div>
    `,
  }

  menus.forEach((value, idx) => {
    let menu
    menu = document.createElement('div')
    menu.classList.add('container', 'tab-pane', 'p-3')
    menu.id = `menu${idx + 1}`

    if (idx == 0) menu.classList.add('active')
    else menu.classList.add('fade')

    let menuRow
    for (const key in value) {
      if (key == 'foto') continue

      menuRow = document.createElement('div')
      menuRow.classList.add('row')
      menuRow.innerHTML = /*html*/ `
        ${label[key]}
        <div class="col-9">${value[key]}</div>
      `

      menu.append(menuRow)
    }

    menuRow = document.createElement('div')
    menuRow.classList.add('row')
    menuRow.innerHTML = /*html*/ `
      <div class="col-12">
        <div>Foto:</div>
        <div>
          <img src="${server_base}/upload/img/jembatan/${row.id}/${value.foto}" style="width: 600px" />
        </div>
      </div>
    `
    menu.append(menuRow)

    tabContent.append(menu)
  })

  tab.append(tabNav, tabContent)

  let content = document.createElement('div')
  content.append(table, tab)

  return content
}

let positionInfo = () => {
  return [
    /*html*/ `
    <tr>
        <td>Lokasi Anda</td>
    </tr>
    `,
  ]
}

let CompleteLines
let PerkerasanLines
let KondisiLines
let SegmentPoints
let AwalPoints
let AkhirPoints
let JembatanPoints

let DataJalan
let JalanLines
let loadDataJalan = (no_jalan) => {
  map_center = default_center
  // clearJalan()
  DataJalan = getAJAX(`${base_url}/Gis/index/datajalan/${no_jalan}`)
  if (DataJalan.length > 0) {
    DataJalan = JSON.parse(DataJalan)
    loadJalan()
    if (no_jalan != 'semua') setFeatureCenter()
    return true
  }
  return false
}

let clearDataJalan = () => {}

let loadJalan = () => {
  JalanLines = loadData(DataJalan.jalan, 'lines', 'jalan')
}

let clearJalan = () => {
  if (JalanLines !== undefined) {
    JalanLines.setMap(null)

    clearComplete()
    clearPerkerasan()
    clearKondisi()
    clearSegment()
    clearAwal()
    clearAkhir()
    clearJembatan()
  }
}

let loadComplete = () => {
  CompleteLines = loadData(DataJalan.complete, 'lines', 'jalan')
}

let clearComplete = () => {
  if (CompleteLines !== undefined) {
    CompleteLines.setMap(null)
  }
}

let loadPerkerasan = () => {
  JalanLines.setMap(null)
  PerkerasanLines = loadData(DataJalan.perkerasan, 'lines', 'jalan')
}

let clearPerkerasan = () => {
  if (PerkerasanLines !== undefined) {
    PerkerasanLines.setMap(null)
  }
}

let loadKondisi = () => {
  JalanLines.setMap(null)
  KondisiLines = loadData(DataJalan.kondisi, 'lines', 'jalan')
}

let clearKondisi = () => {
  if (KondisiLines !== undefined) {
    KondisiLines.setMap(null)
  }
}

let loadSegment = () => {
  let icon = {
    url: `${server_base}/assets/img/circle.png`,
    scaledSize: new google.maps.Size(10, 10),
    anchor: new google.maps.Point(5, 5),
  }

  SegmentPoints = loadData(DataJalan.segment, 'points', 'segment', icon)
}

let clearSegment = () => {
  if (SegmentPoints !== undefined) {
    SegmentPoints.setMap(null)
  }
}

let loadAwal = () => {
  let icon = {
    url: `${server_base}/assets/img/triangle.png`,
    scaledSize: new google.maps.Size(10, 10),
    anchor: new google.maps.Point(5, 5),
  }

  AwalPoints = loadData(DataJalan.awal, 'points', 'awal', icon)
}

let clearAwal = () => {
  if (AwalPoints !== undefined) {
    AwalPoints.setMap(null)
  }
}

let loadAkhir = () => {
  let icon = {
    url: `${server_base}/assets/img/rhombus.png`,
    scaledSize: new google.maps.Size(10, 10),
    anchor: new google.maps.Point(5, 5),
  }

  AkhirPoints = loadData(DataJalan.akhir, 'points', 'akhir', icon)
}

let clearAkhir = () => {
  if (AkhirPoints !== undefined) {
    AkhirPoints.setMap(null)
  }
}

let loadJembatan = () => {
  let icon = {
    url: `${server_base}/assets/img/bridge.png`,
    scaledSize: new google.maps.Size(10, 10),
    anchor: new google.maps.Point(5, 5),
  }

  JembatanPoints = loadData(DataJalan.jembatan, 'points', 'jembatan', icon)
}

let clearJembatan = () => {
  if (JembatanPoints !== undefined) {
    JembatanPoints.setMap(null)
  }
}

let BatasLines
let loadBatas = () => {
  map_data = `${server_base}/data/Batas.json?t=${cur_time}`
  BatasLines = loadData(map_data, 'border', 'batas')
}

let setFeatureCenter = () => {
  coordinates = DataJalan.jalan.geometry.coordinates

  // put all latitudes and longitudes in arrays
  let long = []
  let lat = []
  coordinates.forEach((i) => {
    long.push(i[0])
    lat.push(i[1])
  })

  // sort the arrays low to high
  lat.sort()
  long.sort()

  // get the min and max of each
  const lowX = lat[0]
  const highX = lat[lat.length - 1]
  const lowy = long[0]
  const highy = long[lat.length - 1]

  // center of the polygon is the starting point plus the midpoint
  const centerX = lowX + (highX - lowX) / 2
  const centerY = lowy + (highy - lowy) / 2

  map_center = { lat: centerX, lng: centerY }
  map.setCenter(map_center)
}

let PositionPoint
let positionLat = null,
  positionLng = null

let loadPosition = () => {
  // setLocation();
  marker = new google.maps.Marker({ map })

  const userPosition = { lat: Number(positionLat), lng: Number(positionLng) }
  console.log(userPosition)
  marker.setPosition(userPosition)
  map.panTo(userPosition)
}

let getLocation = () => {
  trackLocation({
    onSuccess: ({ coords: { latitude: lat, longitude: lng } }) => {
      positionLat = lat
      positionLng = lng
      $('#yourLocation').prop('disabled', false)
      document.getElementById('track-error').innerHTML = ''
    },
    onError: (err) => {
      errorLocation(err)
      navigator.geolocation.clearWatch(track)
    },
  })
}

let setLocation = () => {
  getPosition({
    onSuccess: ({ coords: { latitude: lat, longitude: lng } }) => {
      positionLat = lat
      positionLng = lng
      $('#yourLocation').prop('disabled', false)
      document.getElementById('track-error').innerHTML = ''
    },
    onError: (err) => {
      errorLocation(err)
    },
  })
}

let errorLocation = (err) => {
  // alert(getPositionErrorMessage(err.code) || err.message);
  document.getElementById('track-error').innerHTML =
    getPositionErrorMessage(err.code) || err.message
  // $("#yourLocation").prop("disabled", true);
}

const getPosition = ({ onSuccess, onError = () => {} }) => {
  // Omitted for brevity
  return navigator.geolocation.getCurrentPosition(onSuccess, onError, {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0,
  })
}

let track
const trackLocation = ({ onSuccess, onError = () => {} }) => {
  // Omitted for brevity
  track = navigator.geolocation.watchPosition(onSuccess, onError, {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0,
  })
}

let clearPosition = () => {
  if (PositionPoint !== undefined) {
    PositionPoint.setMap(null)
  }
}

let getOriginDestination = () => {
  return [
    [positionLng, positionLat], // ? Origin
    DataJalan.awal.features[0].geometry.coordinates, // ? Destination
  ]
}

let directionsService = new google.maps.DirectionsService()
let directionsRenderer = new google.maps.DirectionsRenderer({
  suppressMarkers: true,
})

let polyline = null
let poly2 = null
let steps = []
let marker = null

function createMarker(latlng, label, html) {
  // alert("createMarker("+latlng+","+label+","+html+","+color+")");
  var contentString = '<b>' + label + '</b><br>' + html
  var marker = new google.maps.Marker({
    position: latlng,
    map: map,
    title: label,
    zIndex: Math.round(latlng.lat() * -100000) << 5,
  })
  marker.myname = label
  // gmarkers.push(marker);

  google.maps.event.addListener(marker, 'click', function () {
    infowindow.setContent(contentString)
    infowindow.open(map, marker)
  })
  return marker
}

let calcRoute = () => {
  const [start, end] = getOriginDestination()

  polyline = new google.maps.Polyline({
    path: [],
    strokeColor: '#FF0000',
    strokeWeight: 3,
  })

  poly2 = new google.maps.Polyline({
    path: [],
    strokeColor: '#FF0000',
    strokeWeight: 3,
  })

  let request = {
    origin: new google.maps.LatLng(start[1], start[0]),
    destination: new google.maps.LatLng(end[1], end[0]),
    travelMode: 'DRIVING',
  }

  directionsRenderer.setMap(map)

  directionsService.route(request, (result, status) => {
    if (status == 'OK') {
      directionsRenderer.setDirections(result)

      startLocation = new Object()
      endLocation = new Object()

      var legs = result.routes[0].legs

      for (i = 0; i < legs.length; i++) {
        if (i == 0) {
          startLocation.latlng = legs[i].start_location
          startLocation.address = legs[i].start_address
          // marker = google.maps.Marker({map:map,position: startLocation.latlng});
          marker = createMarker(
            legs[i].start_location,
            'start',
            legs[i].start_address,
            'green'
          )
        }

        endLocation.latlng = legs[i].end_location

        var steps = legs[i].steps

        for (j = 0; j < steps.length; j++) {
          var nextSegment = steps[j].path

          for (k = 0; k < nextSegment.length; k++) {
            polyline.getPath().push(nextSegment[k])
            bounds.extend(nextSegment[k])
          }
        }
      }
    }
  })
}

let clearRoute = () => {
  directionsRenderer.setMap(null)
}

let step = 50 // 5; // metres
let tick = 100 // milliseconds
let eol
let k = 0
let stepnum = 0
let speed = ''
let lastVertex = 1

let updatePoly = (d) => {
  // Spawn a new polyline every 20 vertices, because updating a 100-vertex poly is too slow
  if (poly2.getPath().getLength() > 20) {
    poly2 = new google.maps.Polyline([polyline.getPath().getAt(lastVertex - 1)])
    // map.addOverlay(poly2)
  }

  if (polyline.GetIndexAtDistance(d) < lastVertex + 2) {
    if (poly2.getPath().getLength() > 1) {
      poly2.getPath().removeAt(poly2.getPath().getLength() - 1)
    }
    poly2
      .getPath()
      .insertAt(poly2.getPath().getLength(), polyline.GetPointAtDistance(d))
  } else {
    poly2.getPath().insertAt(poly2.getPath().getLength(), endLocation.latlng)
  }
}

let animate = (d) => {
  if (d > eol) {
    map.panTo(endLocation.latlng)
    marker.setPosition(endLocation.latlng)
    return
  }

  var p = polyline.GetPointAtDistance(d)
  map.panTo(p)
  marker.setPosition(p)
  updatePoly(d)
  timerHandle = setTimeout('animate(' + (d + step) + ')', tick)
}

let startAnimation = () => {
  eol = polyline.Distance()
  map.setCenter(polyline.getPath().getAt(0))
  poly2 = new google.maps.Polyline({
    path: [polyline.getPath().getAt(0)],
    strokeColor: '#0000FF',
    strokeWeight: 10,
  })
  setTimeout('animate(50)', 2000) // Allow time for the initial map display
}
