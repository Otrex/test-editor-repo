<?php

require "barrel.php";

//include 'editor.html';
$html = file_get_contents('editor.html');

$pageKey = str_replace('.', '/', ($_GET['site'] ?? 'page'));
//search for html files in demo and my-pages folders
$htmlFiles = glob('{my-pages/*.html,demo/*\/*.html,demo/*.html,'.$pageKey.'/*.html,'.$pageKey.'/*\/*.html}',  GLOB_BRACE);
$files = '';
foreach ($htmlFiles as $file) { 
  if (in_array($file, array('new-page-blank-template.html', 'editor.html'))) continue;//skip template files
  $pathInfo = pathinfo($file);
  $filename = $pathInfo['filename'];
  $folder = preg_replace('@/.+?$@', '', $pathInfo['dirname']);
  $subfolder = preg_replace('@^.+?/@', '', $pathInfo['dirname']);
  // if ($filename == 'index' && $subfolder) {
  //   $filename = $subfolder;
  // }

  if ($folder !== 'demo') {
    $folder = $subfolder;
  } else {
    $filename = $subfolder;
  }


  $url = $pathInfo['dirname'] . '/' . $pathInfo['basename'];
  $name = $filename;
  $title = ucfirst($name);
  // clog($pathInfo['dirname']);
  

  $files .= "{name:'$name', file:'$file', title:'$title',  url: '$url', folder:'$folder'},";
} 



//replace files list from html with the dynamic list from demo folder
$html = str_replace('(pages);', "([$files]);", $html);
$html = str_replace('window.PAGE_KEY = "site";', "window.PAGE_KEY = '$pageKey';", $html);

echo $html;
