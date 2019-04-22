<?php include 'config.php' ?>
<?php session_start(); ?>
<?php
    $messages = array();
    // Select latest 3 messages send to the whole world
    $query = "SELECT * FROM announcement ORDER BY send_time DESC LIMIT 3";
    $select_message_query = mysqli_query($connect, $query);
    if(!$select_message_query) {
        die("QUERY FAILED");
    } 
    $public = array();  
    while($row = mysqli_fetch_array($select_message_query)) {
        $public[] = $row;
    }
    // Pack into one json object
    $messages['public'] = $public;
    // If the user is logged in, select latest 5 messages to the user
    if(isset($_SESSION['user'])) {
        $username = $_SESSION['user'];
        $query = "SELECT * FROM messages 
                    INNER JOIN user ON user.id = messages.user_id
                    WHERE user.username = '$username' 
                    ORDER BY send_time DESC LIMIT 5";
        $select_message_query = mysqli_query($connect, $query);
        if(!$select_message_query) {
            die("QUERY FAILED");
        } 
        $private = array();  
        while($row = mysqli_fetch_array($select_message_query)) {
            $private[] = $row;
        }
        // Pack into one json object
        $messages['private'] = $private;
    }
    $intro = "Welcome to the world! ";
    if(isset($_SESSION['room'])) {
        $room = $_SESSION['room'];        
        $intro .= "You are now in Room ".$room.". <br>";
        $query = "SELECT COUNT(*) FROM user WHERE room_id = $room";
        $count_user_query = mysqli_query($connect, $query);
        if(!$count_user_query) {
            die("QUERY FAILED");
        }
        else {
            $others = mysqli_fetch_array($count_user_query)[0] - 1;
            if($others == 0) {
                $intro .= "You are the only person in this room. <br>";
            }
            else {
                $intro .= "There are ".$others." more adventurers in the same room. <br>";
            }
        }
    }
    // Pack into one json object
    $messages['description'] = $intro;
    echo json_encode($messages);
?>