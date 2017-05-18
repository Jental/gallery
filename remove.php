<?php

if (!isset($_REQUEST['file'])) {
  echo '{ "error" : "No file specified" }';
  return;
}

$fileName = $_REQUEST['file'];

$REL_DIR = '../';
$PREVIEWS_DIR = '../previews/';
$TRASH_DIR = '../trashbin/';

$baseDir = dirname(__FILE__) . '/' . $REL_DIR;
$trashDir = dirname(__FILE__) . '/' . $TRASH_DIR;

if (!file_exists($baseDir . '/' . $fileName)) {
  echo '{ "error" : "No file found" }';
  return;
}

$res = rename($baseDir . $fileName, $trashDir . $fileName);

if (!$res) {
  echo '{ "error" : "Failed to delete" }';
  return;
}

$previewFilePath = dirname(__FILE__) . '/' . $PREVIEWS_DIR . $fileName;
if (file_exists($previewFilePath)) {
  $res = unlink($previewFilePath);
  if (!$res) {
    echo '{ "error" : "Failed to delete the preview" }';
    return;
  }
}

echo '{ "result" : "ok" }';

?>
