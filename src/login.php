<?php include 'config.php' ?>
<?php session_start(); ?>

<?php 
    if(isset($_POST['login'])) {
        $username = $_POST['username'];
        // Avoid SQL injection
        $username = mysqli_real_escape_string($connect, $username);

        $query = "SELECT * FROM user WHERE username = '{$username}' ";
        $select_user_query = mysqli_query($connect, $query);
        if(!$select_user_query) {
            die("QUERY FAILED ".mysqli.error($connect));
        }

        $row = mysqli_fetch_array($select_user_query);
        // If the username already exists, log in 
        if($row) {
            $_SESSION['logged_in'] = true;
            $_SESSION['room'] = $row['room_id'];
            $_SESSION['user'] = $username;
            header('Location: ../index.php');         
        }
        // Else, register the user's username and log in
        // The default room of new users is room 1
        else {
            $query = "INSERT INTO user (username, room_id) 
                        VALUES ('$username', 1)";
            $register_user_query = mysqli_query($connect, $query);
            if(!$register_user_query) {
                die("QUERY FAILED ".mysqli.error($connect));
            }
            else {
                $_SESSION['logged_in'] = true;
                $_SESSION['room'] = 1;
                $_SESSION['user'] = $username;
                header('Location: ../index.php');
            }
        }
    }
?>

