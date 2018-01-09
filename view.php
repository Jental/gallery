<?php
$REL_DIR = '../';

if (!isset($_GET['file'])) {
  echo "No file specified";
  return;
}
$filePath = $_GET['file'];

echo '<a href="' . $REL_DIR . $filePath . '"><img src="' . $REL_DIR . $filePath . '" style="max-width: 100%; height: 85%;"></img></a>';
echo '<br/>';
echo $REL_DIR . $filePath;
echo '<br/>';
echo 'Date: ' . shell_exec("identify -format '%[exif:DateTimeOriginal]' " . $REL_DIR . $filePath);
echo '<br/>';

$baseDir = dirname(__FILE__) . '/' . $REL_DIR;
$dir = new DirectoryIterator($baseDir);

$prevFile = NULL;
$nextFile = NULL;
if (!isset($_GET['prev']) && !isset($_GET['next'])) {
  $files = array();
  foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
      $fileName = $fileinfo->getFilename();
      array_push($files, $fileName);
    }
  }

  asort($files);

  $found = false;
  foreach($files as $fileName) {
    if ($found) {
      $nextFile = $fileName;
      break;
    }
    
    if ($fileName == $filePath) {
      $found = true;
    }
    else {
      $prevFile = $fileName;
    }
  }
}
else {
  if (isset($_GET['prev'])) {
    $prevFile = $_GET['prev'];
  }
  if (isset($_GET['next'])) {
    $nextFile = $_GET['next'];
  }
}

if (!is_null($prevFile)) {
  echo '<a href="view.php?file=' . $prevFile . '">Previous</a>';
}
if (!is_null($nextFile)) {
  echo '<a href="view.php?file=' . $nextFile . '" style="margin-left: 20px;">Next</a>';
}

?>
