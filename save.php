<?php

require "barrel.php";
/*
Copyright 2017 Ziadin Givan

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

https://github.com/givanz/VvvebJs
*/

define('MAX_FILE_LIMIT', 1024 * 1024 * 2);//2 Megabytes max html file size

function showError($error) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	die($error);
}

$html   = '';
$file   = '';
$action = '';

if (isset($_POST['startTemplateUrl']) && !empty($_POST['startTemplateUrl'])) {
	$startTemplateUrl = sanitizeFileName($_POST['startTemplateUrl']);
	$html = file_get_contents($startTemplateUrl);
} else if (isset($_POST['html'])){
	$html = substr($_POST['html'], 0, MAX_FILE_LIMIT);
}

if (isset($_POST['file'])) {
	$file = sanitizeFileName($_POST['file'], false);
}

if (isset($_GET['action'])) {
	$action = $_GET['action'];
}

if ($action) {
	//file manager actions, delete and rename
	switch ($action) {
		case 'rename':
			$duplicate = strToBool($_POST['duplicate']);
			$newfile = sanitizeFileName($_POST['newfile'], false);

			$new_dir = dirname($newfile);
			$old_dir = dirname($file);
			
			if ($file && $newfile) {
				if ($duplicate) {
					if (!is_dir($new_dir)) mkdir($new_dir, 0777, true);
					$is_working = false;
					foreach (scandir($old_dir) as $f) {
						if ($f == '.' || $f == '..' || pathinfo($file)['filename'] == $f) continue; // Ignore hidden files
						$is_working = copy($old_dir.'/'.$f, $new_dir.'/'.$f);
					}
					if (copy($file, $newfile)) {
						echo "File '$file' copied to '$newfile'";
					} else {
						showError("Error copied file '$file' renamed to '$newfile'");
					}
				} else {
					if (rename($file, $newfile)) {
						echo "File '$file' renamed to '$newfile'";
					} else {
						showError("Error renaming file '$file' renamed to '$newfile'");
					}
				}
			}
		break;
		case 'delete':
			if ($file) {
				if (unlink($file)) {
					echo "File '$file' deleted";
				} else {
					showError("Error deleting file '$file'");
				}
			}
		break;
		case 'saveReusable':
		    //block or section
			$type = $_POST['type'] ?? false;
			$name = $_POST['name'] ?? false;
			$html = $_POST['html'] ?? false;
			
			if ($type && $name && $html) {
				
				$file = sanitizeFileName("$type/$name");
				$dir = dirname($file);
				if (!is_dir($dir)) {
					echo "$dir folder does not exist\n";
					if (mkdir($dir, 0777, true)) {
						echo "$dir folder was created\n";
					} else {
						showError("Error creating folder '$dir'\n");
					}				
				}
				
				if (file_put_contents($file, $html)) {
					echo "File saved '$file'";
				} else {
					showError("Error saving file '$file'\nPossible causes are missing write permission or incorrect file path!");
				}
			} else {
				showError("Missing reusable element data!\n");
			}
		break;
		default:
			showError("Invalid action '$action'!");
	}
} else {
	//save page
	if ($html) {
		if ($file) {
			$dir = dirname($file);
			if (!is_dir($dir)) {
				echo "$dir folder does not exist\n";
				if (mkdir($dir, 0777, true)) {
					echo "$dir folder was created\n";
				} else {
					showError("Error creating folder '$dir'\n");
				}
			}

			if (file_put_contents($file, $html)) {
				echo "File saved '$file'";
			} else {
				showError("Error saving file '$file'\nPossible causes are missing write permission or incorrect file path!");
			}	
		} else {
			showError('Filename is empty!');
		}
	} else {
		showError('Html content is empty!');
	}
}
