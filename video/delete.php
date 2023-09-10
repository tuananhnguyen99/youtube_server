<?php 
include('../connect.php');
include('../jwt.php');
    $res = [];
    $id = $_GET['_id'] ;

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
    if ($user_id === '7') {
        array_push($res, ['error'=>true, 'message'=>"Bạn không thể xóa bằng tài khoản này"]);
        echo json_encode($res);
        die();
    }
    $sql = "SELECT user_id FROM users WHERE user_id='$user_id'";
    $rl = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($rl);
    if($num <= 0){
        array_push($res, ['error'=>true, 'message'=>'Tài khoản không tồn tại']);
        echo json_encode($res);
        die();
    }

    $sqlSelect = "SELECT video_poster FROM videos WHERE video_id='$id'";
    $rlSelect = mysqli_query($conn, $sqlSelect);

    $num = mysqli_num_rows($rlSelect);

    if ($num <= 0) {
        array_push($res, ['error'=> true,'message'=> 'Video này không có trong dữ liệu! Bạn vui lòng thử tải lại trang']);
        echo json_encode($res);
        die();
    }

    $data = mysqli_fetch_assoc($rlSelect);

    $poster = json_decode($data['video_poster']);

    $sql = "DELETE FROM videos WHERE video_id='$id'";
    $rl = mysqli_query($conn, $sql);

    $sql2 = "DELETE FROM comments WHERE video_id='$id'";
    $rl2 = mysqli_query($conn, $sql2);

    $sql3 = "DELETE FROM comment_vote WHERE video_id='$id'";
    $rl3 = mysqli_query($conn, $sql3);

    $sql4 = "DELETE FROM video_votes WHERE video_id='$id'";
    $rl4 = mysqli_query($conn, $sql4);
    
    $sqlCheck = "SELECT video_id FROM vidoes WHERE video_id='$id'";
    $rlCheck = mysqli_query($conn, $sqlCheck);

    $check = mysqli_num_rows($rlCheck);

    if ($check > 0) {
        array_push($res, ['error'=> true,'message'=> 'Xóa thất bại !']);
        echo json_encode($res);
    }else{
        unlink('../images/video/'.$data['video_poster']);

        array_push($res, ['error'=> false,'message'=> 'Xóa thành công !']);
        echo json_encode($res);
    }
?>