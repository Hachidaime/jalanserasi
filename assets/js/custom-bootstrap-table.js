/**
 * * assets/js/custom-bootstrap-table.js
 * ? Javascript untuk handle Bootstrap Table
 */

/**
 * * Mendefinisikan bariable
 */
let $table = $('.bootstrap-table')
let selections = []

/**
 * * Function ready
 */
$(function () {
  // TODO: Inisiasi table
  initTable()
  // * Table Options
  $table.addClass('table-sm')

  // * Click Add button
  $('#add').on('click', function () {
    window.location.href = `${base_url}/${controller}/${method}/add`
  })
})

/* Bootstrap Table */
function getIdSelections() {
  return $.map($table.bootstrapTable('getSelections'), function (row) {
    return row.id
  })
}

function responseHandler(res) {
  $.each(res.rows, function (i, row) {
    row.state = $.inArray(row.id, selections) !== -1
  })
  return res
}

function detailFormatter(index, row) {
  let html = []
  $.each(row, function (key, value) {
    html.push(/*html*/ `<p><b>${key}:</b> ${value}</p>`)
  })
  return html.join('')
}

function operateFormatter(value, row, index) {
  return [
    /*html*/ `<a class="edit" href="javascript:void(0)" title="Edit">`,
    /*html*/ `<i class="fas fa-edit text-warning"></i>`,
    /*html*/ `</a>  `,
    /*html*/ `<a class="remove" href="javascript:void(0)" title="Remove">`,
    /*html*/ `<i class="fas fa-trash text-danger"></i>`,
    /*html*/ `</a>`,
  ].join('')
}

function viewFormatter(value, row, index) {
  return [
    /*html*/ `<a class="info" href="javascript:void(0)" title="Info">`,
    /*html*/ `<i class="fas fa-clipboard text-info"></i>`,
    /*html*/ `</a>  `,
  ].join('')
}

function viewPrintFormatter(value, row, index) {
  return [
    /*html*/ `<a class="info" href="javascript:void(0)" title="Info">`,
    /*html*/ `<i class="fas fa-clipboard text-info"></i>`,
    /*html*/ `</a>  `,
    /*html*/ `<a class="print" href="javascript:void(0)" title="Cetak">`,
    /*html*/ `<i class="fas fa-print text-warning"></i>`,
    /*html*/ `</a>  `,
  ].join('')
}

function viewJembatanFormatter(value, row, index) {
  return [
    /*html*/ `<a class="info" href="javascript:void(0)" title="Info">`,
    /*html*/ `<i class="fas fa-clipboard text-info"></i>`,
    /*html*/ `</a>`,
  ].join('')
}

function viewEditFormatter(value, row, index) {
  return [
    /*html*/ `<a class="info" href="javascript:void(0)" title="Info">`,
    /*html*/ `<i class="fas fa-clipboard text-info"></i>`,
    /*html*/ `</a>  `,
    /*html*/ `<a class="edit" href="javascript:void(0)" title="Edit">`,
    /*html*/ `<i class="fas fa-edit text-warning"></i>`,
    /*html*/ `</a>  `,
  ].join('')
}

function coordFormatter(value, row, index) {
  return [
    /*html*/ `<a class="coord" href="javascript:void(0)" title="Edit Koordinat">`,
    /*html*/ `<i class="fas fa-edit text-warning"></i>`,
    /*html*/ `</a>  `,
  ].join('')
}

function rowStyle(row, index) {
  if (row.segment > 0) {
    return {
      classes: 'bg-secondary',
    }
  }
  if (row.new == true) {
    return {
      classes: 'bg-danger',
    }
  }
  return 'bg-light'
}

window.operateEvents = {
  'click .edit': function (e, value, row, index) {
    window.location.href = `${base_url}/${controller}/${method}/edit/${row.id}`
  },
  'click .remove': function (e, value, row, index) {
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-success mx-1',
        cancelButton: 'btn btn-danger mx-1',
      },
      buttonsStyling: false,
    })

    swalWithBootstrapButtons
      .fire({
        title: 'Are you sure?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true,
        allowOutsideClick: false,
      })
      .then((result) => {
        if (result.value) {
          let url = `${base_url}/${controller}/${method}/remove`
          $.post(url, `id=${row.id}`, function (data) {
            makeAlert(data)
            $table.bootstrapTable('refresh')
          })
        }
      })
  },
}

window.viewEvents = {
  'click .info': function (e, value, row, index) {
    let modal = $('#myModal')
    modal.modal('show')

    let title = $table.data('title')
    modal.find('.modal-title').text(`Detail ${title}`)
    modal.find('.modal-body').html(row.view)
  },
}

window.viewPrintEvents = {
  'click .info': function (e, value, row, index) {
    let modal = $('#myModal')
    modal.modal('show')

    let title = $table.data('title')
    modal.find('.modal-title').text(`Detail ${title}`)

    setDataJalanModal(row, modal)
  },

  'click .print': function (e, value, row, index) {
    let modal = $('#myModal')

    printAnyMaps(row, modal)
  },
}

