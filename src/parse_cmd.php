<?php include 'config.php' ?>
<?php session_start(); ?>
<?php
    if(isset($_POST['command']) && $_POST['command']) {
        $command = trim($_POST['command']);
        $sender = $_SESSION['user'];
        parse_command($command, $sender, $connect);
        // header('Location: ../index.php');           
    }

    function parse_command($line, $sender, $connect) {
        // If the user want to send a chat message to everyone in the room 
        if(stripos($line, 'say') === 0){
            // Extract user's words.
            $words = trim(explode('say', $line)[1])."\n";
            $room = $_SESSION['room'];
            $message = $sender.' said to every one in Room '.$room.': '.$words;
            say_to_room($message, $room, $sender, $connect);
        }
        // If the user want to send a chat message to every one in the world.
        else if(stripos($line, 'yell') === 0){
            // Extract user's words.
            $words = trim(explode('yell', $line)[1])."\n";
            $message = $sender.' said to every one : '.$words;
            yell($message, $connect);
        }
        // If the user want to send a chat message to a certain person.
        else if(stripos($line, 'tell') === 0){
            // Extract user's words.
            $words = trim(explode('tell', $line)[1]);
            $receiver = trim(explode(' ', $words)[0]);
            $dialogue = trim(explode($receiver, $line)[1])."\n";
            $room = $_SESSION['room'];
            $message = $sender.' said to '.$receiver.': '.$dialogue;  
            tell($message, $sender, $receiver, $connect);          
        }

        else {
            $error_message = "ERROR: illegal instruction, please try again.";
            $_SESSION['message'] = $error_message;
        }       
    }

    function say_to_room($message, $room, $sender, $connect) {
        $query = "SELECT * FROM user WHERE room_id = $room ";
        $select_user_query = mysqli_query($connect, $query);
        if(!$select_user_query) {
            die("QUERY FAILED");
        }

        while($row = mysqli_fetch_array($select_user_query)) {
            $user_id = $row['id'];
            $datetime = date('Y-m-d H:i:s');
            $message_query = "INSERT INTO 
                                    messages (user_id, messages, send_time)
                                    VALUES ($user_id, '$message', '$datetime')";
            echo $message_query;
            $send_message_query = mysqli_query($connect, $message_query);
            if(!$send_message_query) {
                die("QUERY FAILED");           
            }
        }
    }

    function yell($message, $connect) {
        $datetime = date('Y-m-d H:i:s');
        $query = "INSERT INTO announcement (announcement, send_time) VALUES ('$message', '$datetime')";
        echo $query;
        $announcement_query = mysqli_query($connect, $query);
        if(!$announcement_query) {
            die("QUERY FAILED");
        }
    }

    function tell($message, $sender, $receiver, $connect) {
        $query = "SELECT * FROM user WHERE username = '$receiver'";
        $select_user_query = mysqli_query($connect, $query);
        if(!$select_user_query) {
            die("QUERY FAILED");
        }

        $row = mysqli_fetch_array($select_user_query);
        $user_id = $row['id'];
        $datetime = date('Y-m-d H:i:s');
        $message_query = "INSERT INTO 
                                messages (user_id, messages, send_time)
                                VALUES ($user_id, '$message', '$datetime')";
        $send_message_query = mysqli_query($connect, $message_query);
        if(!$send_message_query) {
            die("QUERY FAILED");           
        }
        
    }
?>