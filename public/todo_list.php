<!DOCTYPE html>

<html>
<head>
	<title>Todo List</title>
</head>
<body>
	<?php
		function open_file($filename, $array) {
		    $filesize = filesize($filename);
		    $read = fopen($filename, 'r');
		    $listString = trim(fread($read, $filesize));
		    $listArray = explode("\n", $listString);
		    $array = array_merge($listArray, $array);
		    fclose($read);
		    return $array;        
		}

		echo "<h4>GET</h4>";
		var_dump($_GET);
		echo "<h4>POST</h4>";
		var_dump($_POST);
	?>
		<h2>TODO List</h2>
		<?php 
			$list = ["fixed cracked Mac screen", "count how many stars are out", "Get something for Libby's graduation"];

			// for($i = 0; $i <= count($list); $i++) {
			// 	echo "<li>$list</li>";
			// }
			echo "<ul>";
			foreach ($list as $key => $item) {
				echo "<li>$item</li>";
			}
			echo "</ul>";
		?>
	

	<form method="POST">
		<p>	
			<label for="add">Type in something to add to the list: </label>
			<input type="text" id="add" name="add">
		</p>
		<p>
			<button type="submit">Add it!</button>
		</p>		
	</form>	
</body>
</html>