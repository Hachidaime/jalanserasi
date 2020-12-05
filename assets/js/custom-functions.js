/**
 * * Show PopUp Image
 * @param {*} data
 * ? selector image
 */
let showImage = (param) => {
  /**
   * * Mendefinisikan variable
   */
  let data = $(param)
  let img_source = data.attr('src')
  let judul = data.data('judul')
  let tanggal = data.data('tanggal')
  let lightboxModal = $('#lightboxModal')

  // TODO: Set variable
  lightboxModal.find('img').attr('src', img_source)
  lightboxModal.find('#data-judul').text(judul)
  lightboxModal.find('#data-tanggal').text(tanggal)
}

/**
 * * Menampilkan Alert
 * @param {*} object data
 * ? MultiArray [type, message]
 * @param {*} string type
 * ? Type Alert: Primary, Success, Danger, Warning, Secondary, Info
 * @param {*} array message
 * ? Pesan Alert
 */
let makeAlert = (data) => {
  let totalAlert = Object.keys(data).length

  if (totalAlert > 1) {
    let queue = []
    let steps = []
    let n = 1
    $.each(data, (type, message) => {
      let icon = type.replace('danger', 'error')
      let msg = {}
      msg.icon = icon
      msg.html = message.join('<br>')
      msg.customClass = { content: `text-${type}` }

      queue.push(msg)
      steps.push(n)
      n++
    })

    Swal.mixin({
      confirmButtonText: 'Next &rarr;',
      showCancelButton: true,
      progressSteps: steps,
    }).queue(queue)
  } else {
    $.each(data, (type, message) => {
      let icon = type.replace('danger', 'error')
      Swal.fire({
        position: 'center',
        icon: icon,
        html: message.join('<br>'),
        customClass: {
          content: `text-${type}`,
        },
        showConfirmButton: false,
      })
    })
  }
}

/**
 * * Snackbar
 * @param {*} param
 * ? Snackbar content
 */
let snackbar = (param = null) => {
  /**
   * * Mendefinisikan variable
   */
  let snack = $('.snackbar')

  // TODO: Menampilkan Snackbar
  snack.fadeIn().html(param)

  // TODO: Menyembunyikan Snackbar
  setTimeout(() => {
    snack.hide().html('')
  }, 3000)
}

/**
 * * Gallery Pagination
 * @param {*} {*} page
 * ? page
 */
let loadGallery = (page = 1) => {
  let url = `${base_url}/Gallery/index/search`
  let params = {}
  params['page'] = page

  $.post(
    url,
    $.param(params),
    (data) => {
      $('#gallery-item').html(data.item)
    },
    'json'
  )
}

let scrollFunction = () => {
  let title_wrapper = $('.title-wrapper')
  let header_height = $('#header-img').height()
  if (
    document.body.scrollTop > header_height ||
    document.documentElement.scrollTop > header_height
  ) {
    title_wrapper.removeClass('h1').addClass('h3')
    title_wrapper
      .parent()
      .addClass('bg-primary text-light')
      .removeClass('bg-light')
  } else {
    title_wrapper.removeClass('h3').addClass('h1')
    title_wrapper
      .parent()
      .removeClass('bg-primary text-light')
      .addClass('bg-light')
  }
}

/**
 * * Submit Login Form
 */
let login = () => {
  /**
   * * Mendefinisikan variable
   */
  let params = $('#loginForm').serialize()
  let url = `${base_url}/Session/login`

  // TODO: Ajax Request
  $.post(
    url,
    params,
    (data) => {
      // TODO: Menampilkan Alert
      makeAlert(data)

      // TODO: Cek login success
      if (Object.keys(data)[0] == 'success') {
        // TODO: Redirect ke halaman Admin
        setTimeout(() => {
          window.location.href = `${base_url}/Admin`
        }, 3000)
      }
    },
    'json'
  )
}

/**
 * * Log Out
 */
let logout = () => {
  /**
   * * Mendefinisikan variable
   */
  let url = `${base_url}/Session/logout`

  // TODO: Ajax Request
  $.post(
    url,
    (data) => {
      // TODO: Menampilkan Alert
      makeAlert(data)

      // TODO: Cek logout success
      if (Object.keys(data)[0] == 'success') {
        // TODO: Redirect ke halaman Log In
        setTimeout(() => {
          window.location.href = server_base
        }, 3000)
      }
    },
    'json'
  )
}

