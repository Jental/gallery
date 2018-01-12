<html>
<head>
<meta charset="utf-8" />

<link rel="stylesheet" href="uikit/css/uikit.css" />
<script type="text/javascript" src="jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="uikit/js/uikit.min.js"></script>
<script type="text/javascript" src="uikit/js/uikit-icons.min.js"></script>

<style>
 body {
   padding: 10px 30px;
 }
 .preview-card {
   display: inline-block;
   margin: 2px;
 }
 .preview {
   float: left;
 }
 .preview > img {
   max-width: 320px;
   height: 180px;
 }
 .buttons {
   float: left;
   margin-left: -30px;
   display: none;
 }
 .preview-card:hover .buttons {
   display: inline-flex;
 }
 .buttons > a  {
   width: 30px;
 }
 .remove {
   padding: 0;
 }
 .category {
   padding: 0;
 }
 .category-list {
   line-height: 29px;
   position: absolute;
   margin-left: -37px;
   margin-top: 60px;
   list-style: none;
   padding: 0;
 }
 .category-list a {
   width: 100%;
 }
 .set-category {
 }
 .uk-position-center-left {
   position: fixed;
   left: 10px;
   top: 50%;
 }
 .uk-position-center-right {
   position: fixed;
   right: 10px;
   top: 50%;
 }
</style>

</head>
<body>
  <?php
  $REL_DIR = '../';
  $PREVIEWS_DIR = '../previews/';

  $EXTENSIONS=array('.jpg', '.jpeg', '.png');
  $SYSTEM_DIRS=array('previews', 'gallery', 'trashbin');

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

  $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
  $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
  $sort   = isset($_GET['sort']) ? (int)$_GET['sort'] : NULL;

  $files = array();
  $directories = array();

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
    else if (!is_null($fileinfo) && $fileinfo->isDir() && !$fileinfo->isDot()) {
      $fileName = $fileinfo->getFilename();
      if (!in_array($fileName, $SYSTEM_DIRS)) {
        array_push($directories, $fileinfo->getFilename());
      }
    }
  }

  // asort($files);
  rsort($files);

  ?>
  <div class="uk-flex uk-flex-wrap uk-flex-center">
<?php
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
      }
  ?>
  <div class="preview-card" data-for="<?php echo $fileName; ?>"  >
    <a class="preview" href="view.php?file=<?php echo $fileName; ?>" data-for="<?php echo $fileName; ?>">
      <img src="<?php echo $previewPath;?>" ></img>
    </a>
    <div class="uk-panel buttons uk-flex-inline uk-flex-column">
      <a href="#" class="uk-button remove uk-icon" uk-icon="icon: trash" data-for="<?php echo $fileName; ?>"></a>
      <a href="#" class="uk-button category uk-icon" uk-icon="icon: folder" uk-toggle="target: .category-list[data-for='<?php echo $fileName; ?>']" data-for="<?php echo $fileName; ?>"></a>
      <ul class="category-list uk-flex-column" data-for="<?php echo $fileName; ?>" hidden="hidden">
        <?php foreach ($directories as $dir) { ?>
          <li>
            <a href="#" class="uk-button set-category" data-id="<?php echo $dir; ?>"><?php echo $dir; ?></a>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
<?php
}

$fileNum++;
}
?>
  </div>
  <a id="prevpage" class="uk-slidenav-large uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
<a id="nextpage" class="uk-slidenav-large uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>


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

   $('a.set-category').click(function(e) {
     e.preventDefault();

     var $card = $(e.currentTarget).closest('.preview-card');
     var dfor = $card.attr('data-for');
     var category = $(e.currentTarget).attr('data-id');

     $.ajax({
       method: 'POST',
       url: 'move.php?file=' + dfor,
       data: {
         'dir' : category
       },
       success: function(raw) {
         var resp = JSON.parse(raw);
         if (resp.error) {
           alert(resp.error);
           console.log(resp.error);
           $card.toggle(true);
         }
       },
       error: function(err) {
         alert('Error');
         console.log(err);
         $('a.preview[data-for="' + dfor + '"]').toggle(true);
         $(e.currentTarget).toggle(true);
       }
     });
     $card.toggle(false);
   });

   var url = new URL(window.location.href);
   var limit = url.searchParams.get("limit");
   if (!limit) {
     limit = 50;
   }
   var offset = url.searchParams.get("offset");
   if (!offset) {
     offset = 0;
   }
   url.searchParams.set('offset', Number(offset) + Number(limit));
   $('#nextpage').prop('href', url.toString());
   url.searchParams.set('offset', Math.max(0, Number(offset) - Number(limit)));
   $('#prevpage').prop('href', url.toString());

 });
</script>
</body>
</html>
