<?php 
	include('../connect.php');
	include('../library.php');
	include('../jwt.php');
	
	$res = [
		'totalPosts' => '',
        'totalPage' => '',
        'page' => '',
        'limit' => '',
		'postsList'=> [],
	];
	$params = '';
	$user_id = $_GET['_user_id'] ? $_GET['_user_id'] : '';
	$video_id = $_GET['video_id'];
	$post_type = $_GET['_post_type'] ? $_GET['_post_type'] : '';
	$type = $_GET['_type'] ? $_GET['_type'] : '';
    $limit = $_GET['_limit'] ? (int)$_GET['_limit'] : 10;
    $page = $_GET['_page'] ? (int)$_GET['_page']: 1;
    $order_by = $_GET['_order_by'] ? $_GET['_order_by'] : 'post_id';
    $order_type = $_GET['_order_type'] ? $_GET['_order_type'] : 'DESC';

    if ($type == 'get_by_token') {
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
    }
    
	$sql = "SELECT post_id FROM posts ".$params;
	$rl = mysqli_query($conn, $sql);
	$res['totalPosts'] = mysqli_num_rows($rl);
	$res['limit'] = $limit;
    $res['page'] = $page;
    $res['totalPage'] = ceil($res['totalPosts'] / $res['limit']);
    $start = ($res['page'] - 1) * $res['limit'];
    $sql_limit="LIMIT $start, $limit"; 

	$sql2 = "SELECT * FROM posts INNER JOIN users ON posts.user_id = users.user_id WHERE posts.user_id= '$user_id' ORDER BY $order_by $order_type $sql_limit";
	$rl2 = mysqli_query($conn, $sql2);
	
	while ( $row = mysqli_fetch_assoc($rl2) ) {
		$post_id = $row['post_id'];
		$video_id = $row['video_id'];
		$user_id = $row['user_id'];

		$post_type = $row['post_type'];
		$post_content = $row['post_content'];
		$post_img = $row['post_img'];
		$post_created_at = $row['post_created_at'];
		$post_updated_at = $row['post_updated_at'];

		$user_name = $row['user_name'];
		$user_avatar = $row['user_avatar'];

		if ($post_type == 'video_id') {
			$sql3 = "SELECT * FROM videos WHERE videos.video_id='$video_id'";
			$rl3 = mysqli_query($conn, $sql3);
			$video = mysqli_fetch_assoc($rl3);
		}

		array_push($res['videoList'], [
			'user_id' => $user_id,
			'post_id' => $post_id,
			'video_id' => $video_id,
			'post_type' => $post_type,
			'post_content' => $post_content,
			'post_img' => $post_img,
			'post_created_at' => $post_created_at,
			'post_updated_at' => $post_updated_at,
			'user_name' => $user_name,
			'user_avatar' => $user_avatar ? URLImgUser().$user_avatar : '',
			'videoData' => $video,
		]);
	}
	echo json_encode($res);
?>