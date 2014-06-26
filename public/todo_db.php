<?php
//1)Establish DB connection
$dbc = new PDO('mysql:host=127.0.0.1;dbname=todo_db', 'nicole', 'bakagaki');

// Tell PDO to throw exceptions on error
$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo $dbc->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";

//require_once('classes/filestore.php');
//$filename = 'data/list.txt';

// //need to instanciate the class for Filestore
//$filestore = New Filestore($filename);

function upload_file ($uploaded_file) {
	$filename = $uploaded_file['name'];
	$upload_directory = '/vagrant/sites/todo.dev/public/uploads/';
	$upload_filename = basename($filename);
	$saved_filename = $upload_directory . $upload_filename;
	move_uploaded_file($uploaded_file['tmp_name'], $saved_filename);
	return $saved_filename;
}

function read_lines($filename) {
    // todo list 
    $list_array = [];
    if (is_readable($filename) && filesize($filename) > 0) {
        $filesize = filesize($filename);
        $read = fopen($filename, 'r');
        $list_string = trim(fread($read, $filesize));
        fclose($read);
        $list_array = explode("\n", $list_string);
    }
    return $list_array; 
} 
//"<li>$item <a href=\"?key=$key\">Complete</a></li>";

//$list = $filestore->read($filename);

// class InvalidInputException extends Exception {}

//2) Check if something was posted
try {
	if (isset($_POST['add_item'])) {
		if (empty($_POST['add_item']) || strlen($_POST['add_item']) >= 150) {
			throw new InvalidInputException("Exception: Items added to the list must be 150 characters or less and cannot be blank");
		} else {
			//2a) is the item being added? add todo
			$stmt = $dbc->prepare('INSERT INTO todo_db (add_item) VALUES (:add_item)');
			$stmt->bindValue(':add_item', $_POST['add_item'], PDO::PARAM_STR);
			$stmt->execute();
		}	
		//var_dump($_POST['add_item']);
		//need to make page refresh to get new item to show up
		//$filestore->write($list);
	}
} catch (InvalidInputException $exception) {
	$msg = $exception->getMessage() . PHP_EOL;
}
//2b) Is the item being removed? Remove it
if (isset($_POST['key']) || !empty($_POST['key'])) {
	$id = $_POST['key'];
	$stmt = $dbc->prepare('DELETE FROM todo_db WHERE id=:id');
	$stmt->bindValue(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	//$filestore->write($list);
}
//2c) Is a file being uploaded
if (isset($_FILES['file1']['error']) && ($_FILES['file1']['error'] == 0)) {
	if ($_FILES['file1']['type'] == 'text/plain') {
		$saved_filename = upload_file($_FILES['file1']);
		$saved_file_array = read_lines($saved_filename);
		$stmt = $dbc->prepare('INSERT INTO todo_db (add_item) VALUES (:add_item)');
		foreach ($saved_file_array as $item) {
			$stmt->bindValue(':add_item', $item, PDO::PARAM_STR);
			$stmt->execute();
		}
	} else {
		echo "You cannot upload that file";
	}
}
//3) Query DB for total todo count
	
//4) Determine pagination values
$stmt  = $dbc->query('SELECT count(*) FROM todo_db');
$count = $stmt->fetchColumn();

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$numPages = ceil($count / 10);
$next_page = $page + 1;
$previous_page = $page - 1;
$offset = ($page - 1) * 10;

//5)Query for todos on current page
$list = $dbc->query('SELECT * FROM todo_db LIMIT 10 OFFSET ' . $offset);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>ToDo List</title>
</head>
<body>
	<h2>ToDo List</h2>
	
	<?if (isset($msg)) : ?>
		<? echo $msg; ?>
	<? endif; ?>

	<ul>
		<? foreach ($list as $item) : ?>
			<li><?= $item['add_item']; ?><button class="btn btn-danger btn-sm pull-right btn-remove" data-todo="<?= $item['id']; ?>">Remove</button></li>
		<? endforeach; ?>	
	</ul>

	<form method="POST" action="todo_db.php">
		<p>
			<label for="add_item">Type in something to add to the list: </label>
			<input type="text" id="add_item" name="add_item">
		</p>
		<p>
			<button type="submit">Add it!</button>
		</p>		
	</form>

	<form id="removeForm" action="todo_db.php" method="post">
	    <input id="removeId" type="hidden" name="key" value="">
	</form>

	<form method="POST" enctype="multipart/form-data" action="/todo_db.php">
		<p>
			<label for="file1">Choose a file you would like to open and add to the list: </label>
			<input type="file" id="file1" name="file1">
		</p>
		<p>
			<button type="submit" value="upload">Upload file!</button>
		</p>	
	</form>
	<ul class="pager">
        <li class="previous_disabled"><a href="<?="?page=$previous_page"?>">Previous</a></li>
        <li class="next"><a href="<?="?page=$next_page"?>">Next</a></li>
    </ul>
</body>

	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script>
	$('.btn-remove').click(function () {
	    var todoId = $(this).data('todo');
	    if (confirm('Are you sure you want to remove item ' + todoId + '?')) {
	        $('#removeId').val(todoId);
	        $('#removeForm').submit();
	    }
	});
	</script>
</html>