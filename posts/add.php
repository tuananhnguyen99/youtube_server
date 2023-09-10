<?php 
	include('../connect.php');
	include('../library.php');
	include('../jwt.php');
	
	$res = [];
	$post_type = $_POST['_post_type'];
	$video_id = $_POST['_video_id'] ? $_POST['_video_id']: '';
	$post_img = $_FILES['_post_img'];
	$post_contet = $_POST['_post_content'] ? $_POST['_post_content']: '';
    
    $headers = apache_request_headers();
    $token = $headers['access_token'];
    $token = str_replace('Bearer ', '', $token);
    $verify = verifyAccessToken($token);
    if ($verify['err']) {
        array_push($res, ['error'=>true, 'message'=>$verify['msg']]);
        echo json_encode($res);
        die();
    }
    $user_id = $verify['user']['user_id'];
    if ($post_type === 'img') {
    	if ($post_img['name'] == '' || $post_contet == '') {
	        array_push($res, ['error'=>true, 'message'=>'Bạn chưa nhập đủ thông tin']);
	        echo json_encode($res);
	        die();
	    }
	    if ($post_img['type'] != 'image/png' && $post_img['type'] != 'image/jpeg' && $post_img['type'] != 'image/gif') {
	        array_push($res, ['error'=> true, 'message'=> 'Hình ảnh bạn nhập không đúng định dạng (PNG, JPEG, GIF)']);
	        echo json_encode($res);
	        die();
	    }

	    $time = time();

	    $sqlInsert = "INSERT INTO posts(post_img, post_content, post_type, user_id) VALUES('".$time.$post_img['name']."', '$post_content', '$post_type', '$user_id')";
		$rlInsert = mysqli_query($conn, $sqlInsert);

		$insert_id = mysqli_insert_id($conn);

		if ($insert_id > 0) {
			move_uploaded_file($post_img['tmp_name'], '../images/posts/'.$time.$post_img['name']);
		}

    }else if ($post_type === 'video_id') {
    	if ($video_id == '' || $post_contet == '') {
	        array_push($res, ['error'=>true, 'message'=>'Bạn chưa nhập đủ thông tin']);
	        echo json_encode($res);
	        die();
	    }
	    $sqlInsert = "INSERT INTO posts(video_id, post_content, post_type, user_id) VALUES('$video_id', '$post_content', '$post_type', '$user_id')";
		$rlInsert = mysqli_query($conn, $sqlInsert);

		$insert_id = mysqli_insert_id($conn);
    }

	if ($insert_id > 0) {
		array_push($res, ['error'=> false, 'message'=> 'Thêm thành công']);
        echo json_encode($res);
        die();
	}else{
		array_push($res, ['error'=> true, 'message'=> 'Thêm thất bại']);
        echo json_encode($res);
        die();
	}
	
?>