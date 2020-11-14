<?php

/**
 * * app/controllers/FileHandler.php
 */
class FileHandler
{
  /**
   * * FileHandler::Upload()
   * ? Upload File ke Temporary directory
   */
  public static function Upload()
  {
    // ? Temporary file name
    $temp = $_FILES['file']['tmp_name'];

    // ? File name
    $filename = $_FILES['file']['name'];

    // ? File location
    $location = TEMP_UPLOAD_DIR . $filename;

    // ? Source
    $source = TEMP_UPLOAD_URL . $filename;

    // TODO: Get mime type
    list($type, $extension) = explode("/", mime_content_type($temp));

    // * Allowed type & extension
    $allowed_type = ['video', 'image'];
    $allowed_extension = ['pdf', 'xml'];

    // TODO: Get accepted file type & extension
    list($accept_type, $accept_extension) = explode("/", str_replace('kml', 'xml', $_POST['accept']));

    $upload = false;

    // TODO: Cek extension
    if (in_array($extension, $allowed_extension) && $accept_extension == $extension) { // ? Extension cocok
      $upload = true;
    } else { // ! extension tidak cocok
      // TODO: Cek type
      if (in_array($type, $allowed_type) && in_array($accept_type, $allowed_type)) { // ? Type cocok
        $upload = true;
      }
    }

    // TODO: Cek Upload OK
    if ($upload) { // ? Upload OK
      // TODO: Upload File
      if (move_uploaded_file($temp, $location)) { // ? Upload Success
        Functions::setDataSession('alert', ["Your file is temporarily uploaded.", 'warning']);
      } else { // ! Upload Gagal
        Functions::setDataSession('alert', ["Nothing file uploaded.", 'danger']);
      }
    } else { // ! File tidak cocok
      Functions::setDataSession('alert', ["File is not allowed.", 'danger']);
    }

    $alert = Functions::getDataSession('alert');

    // * Mengembalikan nilai result
    $result = [];
    $result['alert'] = $alert;
    $result['location'] = $location;
    $result['filename'] = $filename;
    $result['source'] = $source;
    $result['filetype'] = $type;
    echo json_encode($result);
  }

  /**
   * * FileHandler::MoveFromTemp($filedir, $filename)
   * ? Pindah file dari temporary directory
   * @param string filedir
   * ? Directory tujuan
   * @param string filedir
   * ? Nama file
   * @param bool $timestamp
   */
  public static function MoveFromTemp(string $filedir, string $filename, bool $timestamp = false, bool $clear = false)
  {
    // TODO: Cek file ada di temporary directory
    if (file_exists(TEMP_UPLOAD_DIR . $filename)) { // ? file ada
      // TODO: Parsing directory
      $dir = [];
      foreach (explode("/", $filedir) as $folder) {
        $dir[] = $folder;
        $new_dir = UPLOAD_DIR . implode("/", $dir);
        // echo $new_dir . "<br>";
        // TODO: Membuat directory baru jika belum ada
        self::createWritableFolder($new_dir);
      }

      $time = ($timestamp == true) ? date('ymdHis', time()) . "_" : '';

      if ($clear) {
        // var_dump($clear);
        self::clearOldFile($filedir);
      }

      // TODO: Pindah file
      rename(TEMP_UPLOAD_DIR . $filename, UPLOAD_DIR . "{$filedir}/{$time}{$filename}");
      // if (file_exists(UPLOAD_DIR . "{$filedir}/{$time}{$filename}")) {
      //   echo UPLOAD_DIR . "{$filedir}/{$time}{$filename}" . 'moved <br>';
      // } else {
      //   echo UPLOAD_DIR . "{$filedir}/{$time}{$filename}" . 'not moved <br>';
      // }
    }
  }

  public static function clearOldFile(string $filedir)
  {
    // var_dump($filedir);
    $files = glob(UPLOAD_DIR . "{$filedir}/*"); // ?  get all file names
    // var_dump($files);
    foreach ($files as $file) { // ?  iterate files
      // var_dump($file);
      if (is_file($file))
        @unlink($file); // TODO: delete file
    }
  }

  public static function createWritableFolder(string $folder)
  {
    // $folder = $folder;
    // if ($folder != '.' && $folder != '/') {
    //     self::createWritableFolder(dirname($folder));
    // }
    // TODO: Cek folder exist
    if (file_exists($folder)) { // ! Folder exist
      return is_writable($folder);
    }

    // TODO: Buat folder baru
    @mkdir($folder, 0777, true);
  }

  public static function checkUploadedFile()
  {
    $filepath = $_POST['filepath'];
    list($file, $status) = self::checkFileExist($filepath);

    $result = [];
    $result['status'] = $status;
    $result['file'] = $file;
    echo json_encode($result);
    exit;
  }

  public static function checkFileExist($filepath)
  {
    list($filename) = array_reverse(explode("/", $filepath));

    $status = 404;
    $file = '';
    if (file_exists(UPLOAD_DIR . $filepath)) {
      $status = 200;
      $file = UPLOAD_URL . $filepath;
    } else {
      if (file_exists(TEMP_UPLOAD_DIR . $filename)) {
        $status = 200;
        $file = TEMP_UPLOAD_URL . $filename;
      }
    }

    return [$file, $status];
  }
}
