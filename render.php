<?php

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  $config = include('config.php');

  $httpHeaders = get_headers($config['site'] . '/wp-json/wp/v2/posts?per_page=1', 1);
  $totalPosts = $httpHeaders['X-WP-Total'];
  $totalPages = intval($totalPosts / 100);
  if ($totalPages == 0) {
    $totalPages = 1;
  } elseif ($totalPosts % 100 > 0) {
    $totalPages++;
  }

  $slugs = [];
  $titles = [];
  $posts = [];
  $featuredImage = [];
  $year = [];
  $month = [];

  for ($i = 1; $i <= $totalPages; $i++ ) {
    $json = file_get_contents($config['site'] . '/wp-json/wp/v2/posts?filter[orderby]=date&order=desc&_embed&per_page=100&page=' . $i);
    $obj = json_decode($json);

    foreach ($obj as $value) {
      $slugs[] = $value->slug;
      $titles[] = $value->title->rendered;
      $posts[] = $value->content->rendered;
      if (isset($value->_embedded->{'wp:featuredmedia'}[0]->source_url)) {
        $featuredImage[] = $value->_embedded->{'wp:featuredmedia'}[0]->source_url;
      } else {
        $featuredImage[] = false;
      }
      

      $timestamp = strtotime($value->date);
      $year[] = date('Y', $timestamp);
      $month[] = date('m', $timestamp);
    }
  }

  $header = file_get_contents('_header.php');
  $footer = file_get_contents('_footer.php');
  $indexContents = '';
  $currentYear = '';
  
  for ($i = 0; $i < $totalPosts; $i++ ) {
    $path = '';
    if ($config['folderStructure'] == 1 || $config['folderStructure'] == 2) {
      $path .= $year[$i] . '/';
      if (!is_dir($path)) {
        mkdir($path, 0700);
      }
    }

    if ($config['folderStructure'] == 2) {
      $path .= $month[$i] . '/';
      if (!is_dir($path)) {
        mkdir($path, 0700);
      }
    }

    if ($config['yearHeadings'] && $currentYear != $year[$i]) {
      $currentYear = $year[$i];

      if ($indexContents != '') {
        $indexContents .= '</ul>';
      }
      $indexContents .= '<h2>' . $currentYear . '</h2><ul>';
    }

    $indexContents .= "<li><a href=\"" . $path . $slugs[$i] . ".html\">" . $titles[$i] . "</a></li>";

    $fh = fopen($path . $slugs[$i].".html", 'w+') or die ("Can't open file.");
    $modifiedHeader = str_replace("[TITLE]", $titles[$i], $header);
    fwrite($fh, $modifiedHeader);
    fwrite($fh, "<h1>" . $titles[$i] . "</h1>\n\n");

    if ($featuredImage[$i]) {
      fwrite($fh, "<img src=\"" . $featuredImage[$i] . "\"/>" );
    }

    fwrite($fh, $posts[$i]);
    fwrite($fh, "<a href=\"/index.html\">Back</a>");
    fwrite($fh, $footer);
    fclose($fh);
  }

  $indexContents .= "</ul>";

  $fh = fopen("index.html", 'w+') or die ("Can't open file.");
  $modifiedHeader = str_replace("[TITLE]", $config['siteName'], $header);
  fwrite($fh, $modifiedHeader);
  fwrite($fh, "<h1>Posts</h1>\n\n");
  fwrite($fh, $indexContents);
  fwrite($fh, $footer);
  fclose($fh);
?>

<?php
  $modifiedHeader = str_replace("[TITLE]", "Render static...", $header);
  echo $modifiedHeader
?>
<h1>WordPress to static via REST API</h1>
<?php echo $indexContents ?>
<p><a href="index.html">View the index</a></p>
<?php echo $footer ?>