/**
 * * Set Active System
 * @param {*} id
 * ? system_id
 */
let setMenu = (id) => {
  let params = 'id=' + id
  let url = `${base_url}/Session/setMenu`
  $.post(
    url,
    params,
    (data) => {
      if (data == 1) window.location.href = base_url
    },
    'json'
  )
}

let clearKoordinatModal = () => {
  let myForm = $('.koordinatForm')
  myForm[0].reset()
  myForm.find('.selectpicker').val(0)
  myForm.find('.selectpicker').selectpicker('refresh')
  myForm.find('input').val('')

  let preview = myForm.find('#previewfoto')
  preview.hide()
  preview.find('a').attr('href', '')
  preview.find('img').attr('src', '')

  let file_action = myForm.find('#file-actionfoto')
  file_action.hide()
  file_action.find('span.filename').text('')
  file_action.find('img').attr('href', '')
}

let clearAddKoordinatModal = () => {
  let myForm = $('.addKoordinatForm')
  myForm[0].reset()
}

let width = () => $(window).width()

let openNav = () => $('#mySidepanel').show()

let closeNav = () => $('#mySidepanel').hide()

let getAJAX = (url) => {
  var request = new XMLHttpRequest()
  request.open('GET', url, false)
  request.send(null)
  return request.responseText
}

let getPanjangJalan = () => {
  let url = $table.bootstrapTable('getOptions').url
  url = url.replace('search', 'detail')
  let coordinates = JSON.parse(getAJAX(url))

  let perkerasan = [0, 0, 0, 0, 0]
  let kondisi = [0, 0, 0, 0, 0]

  for (const [type, value] of Object.entries(coordinates)) {
    for (const [val, row] of Object.entries(value)) {
      for (const [idx, points] of Object.entries(row)) {
        switch (type) {
          case 'perkerasan':
            perkerasan[val] += countLength(makePath(points))
            break
          case 'kondisi':
            kondisi[val] += countLength(makePath(points))
            break
        }
      }
    }
  }

  let result = []
  result['perkerasan'] = perkerasan
  result['kondisi'] = kondisi
  result = Object.assign({}, result)
  return result
}

let getTime = () => {
  let d = new Date()
  return d.getTime()
}

let downloadXlsx = () => {
  var FormatsTable = document.getElementById('laporan-table')
  $(FormatsTable).tableExport({
    formats: ['xlsx'],
  })
}

const userPosition = ({ onSuccess, onError = () => {} }) => {
  // Omitted for brevity
  return navigator.geolocation.getCurrentPosition(onSuccess, onError, {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0,
  })
}

const getPositionErrorMessage = (code) => {
  let msg
  switch (code) {
    case 1:
      msg = 'Permission denied.'
      break
    case 2:
      msg = 'Position unavailable.'
      break
    case 3:
      msg = 'Timeout reached.'
      break
  }

  msg += '\nPlease check your browser location permission.'
  msg += '\nTry again later.'

  return msg
}

let setKondisi = (sdi) => {
  let myForm = $('.koordinatForm')

  sdi < 50
    ? myForm.find('#kondisi').val(1)
    : sdi >= 50 && sdi <= 100
    ? myForm.find('#kondisi').val(2)
    : sdi >= 101 && sdi <= 150
    ? myForm.find('#kondisi').val(3)
    : sdi > 150
    ? myForm.find('#kondisi').val(4)
    : myForm.find('#kondisi').val(0)
  myForm.find('#kondisi').selectpicker('refresh')
}

let getBms = (bms) => {
  switch (bms) {
    case '0':
      return 'Jembatan baru tanpa rusak'
      break
    case '1':
      return 'Kerusakan kecil'
      break
    case '2':
      return 'Kerusakan yang memerlukan pemantauan atau pemeliharan diwaktu mendatang'
      break
    case '3':
      return 'Kerusakan yang memerlukan tindakan secepatnya'
      break
    case '4':
      return 'Kondisi kritis'
      break
    case '5':
      return 'Elemen jembatan tidak berfungsi lagi'
      break
    default:
      return ''
      break
  }
}
