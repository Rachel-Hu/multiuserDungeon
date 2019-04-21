<?php include "src/config.php" ?>
<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <title>Room</title>
</head>
<body>
    <div class="container" style="margin: 2em auto;">
        <!-- Print out error message -->
        <?php
            if(isset($_SESSION['message']) && $_SESSION['message']) {
                $error_message = '<div class="alert alert-danger" role="alert">'.$_SESSION['message'].'</div>';
                echo $error_message;
                $_SESSION['message'] = '';
            }
        ?>
        <!-- An introduction to the current room -->
        <h2 class="text-center">Introduction</h2>
        <p class="text-center">
            <?php 
                $intro = "Welcome to the world! ";
                // If the user is logged in, a description of the room number and 
                // users in that room will be given.
                if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
                    $intro .= "You are now in Room ".$_SESSION['room'].". <br>";
                    $room = $_SESSION['room'];
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
                echo $intro; 
            ?>
        </p>
        <hr>
        <!-- An introduction of the basic command the user could use -->
        <h2 class="text-center">Command</h2>
        <table class="table" style="margin: 2em auto;">
            <thead>
                <tr>
                    <th scope="col">Command</th>
                    <th scope="col">Clarification</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">say &lt;dialog&gt;</th>
                    <td>send a message to everyone in the room</td>
                </tr>
                <tr>
                    <th scope="row">tell &lt;person_name&gt; &lt;dialog&gt;</th>
                    <td>send to a specific person</td>
                </tr>
                <tr>
                    <th scope="row">yell &lt;dialog&gt;</th>
                    <td>yell across the entire world</td>
                </tr>
                <tr>
                    <th scope="row">&lt;direction&gt;</th>
                    <td>move around, directions including north, south, east, west, up, and down</td>
                </tr>
            </tbody>
        </table>
        <hr>
        <h2 class="text-center">Message</h2>
        <!-- Print out latest 3 messages sent to the whole world (i.e public messages) -->
        <div class="card" style="margin: 2em 0;">
            <ul class="list-group list-group-flush" id="public">
                <?php
                    $query = "SELECT * FROM announcement ORDER BY send_time DESC LIMIT 3";
                    $select_message_query = mysqli_query($connect, $query);
                    if(!$select_message_query) {
                        die("QUERY FAILED");
                    }   
                    while($row = mysqli_fetch_array($select_message_query)) {
                        $message = $row['announcement'];
                        $time = $row['send_time'];
                        $message_line = '<li class="list-group-item"><span>'.$message.'</span><span style="float: right;">';
                        $message_line .= $time.'</span></li>';
                        echo $message_line;
                    }
                ?>
            </ul>
        </div> 
        <!-- Print out latest 5 messages sent to the current room and user (i.e. private messages) -->
        <div class="card" style="margin: 2em 0;">
            <ul class="list-group list-group-flush" id="private">
                <?php
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
                        while($row = mysqli_fetch_array($select_message_query)) {
                            $message = $row['messages'];
                            $time = $row['send_time'];
                            $message_line = '<li class="list-group-item"><span>'.$message.'</span><span style="float: right;">';
                            $message_line .= $time.'</span></li>';
                            echo $message_line;
                        }
                    }
                ?>
            </ul>
        </div>   
        <?php
            // If the current user is logged in, ask for the user to enter command.
            if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
                $command_form = '<form method="POST" action="src/parse_cmd.php">
                                    <div class="form-group row">
                                        <label for="command" class="col-sm-3 col-form-label">Please enter your command here: </label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext" id="command" 
                                                    name="command" placeholder="New command" value="">
                                        </div>
                                        <div class="col-sm-3 text-center">
                                            <button type="submit" class="btn btn-outline-dark" name="submit" value="submit">Enter</button>
                                        </div>
                                    </div>  
                                </form>';
                echo $command_form;
                // Also add a logout button
                $logout_btn = '<div class="text-center" style="position: absolute; right: 20%; top: 5%;">
                                    <a href="src/logout.php" class="btn btn-outline-danger">Logout</a>
                                </div>';
                echo $logout_btn;
            }
            // Else, present the login form.
            else {
                $login_form = '<h2 class="text-center" style="margin: 1em auto;">Log In</h2>
                                <form method="POST" action="src/login.php">
                                    <div class="row">
                                        <div class="col-sm-6 mx-auto">
                                            <div class="form-group row">
                                                <label for="username" class="col-sm-3 col-form-label">Username</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-12 text-center">
                                                <button type="submit" class="btn btn-primary" name="login" value="login">Log in</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>';
                echo $login_form;
            }
        ?>

    </div>

    <script src="static/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.0.min.js" integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>
</html>