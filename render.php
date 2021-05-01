<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

  $httpHeaders = get_headers('http://EXAMPLE.COM/wp-json/wp/v2/posts?per_page=1', 1);
  $totalPosts = $httpHeaders['X-WP-Total'];
  $totalPages = intval($totalPosts / 100);
  if ($totalPages == 0) {
    $totalPages = 1;
  } elseif ($totalPosts % 100 > 0) {
    $totalPages++;
  }

  $slugs = array();
  $titles = array();
  $posts = array();

  for ($i = 1; $i <= $totalPages; $i++ ) {
    $json = file_get_contents('http://EXAMPLE.COM/wp-json/wp/v2/posts?filter[orderby]=date&order=desc&per_page=100&page=' . $i);
    $obj = json_decode($json);

    foreach ($obj as $value) {
      $slugs[] = $value->slug;
      $titles[] = $value->title->rendered;
      $posts[] = $value->content->rendered;
    }
  }

  $header = file_get_contents('_header.php');
  $footer = file_get_contents('_footer.php');

  $indexContents = "<ul>";
  for ($i = 0; $i < $totalPosts; $i++ ) {
    $indexContents .= "<li><a href=\"" . $slugs[$i] . ".html\">" . $titles[$i] . "</a></li>";
  }
  $indexContents .= "</ul>";

  $fh = fopen("index.html", 'w+') or die ("Can't open file.");
  $modifiedHeader = str_replace("[TITLE]", "Home", $header);
  fwrite($fh, $modifiedHeader);
  fwrite($fh, "<h1>Posts</h1>\n\n");
  fwrite($fh, $indexContents);
  fwrite($fh, $footer);
  fclose($fh);

  for ($i = 0; $i < $totalPosts; $i++ ) {
    $fh = fopen($slugs[$i].".html", 'w+') or die ("Can't open file.");
    $modifiedHeader = str_replace("[TITLE]", $titles[$i], $header);
    fwrite($fh, $modifiedHeader);
    fwrite($fh, "<h1>" . $titles[$i] . "</h1>\n\n");
    fwrite($fh, $posts[$i]);
    fwrite($fh, "<a href=\"index.html\">Back</a>");
    fwrite($fh, $footer);
    fclose($fh);
  }
?>
<?php
  $modifiedHeader = str_replace("[TITLE]", "Render static...", $header);
  echo $modifiedHeader
?>
<h1>WordPress to static via REST API</h1>
<?php echo $indexContents ?>
<p><a href="index.html"> View the index</a></p>
<?php echo $footer ?>