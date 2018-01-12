<?php

if (!isset($_REQUEST['file'])) {
  echo '{ "error" : "No file specified" }';
  return;
}

$fileName = $_REQUEST['file'];
$dir      = $_REQUEST['dir'];

$REL_DIR = '../';
$PREVIEWS_DIR = '../previews/';

$baseDir = dirname(__FILE__) . '/' . $REL_DIR;
$targetDir = dirname(__FILE__) . '/../' . $dir . '/';

if (!file_exists($baseDir . '/' . $fileName)) {
  echo '{ "error" : "No file found" }';
  return;
}

echo($baseDir . $fileName);
echo($targetDir . $fileName);
$res = rename($baseDir . $fileName, $targetDir . $fileName);

if (!$res) {
  echo '{ "error" : "Failed to move" }';
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
