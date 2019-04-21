<?php include 'config.php' ?>
<?php session_start(); ?>
<?php
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
    }
    // Pack into one json object
    $messages = array();
    $messages['public'] = $public;
    $messages['private'] = $private;
    echo json_encode($messages);
?>