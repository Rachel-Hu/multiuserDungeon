<?php include 'config.php' ?>
<?php session_start(); ?>
<?php
    if(isset($_POST['command']) && $_POST['command']) {
        $command = trim($_POST['command']);
        $command = mysqli_real_escape_string($connect, $command);
        $sender = $_SESSION['user'];
        parse_command($command, $sender, $connect);
        header('Location: ../index.php');           
    }

    /* 
     * parse_command: to parse the command from user input. Based on legal
     * command set, the user will receive different reactions. If the command is 
     * not allowed, however, the user will receive an error message.
     * All commmand is case-insensitive.
     */
    function parse_command($line, $sender, $connect) {
        $directions = array('north', 'south', 'east', 'west', 'up', 'down');
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
            $words = trim(explode('yell', $line)[1])."\n";
            $message = $sender.' said to every one : '.$words;
            yell($message, $connect);
        }
        // If the user want to send a chat message to a certain person.
        else if(stripos($line, 'tell') === 0){
            $words = trim(explode('tell', $line)[1]);
            $receiver = trim(explode(' ', $words)[0]);
            $dialogue = trim(explode($receiver, $line)[1])."\n";
            $message = $sender.' said to '.$receiver.': '.$dialogue;  
            tell($message, $sender, $receiver, $connect);          
        }
        // If the user want to move around 
        else if(array_search(strtolower($line), $directions) !== false) {
            $line = strtolower($line);
            $room = $_SESSION['room'];
            move($room, $sender, $line, $connect);
        }
        // The rest of commands are illegal and an error message will be presented
        else {
            $error_message = "ERROR: illegal instruction, please try again.";
            $_SESSION['message'] = $error_message;
        }       
    }

    /* 
     * say_to_room: send a message to everyone in the same room
     */
    function say_to_room($message, $room, $sender, $connect) {
        // Select all users in the same room
        $query = "SELECT * FROM user WHERE room_id = $room ";
        $select_user_query = mysqli_query($connect, $query);
        if(!$select_user_query) {
            die("QUERY FAILED");
        }
        // Send the same message
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

    /* 
     * yell: send a message to everyone 
     */
    function yell($message, $connect) {
        $datetime = date('Y-m-d H:i:s');
        $query = "INSERT INTO announcement (announcement, send_time) VALUES ('$message', '$datetime')";
        echo $query;
        $announcement_query = mysqli_query($connect, $query);
        if(!$announcement_query) {
            die("QUERY FAILED");
        }
    }

    /* 
     * tell: send a message to a specific user
     */
    function tell($message, $sender, $receiver, $connect) {
        // Find the specific user from database
        $query = "SELECT * FROM user WHERE username = '$receiver'";
        $select_user_query = mysqli_query($connect, $query);
        if(!$select_user_query) {
            die("QUERY FAILED");
        }

        // Send the message
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

    /* 
     * move: make movements. 
     * The room conditions are hard-coded here. It is a 2*2*2 cube, with room 4
     * and 6 to be soild (which users cannot move to).
     */
    function move($room, $sender, $line, $connect) {
        $new_room = 0;
        switch ($line) {
            case 'north':
            case 'south':
                switch ($room) {
                    case 2:
                        $new_room = 3;
                        break;
                    case 3:
                        $new_room = 2;
                        break;
                    case 5:
                        $new_room = 8;
                        break;
                    case 8:
                        $new_room = 5;
                        break;
                    default:
                        $new_room = 0;
                }
                break;
            case 'west':
            case 'east':
                switch ($room) {
                    case 1:
                        $new_room = 2;
                        break;
                    case 2:
                        $new_room = 1;
                        break;
                    case 7:
                        $new_room = 8;
                        break;
                    case 8:
                        $new_room = 7;
                        break;
                    default:
                        $new_room = 0;
                }
                break;
            case 'up':
            case 'down':
                switch ($room) {
                    case 1:
                        $new_room = 5;
                        break;
                    case 5:
                        $new_room = 1;
                        break;
                    case 3:
                        $new_room = 7;
                        break;
                    case 7:
                        $new_room = 3;
                        break;
                    default:
                        $new_room = 0;
                }
                break;
            default:
                $new_room = 0;
        }
        if($new_room == 0) {
            $error_message = "ERROR: illegal moving direction, the room you are moving to is solid.";
            $_SESSION['message'] = $error_message;
        }
        else {
            $room_query = "UPDATE user SET room_id = $new_room
                                    WHERE username = '$sender'";
            $change_room_query = mysqli_query($connect, $room_query);
            if(!$change_room_query) {
                die("QUERY FAILED");           
            }
            else {
                $_SESSION['room'] = $new_room;
            }
        }
    }
?>