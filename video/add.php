<?php 
include('../connect.php');
include('../jwt.php');
include('../library.php');

    $res = [];
    
    $category = $_POST['_category'];
    $playlist = $_POST['_playlist'];
    $link_video = $_POST['_link_video'];
    $poster = $_FILES['_poster'];
    $title = $_POST['_title'];
    $des = $_POST['_des'];
    $duration = $_POST['_duration'];
    $time = time();
    $playlist_update_time = $playlist ? getTimeCurrent() : '';
    $views = rand(100, 2000);

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
    $sql = "SELECT user_id FROM users WHERE user_id='$user_id'";
    $rl = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($rl);
    if($num == 0){
        array_push($res, ['error'=>true, 'message'=>'Tài khoản không tồn tại']);
        echo json_encode($res);
        die();
    }

    if ($category == '' || $title == '' || $poster['name'] == ''|| $link_video == '') {
        array_push($res, ['error'=> true, 'message'=> 'Bạn chưa nhập đủ thông tin']);
        echo json_encode($res);
        die();
    }

    if ($poster['type'] != 'image/png' && $poster['type'] != 'image/jpeg' && $poster['type'] != 'image/gif') {
        array_push($res, ['error'=> true, 'message'=> 'Hình ảnh đại diện bạn nhập không đúng định dạng (PNG, JPEG, GIF)']);
        echo json_encode($res);
        die();
    }

    $sql = "INSERT INTO videos(user_id, category_id, playlist_id, video_title, video_link, video_poster, video_des, video_duration, playlist_update_time,video_views)
            VALUES('$user_id', '$category', '$playlist', '$title', '$link_video', '".$time.$poster['name']."', '$des','$duration','$playlist_update_time','$views')";
    $rl = mysqli_query($conn, $sql);

    $idIsert = mysqli_insert_id($conn);

    if ($idIsert > 0) {
        move_uploaded_file($poster['tmp_name'], '../images/video/'.$time.$poster['name']);
        array_push($res, ['error'=> false,'message'=> 'Thêm video thành công !']);
        echo json_encode($res);
        die();
    }else{
        array_push($res, ['error'=> true,'message'=> 'Thêm video thất bại !']);
        echo json_encode($res);
    }
?>