// printAnyMaps ::
function printAnyMaps(row, modal) {
  setDataJalanModal(row, modal)
  const $body = $('body')
  const $mapContainer = $('.modal-body')
  const $mapContainerParent = $mapContainer.parent()
  const $printContainer = $('<div style="position:relative;">')

  $printContainer
    .height($mapContainer.height())
    .append($mapContainer)
    .prependTo($body)

  const $content = $body.children().not($printContainer).not('script').detach()

  /**
   * Needed for those who use Bootstrap 3.x, because some of
   * its `@media print` styles ain't play nicely when printing.
   */
  const $patchedStyle = $('<style media="print">')
    .text(
      `
      img { max-width: none !important; }
      a[href]:after { content: ""; }
    `
    )
    .appendTo('head')

  window.print()

  $body.prepend($content)
  $mapContainerParent.prepend($mapContainer)

  $printContainer.remove()
  $patchedStyle.remove()
}

let setDataJalanModal = (row, modal) => {
  let table,
    rowNoJalan,
    rowNamaJalan,
    rowKoordinatAwal,
    rowKoordinatAkhir,
    rowPermukaan,
    rowPermukaanLabel,
    rowPermukaanValue,
    rowKondisi,
    rowKondisiLabel,
    rowKondisiValue,
    rowMap,
    rowJmlJembatan

  table = document.createElement('table')
  table.setAttribute('width', '100%')
  table.id = 'detail-content'
  table.classList.add('table')
  table.classList.add('table-bordered')
  table.classList.add('table-striped')
  table.classList.add('table-sm')
  table.classList.add('is-print')

  rowNoJalan = document.createElement('tr')
  rowNamaJalan = document.createElement('tr')
  rowKoordinatAwal = document.createElement('tr')
  rowKoordinatAkhir = document.createElement('tr')

  rowPermukaan = document.createElement('tr')
  rowPermukaan.innerHTML = /* html */ `
      <td align="center" colspan="4">PERMUKAAN</td>
      `
  rowPermukaanLabel = document.createElement('tr')
  rowPermukaanLabel.innerHTML = /* html */ `
      <td align="center" width="25%">ASPAL (km)</td>
      <td align="center" width="25%">BETON (km)</td>
      <td align="center" width="25%">KERIKIL (km)</td>
      <td align="center" width="25%">TANAH (km)</td>
      `
  rowPermukaanValue = document.createElement('tr')

  rowKondisi = document.createElement('tr')
  rowKondisi.innerHTML = /* html */ `
      <td align="center" colspan="4">KONDISI</td>
      `
  rowKondisiLabel = document.createElement('tr')
  rowKondisiLabel.innerHTML = /* html */ `
      <td align="center" width="25%">BAIK (km)</td>
      <td align="center" width="25%">SEDANG (km)</td>
      <td align="center" width="25%">RUSAK RINGAN (km)</td>
      <td align="center" width="25%">RUSAK BERAT (km)</td>
      `
  rowKondisiValue = document.createElement('tr')

  rowMap = document.createElement('tr')
  rowMap.innerHTML = /* html */ `
      <td align="center" colspan="4">
        <div class="kotakpeta">
          <div id="map_canvas" style="width: 600px"></div>
        </div>
      </td>
      `

  rowJmlJembatan = document.createElement('tr')

  table.append(
    rowNoJalan,
    rowNamaJalan,
    rowKoordinatAwal,
    rowKoordinatAkhir,
    rowPermukaan,
    rowPermukaanLabel,
    rowPermukaanValue,
    rowKondisi,
    rowKondisiLabel,
    rowKondisiValue,
    rowMap,
    rowJmlJembatan
  )

  modal.find('.modal-body').html(table)
  initMap2()

  loadDataJalan(row.no_jalan)
  loadKondisi()
  loadAwal()
  loadAkhir()

  let jalan = DataJalan.jalan.properties

  rowNoJalan.innerHTML = /* html */ `
      <td>NO. RUAS JALAN</td>
      <td colspan="3">${jalan.no_jalan}</td>
      `
  rowNamaJalan.innerHTML = /* html */ `
      <td>NAMA RUAS JALAN</td>
      <td colspan="3">${jalan.nama_jalan}</td>
      `
  rowKoordinatAwal.innerHTML = /* html */ `
      <td>KOORDINAT AWAL</td>
      <td colspan="3">Lat: ${jalan.koordinat_awal[1]} Long: ${jalan.koordinat_awal[0]} </td>
      `
  rowKoordinatAkhir.innerHTML = /* html */ `
      <td>KOORDINAT AKHIR</td>
      <td colspan="3">Lat: ${jalan.koordinat_akhir[1]} Long: ${jalan.koordinat_akhir[0]} </td>
      `
  rowPermukaanValue.innerHTML = /* html */ `
      <td align="center" width="25%">${jalan.perkerasan[2]}</td>
      <td align="center" width="25%">${jalan.perkerasan[1]}</td>
      <td align="center" width="25%">${jalan.perkerasan[3]}</td>
      <td align="center" width="25%">${jalan.perkerasan[4]}</td>
      `
  rowKondisiValue.innerHTML = /* html */ `
      <td align="center" width="25%">${jalan.kondisi[1]}</td>
      <td align="center" width="25%">${jalan.kondisi[2]}</td>
      <td align="center" width="25%">${jalan.kondisi[3]}</td>
      <td align="center" width="25%">${jalan.kondisi[4]}</td>
      `
  rowJmlJembatan.innerHTML = /* html */ `
      <td>JML JEMBATAN</td>
      <td colspan="3">${jalan.jml_jembatan}</td>
      `

  infowindow = false
}

