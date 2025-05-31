
<?php
    session_start();
    $conn = mysqli_connect('localhost', 'root', '', 'chat');
    $id = 0;
    if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];
        $id_sel_sql = "SELECT cre_id FROM user WHERE user_id = '$id'";
        $id_sel_result = mysqli_query($conn, $id_sel_sql);
        $id_sel_row = mysqli_fetch_assoc($id_sel_result);
        $num_id = $id_sel_row['cre_id'];
    } else {
        $id = 0;
    }
    $list = [];

    $logout = isset($_POST['logout']) ? $_POST['logout'] : null;
    if(!empty($logout)) {
        logout();
    }

    function logout() {
        unset($_SESSION['user_id']);
        echo"
        <script>
            alert('로그아웃 되었습니다.');
            location = 'web.php';
        </script>
        ";
    }

    $room_id = null;
    $chat_room = isset($_GET['chat_room']) ? $_GET['chat_room'] : null;
    if(!empty($chat_room)) {
        $chat_id_sel_sql = "SELECT * FROM user WHERE user_id = '$chat_room'";
        $chat_id_sel_result = mysqli_query($conn, $chat_id_sel_sql);

        $room_fri_che_sql = "SELECT * FROM addy WHERE (user_id = '$chat_room' OR friend_id = '$chat_room') AND (user_id = '$id' OR friend_id = '$id')";
        $room_fri_che_result = mysqli_query($conn, $room_fri_che_sql);

        if(mysqli_num_rows($room_fri_che_result) === 0 && mysqli_num_rows($chat_id_sel_result) > 0) {
            echo "<div class='new_friend_add_box'>
            <div class='new_friend_add_container'>
                <div class='new_friend_add_header'>
                    <span>친구추가</span>
                </div>
                <div class='new_friend_add_body'>
                    <p>새로운 유저를 친구추가 하시겠습니까?</p>
                </div>
                <div class='new_friend_add_footer'>
                    <div class='new_friend_add_footer_btn_box'>
                        <a onclick='aa()'>취소</a> 
                        <a href='web.php?chat_new_friend=$chat_room'>확인</a>
                    </div>
                </div>
            </div>
            </div>";
        } else if(mysqli_num_rows($chat_id_sel_result) === 0) {
            echo"
                <script>
                    alert('존재하지 않는 아이디입니다.');
                    history.back();
                </script>
                ";  
        } else {
            $now_room_sql = "SELECT * FROM chat_rooms WHERE (user1_id = '$chat_room' OR user2_id = '$chat_room') AND (user1_id = '$id' OR user2_id = '$id')";
            $now_room_result = mysqli_query($conn, $now_room_sql);
            $now_room_row = mysqli_fetch_assoc($now_room_result);
            $room_id = $now_room_row['id'];
            if(mysqli_num_rows($now_room_result) === 0) {
                $new_room_sql = "INSERT INTO chat_rooms (user1_id, user2_id) VALUES ('$id', '$chat_room')";
                $new_room_result = mysqli_query($conn, $new_room_sql);
                if($new_room_result !== false) {
                    echo"
                    <script>
                        location = 'web.php?chat_room=".$chat_room."';
                        localStorage.setItem('chat', 1);
                    </script>
                    ";  
                }
            }
        }
    }

    $chat_new_friend = isset($_GET['chat_new_friend']) ? $_GET['chat_new_friend'] : null;
    if(!empty($chat_new_friend)) {
        $chat_new_friend_sql = "INSERT INTO addy (user_id, friend_id) VALUES ('$id', '$chat_new_friend')";
        $chat_new_friend_result = mysqli_query($conn, $chat_new_friend_sql);
        if($chat_new_friend_result !== false) {
            echo"
                <script>
                    location = 'web.php?chat_room=".$chat_room."';
                    localStorage.setItem('chat', 1);
                </script>
                ";  
        }
    }


    $mese_text = isset($_POST['mese_text']) ? $_POST['mese_text'] : null;
    $room_idd = isset($_POST['room_id']) ? $_POST['room_id'] : null;
    if(!empty($mese_text) && !empty($room_idd)) {
        $messa_sql = "INSERT INTO messages (room_id, sender_id, message) VALUES ($room_idd, '$id', '$mese_text')";
        $messa_result = mysqli_query($conn, $messa_sql);
        if($messa_result !== false) {
            echo "
            <script>
            localStorage.setItem('chat', 1);
            location = 'web.php?chat_room=".$chat_room."';
            </script>
            ";
        }
    }

    $fri = isset($_GET['fri']) ? $_GET['fri'] : null;
    if(!empty($fri)) {
        $fri_sql = "INSERT INTO addy (user_id, friend_id) VALUES ('$id', '$fri')";
        $fri_result = mysqli_query($conn, $fri_sql);
        if($fri_result !== false) {
            echo"
        <script>
            alert('친구추가가 완료되었습니다.')
            location = 'web.php';
        </script>
        ";
        }
    }

    $addy_id = isset($_POST['addy_id']) ? $_POST['addy_id'] : null;

    $swich = isset($_POST['swich']) ? $_POST['swich'] : null;
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : null;
    if(!empty($swich)) {
        if(!empty($user_id) && !empty($user_password)) {
            $salt = bin2hex(random_bytes(32));
            $hash_password = hash('sha256', $user_password . $salt);
            
            if($swich == 1) {
                $id_sql = "SELECT * FROM user WHERE user_id = '$user_id'";
                $id_result = mysqli_query($conn, $id_sql);
                if($id_result !== false) {
                    $sel_row = mysqli_fetch_assoc($id_result);
                    $sel_haspa = $sel_row['hash_password'];
                    $sel_sal = $sel_row['salt'];

                    $hapa = hash('sha256', $user_password . $sel_sal);

                    if($sel_haspa === $hapa) {
                        $_SESSION['user_id'] = $sel_row['user_id'];
                        echo "
                        <script>
                            location = 'web.php';
                        </script>
                    ";    
                    } else {
                        echo "
                    <script>
                        alert('아이디 또는 비밀번호를 확인해주세요');
                        history.back();
                    </script>
                ";
                    }
                } else {
                    echo "
                    <script>
                        alert('아이디 또는 비밀번호를 확인해주세요');
                        history.back();
                    </script>
                ";
                }
            } else if($swich == 2) {
                $sql = "INSERT INTO user (user_id, hash_password, salt) VALUES ('$user_id', '$hash_password', '$salt')";
                $result = mysqli_query($conn, $sql);
                if($result !== false) {
                    echo "
                        <script>
                            location = 'web.php';
                        </script>
                    ";
                } else {
                    echo "
                        <script>
                            alert('중복된 아이디입니다.');
                            history.back();
                        </script>
                    ";
                }
            }
        } else {
            echo "
            <script>
                alert('빈칸을 확인해주세요');
                history.back();
            </script>
            ";
        }
    }

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="web.css">
</head>
<body>
    <div id="wrap">
        <div class="side_menu">
            <div class="side_menu_header">
                <div class="btn_box">
                    <button onclick="sin()" class="bt" style="<?php if($id === 0) {echo'display: block;';}else {echo'display: none;';}?>">로그인</button>

                    <button onclick="sup()" class="bt" style="<?php if($id === 0) {echo'display: block;';}else {echo'display: none;';}?>">회원가입</button>
                </div>

                <div class="log_btn">
                    <input type="checkbox" id="che">
                    <label style="<?php if($id !== 0) {echo'display: block;';}else {echo'display: none;';}?>" class="che" for="che">친구추가</label>

                    <input type="checkbox" id="chat" name="chat">
                    <label style="<?php if($id !== 0) {echo'display: block;';}else {echo'display: none;';}?>" for="chat" class="chat">채팅</label>
                </div>
            </div>


            <div class="side_menu_body">

                <div class="friend_box">
                    <?php
                        $addy_fri_sql = "SELECT * FROM addy WHERE user_id = '$id'";
                        $addy_fri_result = mysqli_query($conn, $addy_fri_sql);
                        if(mysqli_num_rows($addy_fri_result) > 0) {
                            while($addy_fri_row = mysqli_fetch_assoc($addy_fri_result)) {
                                echo '<div class="itemm">';
                                echo '<p class="add_idd">'.$addy_fri_row['friend_id'].'</p>';
                                echo '<a href="web.php?chat_room='.$addy_fri_row['friend_id'].'" id="ia" class="ia">+</a>';
                                echo '</div>';
                            }
                        }
                    ?>
                </div>

                <input type="radio" name="ss" id="sin">
                <form class="sin" action="web.php" method="POST">
                    <h1>로그인</h1> <br>
                    <input type="hidden" name="swich" value="1">
                    <input type="text" name="user_id" id="user_id" placeholder=" 아이디"><br>
                    <input type="password" name="user_password" id="user_password" placeholder=" 비밀번호"><br>
                    <input type="submit" value="로그인">
                </form>
    
                <input type="radio" name="ss" id="sup">
                <form class="sup" action="web.php" method="POST">
                    <h1>회원가입</h1><br>
                    <input type="hidden" name="swich" value="2">
                    <input type="text" name="user_id" id="user_id" placeholder=" 아이디"><br>
                    <input type="password" name="user_password" id="user_password" placeholder=" 비밀번호"><br>
                    <input type="submit" value="회원가입">
                </form>

                <div class="chat_box" id="chat_box">
                <?php
                    $chat_box_sql = "SELECT r.id, r.user1_id, r.user2_id, MAX(m.sent_at) AS last_message_time
                    FROM chat_rooms r
                    JOIN messages m ON r.id = m.room_id
                    WHERE r.user1_id = '$id' OR r.user2_id = '$id'
                    GROUP BY r.id
                    ORDER BY last_message_time DESC
                    ";

                    $chat_box_result = mysqli_query($conn, $chat_box_sql);
                    if(mysqli_num_rows($chat_box_result) > 0) {
                        while($chat_box_row = mysqli_fetch_assoc($chat_box_result)) {
                            $user1_id = $chat_box_row['user1_id'];
                            $user2_id = $chat_box_row['user2_id'];
                            $user_chat_id;

                            if($user1_id !== $id) {
                                $user_chat_id = $user1_id;
                            } else if($user2_id !== $id) {
                                $user_chat_id = $user2_id;
                            }

                            echo '<a href="web.php?chat_room='.$user_chat_id.'" class="item">';
                            echo '<p class="add_id">'.$user_chat_id.'</p>';
                            echo '<span class="item_a">></span>';
                            echo '</a>';  
                        }
                    } else {
                        echo'없습니다';
                    }
                ?>
            </div>

            <div id="add" class="addy_box">
                <form action="web.php" method="POST">
                    <input type="text" name="addy_id" id="addy_id" placeholder=" 유저 아이디 입력"><input type="submit" value="검색" onclick="add()">
                </form>
                <br>
                <?php 
                $show = true;
                if(!empty($addy_id)) {

                    $addy_sql = "SELECT * FROM addy WHERE user_id = '$id'";
                    $addy_result = mysqli_query($conn, $addy_sql);
                    if($addy_result !== false) {
                        while($addy_row = mysqli_fetch_assoc($addy_result)) {
                            $list[] = $addy_row['friend_id'];
                        }
                    }

                    $sea_sql = "SELECT user_id,cre_id FROM user WHERE user_id = '$addy_id'";
                    $sea_result = mysqli_query($conn, $sea_sql);
                    if($sea_result !== false) {
                        if(mysqli_num_rows($sea_result) > 0) {
                            $sea_row = mysqli_fetch_assoc($sea_result);
                            if($id === $sea_row['user_id']) {
                                echo'나';
                            } else {
                                for($i = 0;$i < count($list); $i++) {
                                    if($sea_row['user_id'] === $list[$i]) {
                                        echo '이미 친구추가가 되어있습니다.';
                                        $show = false;
                                        break;
                                    }
                                }
                                if($show) {
                                    echo $sea_row['user_id'].' <a class="add_a" href="web.php?fri='.$sea_row['user_id'].'">추가 +</a>';
                                }
                            }
                        } else {
                            echo"존재하지 않는 아이디입니다.";
                        }
                    }
                }
            ?>
            </div>
            </div>


            <div class="side_menu_footer">
                <form style="<?php if($id !== 0) {echo'display: block;';}else {echo'display: none;';}?>" action="web.php" method="POST">
                    <input type="hidden" name="logout" value="1">
                    <input id="out" style="margin: 0 10px 0 10px;" type="submit" value="로그아웃">
                </form>
            </div>
        </div>
        <div class="chat_container">
            <div style="<?php if(!empty($chat_room)){echo 'display: block';} else{echo 'display: none;';}?>" class="chat_con">
                <div class="chat_body">
                    <?php
                    if(!empty($chat_room)) {
                        $friends = true;
                        $me_friend_sql = "SELECT * FROM addy WHERE user_id = '$id' AND friend_id = '$chat_room'";
                        $me_friend_result = mysqli_query($conn, $me_friend_sql);
                        if(mysqli_num_rows($me_friend_result) === 0) {
                            $friends = false;
                        } else {
                            $friends = true;
                        }
                    }
                    ?>
                    <div style="<?php if($friends === true) {echo'display: none;';} else {echo'display: block;';}?>" class="no_friend_text_message_box">
                        <div class="no_friend_text_box">
                            <h1>친구가 아닌 사용자 <a href="web.php?fri=<?php if(!empty($chat_room)) {echo $chat_room;}?>">친구추가하기</a></h1>
                        </div>
                    </div>
                    <div class="chat_body_box">
                        <?php
                            if(!empty($room_id)) {
                                $chat_show_sql = "SELECT * FROM messages WHERE room_id = $room_id ORDER BY sent_at ASC";
                                $chat_show_result = mysqli_query($conn, $chat_show_sql);
                                if(mysqli_num_rows($chat_show_result) > 0) {
                                    while($chat_show_row = mysqli_fetch_assoc($chat_show_result)) {
                                        if($chat_show_row['sender_id'] == $id) {
                                            echo '<div class="chating_box">';
                                            echo '<p class="user_message1">'.$chat_show_row['message'].'</p>';
                                            echo '</div>';
                                        } else {
                                            echo '<div class="chating_box">';
                                            echo '<p class="user_message2">'.$chat_show_row['message'].'</p>';
                                            echo '</div>';
                                        }
                                    }
                                }
                            }
                        ?>
                    </div>
                </div>
                <div class="chat_text_box">
                    <form id="chat_up" action="web.php?chat_room=<?php echo $chat_room?>" method="POST">
                        <input type="text" name="mese_text" id="mese_text" placeholder=" Enter message">
                        <div class="chat_text_control_box">
                            <input type="hidden" name="room_id" value="<?php if(!empty($room_id)) echo $room_id;?>">
                            <input type="submit" value="↑">
                        </div>
                    </form>
                    </div>
            </div>
        </div>
    </div>
    <script src="web.js"></script>
</body>
</html>