<?php 
$filename = 'data/list.txt';
?>

<!DOCTYPE html>
<html>
<head>
	<title>Todo List</title>
</head>
<body>
	<?php
		function open_file($filename) {
		    $filesize = filesize($filename);
		    $read = fopen($filename, 'r');
		    $listString = trim(fread($read, $filesize));
		    $listArray = explode("\n", $listString);
    		fclose($read);
    		return $listArray;               
		}

		function save_file($filename, $array) {
   			$saved_file = fopen($filename, 'w');
		    //$arrayNew = array_merge($saved_file, $array);
    		$listString = implode("\n", $array);
    		fwrite($saved_file, $listString);
    		fclose($saved_file);
    		//return "Thanks for saving to {$filename}!\n";
		}

		// echo "<h4>GET</h4>";
		// echo $_GET['name'];
		// echo $_GET['key'];

		// echo "<h4>POST</h4>";
		// echo $_POST['add'];
		// echo $_POST['open'];

	?>
	<h2>TODO List</h2>
	<?php

		function display_list($array) {
			echo "<ul>";
			foreach ($array as $key => $item) {
				echo "<li>$item <a href=\"?key=$key\">Complete</a></li>"; //?action=remove&index={$index}
			}
			echo "</ul>";
		}
	?>

	<?php	
		$list = open_file($filename);
		if (isset($_POST['add']) || !empty($_POST['add'])) {
			array_push($list, $_POST['add']);
			save_file($filename, $list);
			display_list($list);
		}
		 else if (isset($_GET['key']) || !empty($_GET['key'])) {
			//$key_to_remove = $_GET['key'];
			unset($list[$_GET['key']]);
			save_file($filename, $list);
			display_list($list);
		}
		else
		{
			display_list($list);
		}
	?>
	<form method="POST" action="todo_list.php">
		<p>
			<label for="add">Type in something to add to the list: </label>
			<input type="text" id="add" name="add">
		</p>
		<p>
			<label for="open">Type in a file you would like to open and add to the list: </label>
			<input type="text" id="open" name="open">
		</p>
		<p>
			<button type="submit">Add it!</button>
		</p>		
	</form>	
</body>
</html>