window.viewJembatanEvents = {
  'click .info': function (e, value, row, index) {
    let modal = $('#myModal')
    modal.modal('show')

    let title = $table.data('title')
    modal.find('.modal-title').text(`Detail ${title}`)

    setDataJembatanModal(row, modal)
  },
}

let setDataJembatanModal = (row, modal) => {
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
    <td>${row.survei}</td>
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
      tipe: row.tipe_bangunan_atas,
      bms: row.bms_bangunan_atas,
      keterangan: row.keterangan_bangunan_atas,
      kondisi: row.kondisi_bangunan_atas,
      foto: row.foto_bangunan_atas,
    },
    {
      tipe: row.tipe_bangunan_bawah,
      bms: row.bms_bangunan_bawah,
      keterangan: row.keterangan_bangunan_bawah,
      kondisi: row.kondisi_bangunan_bawah,
      foto: row.foto_bangunan_bawah,
    },
    {
      tipe: row.tipe_fondasi,
      bms: row.bms_fondasi,
      keterangan: row.keterangan_fondasi,
      kondisi: row.kondisi_fondasi,
      foto: row.foto_fondasi,
    },
    {
      tipe: row.tipe_lantai,
      bms: row.bms_lantai,
      keterangan: row.keterangan_lantai,
      kondisi: row.kondisi_lantai,
      foto: row.foto_lantai,
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

  let modalContent = document.createElement('div')
  modalContent.append(table, tab)

  modal.find('.modal-body').html(modalContent)
}

window.viewEditEvents = {
  'click .info': function (e, value, row, index) {
    let modal = $('#myModal')
    modal.modal('show')

    let title = $table.data('title')
    modal.find('.modal-title').text(`Detail ${title}`)
    modal.find('.modal-body').html(row.view)
  },
  'click .edit': function (e, value, row, index) {
    window.location.href = `${base_url}/${controller}/${method}/edit/${row.id}`
  },
}

window.coordEvents = {
  'click .coord': function (e, value, row, index) {
    let no_jalan = $('.myForm').find('#no_jalan').val()

    let modal = $('#koordinatModal')
    modal.modal('show')
    modal.find('.modal-title').text(`Edit Koordinat #${row.row}`)

    let selectinput = ['perkerasan', 'kondisi']

    let myForm = $('.koordinatForm')
    $.each(row, function (k, v) {
      if (selectinput.includes(k)) {
        // console.log(k);
        if (v != null || v != 'undefined') {
          myForm.find(`#${k}`).val(v)
          myForm.find(`#${k}`).selectpicker('refresh')
        } else {
          myForm.find(`#${k}`).val('0')
          myForm.find(`#${k}`).selectpicker('refresh')
        }
      } else if (k == 'foto' && v != null) {
        let url = `${base_url}/FileHandler/checkUploadedFile`
        let foto_url = `img/jalan/${no_jalan}/${row.row}/${v}`
        let params = {}
        params['filepath'] = foto_url

        $.post(
          url,
          params,
          function (data) {
            if (data.status == 200) {
              myForm.find(`#${k}`).val(v)
              let preview = myForm.find('#previewfoto')
              preview.show()
              preview.find('a').attr('href', data.file)
              preview.find('img').attr('src', data.file)

              let file_action = myForm.find('#file-actionfoto')
              file_action.show()
              file_action.find('span.filename').text(v)
              file_action.find('a').attr('href', data.file)
            }
          },
          'json'
        )
      } else {
        myForm.find(`#${k}`).val(v)
      }
    })
    myForm.find(`#index`).val(row.row - 1)
    myForm.find(`#tag`).val('edit')
  },
}

function initTable() {
  $table.bootstrapTable('destroy').bootstrapTable({
    exportDataType: 'all',
    exportTypes: ['csv', 'excel', 'pdf'],
    exportOptions: {
      jspdf: { orientation: 'l' },
    },
    forceExport: true,
  })

  $table.on(
    'check.bs.table uncheck.bs.table ' +
      'check-all.bs.table uncheck-all.bs.table',
    function () {
      $remove.prop('disabled', !$table.bootstrapTable('getSelections').length)

      // save your data, here just save the current page
      selections = getIdSelections()
      // push or splice the selections if you want to save all data selections
    }
  )
  $table.on('all.bs.table', function (e, name, args) {
    // console.log(name, args)
  })
}
