<?php
$REL_DIR = '../';

$baseDir = dirname(__FILE__) . '/' . $REL_DIR;
$dir = new DirectoryIterator($baseDir);

$files = array();
foreach ($dir as $fileinfo) {
  if (!$fileinfo->isDot()) {
    $fileName = $REL_DIR . $fileinfo->getFilename();
    array_push($files, $fileName);
  }
}
asort($files);
foreach($files as $fileName) {
  echo '<a href="view.php?file=' . $fileName . '"><img src=' . $fileName . ' style="width: 320px; height: 180px;"></img></a>';
}
?>
