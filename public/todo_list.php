<?php

require_once('classes/filestore.php');

$filename = 'data/list.txt';


// //need to instanciate the class for Filestore
$filestore = New Filestore($filename);
$list = $filestore->read_lines();
//var_dump($list);

// function open_file($filename) {
// 	$list_array = [];
// 	if (is_readable($filename) && filesize($filename) > 0) {
// 		$filesize = filesize($filename);
//    		$read = fopen($filename, 'r');
//    		$list_string = trim(fread($read, $filesize));
//    		$list_array = explode("\n", $list_string);
//    		fclose($read);
//    	}
// 	return $list_array;		               
// }

// function save_file($filename, $array) {
// 	$saved_file = fopen($filename, 'w');
// 	$list_string = implode("\n", $array);
// 	fwrite($saved_file, $list_string);
// 	fclose($saved_file);
// }

// function upload_file ($uploaded_file) {
// 	$filename = $uploaded_file['name'];
// 	$upload_directory = '/vagrant/sites/todo.dev/public/uploads/';
// 	$upload_filename = basename($filename);
// 	$saved_filename = $upload_directory . $upload_filename;
// 	move_uploaded_file($uploaded_file['tmp_name'], $saved_filename);
// 	return $saved_filename;
// }

//"<li>$item <a href=\"?key=$key\">Complete</a></li>";
$list = $filestore->read_lines($filename);
if (isset($_POST['add']) || !empty($_POST['add'])) {
	array_push($list, $_POST['add']);
	$filestore->write_lines($list);

} else if (isset($_GET['key']) || !empty($_GET['key'])) {
	unset($list[$_GET['key']]);
	$filestore->write_lines($list);

} else if (isset($_FILES['file1']['error']) && ($_FILES['file1']['error'] == 0)) {
	if ($_FILES['file1']['type'] == 'text/plain') {
		$saved_filename = upload_file($_FILES['file1']);
		$saved_file_array = open_file($saved_filename);
		$list = array_merge($list, $saved_file_array);
		$filestore->write_lines($list);
} else {
		echo "You cannot upload that file";
	}
}
//var_dump($list);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>ToDo List</title>
</head>
<body>
	<h2>ToDo List</h2>
	<ul>
	<? foreach ($list as $key => $item) : ?>

		<li><?= $item ?><a href="?key=<?= $key; ?>">Complete</a></li>
	<? endforeach; ?>
	</ul>
	<form method="POST" action="/todo_list.php">
		<p>
			<label for="add">Type in something to add to the list: </label>
			<input type="text" id="add" name="add">
		</p>
		<p>
			<button type="submit">Add it!</button>
		</p>		
	</form>	
	<form method="POST" enctype="multipart/form-data" action="/todo_list.php">
		<p>
			<label for="file1">Choose a file you would like to open and add to the list: </label>
			<input type="file" id="file1" name="file1">
		</p>
		<p>
			<button type="submit" value="upload">Upload file!</button>
		</p>	
	</form>	
</body>
</html>