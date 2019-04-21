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
        <h2 class="text-center">Introduction</h2>
        <p class="text-center">
            <?php 
                $intro = "Welcome to the world! ";
                if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true)
                    $intro .= "You are now in Room ".$_SESSION['room'].". <br>";
                echo $intro; 
                // $current_room->description($users);
            ?>
        </p>
        <hr>
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
        <!-- Print out all messages sent to the whole world -->
        <div class="card" style="margin: 2em 0;">
            <ul class="list-group list-group-flush">
                <?php
                    $query = "SELECT * FROM announcement ORDER BY send_time DESC LIMIT 5";
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
        <!-- Print out all messages sent to the current room and user -->
        <div class="card" style="margin: 2em 0;">
            <ul class="list-group list-group-flush">
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

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>
</html>