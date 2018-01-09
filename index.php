<html>
<head>
<meta charset="utf-8" />

<link rel="stylesheet" href="uikit/css/uikit.css" />
<script type="text/javascript" src="jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="uikit/js/uikit.js"></script>

<style>
.preview > img {
  max-width: 320px;
  height: 180px;
}
.remove {
  line-height: 29px;
  position: absolute;
  margin-left: -35px;
}
</style>

</head>
<body>
<?php
$REL_DIR = '../';
$PREVIEWS_DIR = '../previews/';

$EXTENSIONS=array('.jpg', '.jpeg', '.png');

function endsWith($haystack, $needle)
{
  $length = strlen($needle);
  if ($length == 0) {
    return true;
  }

  $needleL = strtolower($needle);

  return (strtolower(substr($haystack, -$length)) === $needleL);
}

$baseDir = dirname(__FILE__) . '/' . $REL_DIR;
$dir = new DirectoryIterator($baseDir);

$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : NULL;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$sort   = isset($_GET['sort']) ? (int)$_GET['sort'] : NULL;

$files = array();

foreach ($dir as $fileinfo) {
  if (!is_null($fileinfo) && $fileinfo->isFile() && !$fileinfo->isDot()) {
    $fileName = $fileinfo->getFilename();

    $isValidExt = false;
    foreach($EXTENSIONS as $ext) {
      if (endsWith($fileName, $ext)) {
        $isValidExt = true;
        break;
      }
    }
    if (!$isValidExt) continue;

    if (!is_null($sort) && $sort == 'date') {
      $dateStr = shell_exec("identify -format '%[exif:DateTimeOriginal]' " . $REL_DIR . $fileName);
      $files[$dateStr] = $fileName;
    }
    else {
      array_push($files, $fileName);
    }
  }
}

// asort($files);
rsort($files);

$fileNum = 0;
$lastNum = is_null($limit) ? NULL : ($offset + $limit);
foreach($files as $fileName) {
  if (!is_null($lastNum) && $fileNum > $lastNum - 1) break;

  if ($fileNum >= $offset) {
    if (file_exists(dirname(__FILE__) . '/' . $PREVIEWS_DIR . $fileName)) {
      $previewPath = $PREVIEWS_DIR . $fileName;
    }
    else {
      $previewPath = $REL_DIR . $fileName;
      // echo dirname(__FILE__) . '/' . $PREVIEWS_DIR . $fileName;
    }
?>
  <a class="preview" href="view.php?file=<?php echo $fileName; ?>" data-for="<?php echo $fileName; ?>">
    <img src="<?php echo $previewPath;?>" ></img>
  </a>
  <a href="#" class="uk-button remove uk-icon-remove" data-for="<?php echo $fileName; ?>"></a>
<?php
  }

  $fileNum++;
}
?>

<script type="text/javascript">
  $(function(){
    $('a.remove').click(function(e) {
      e.preventDefault();

      var dfor = $(e.currentTarget).attr('data-for');
      $('a.preview[data-for="' + dfor + '"]').toggle(false);
      $(e.currentTarget).toggle(false);

      $.ajax({
        method: 'POST',
        url: 'remove.php?file=' + dfor,
        data: {},
        success: function(raw) {
          var resp = JSON.parse(raw);
          if (resp.error) {
            alert(resp.error);
            console.log(resp.error);
            $('a.preview[data-for="' + dfor + '"]').toggle(true);
            $(e.currentTarget).toggle(true);
          }
        },
        error: function(err) {
          alert('Error');
          console.log(err);
          $('a.preview[data-for="' + dfor + '"]').toggle(true);
          $(e.currentTarget).toggle(true);
        }
      });
    });
  });
</script>
</body>
</html>
