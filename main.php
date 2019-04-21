<?php include 'src/config.php' ?>
<?php

class Room {
    // Constructor for ropm object
    public function __construct($status, $number){
        $this->status = $status;
        $this->number = $number;
    }

    public function echo_status(){
        echo "The current room you are in is ".$this->status.".\n";
    }
    // Give user a description of the current room
    public function description($users){
        $this->echo_status();
        echo "There are ".$users." users in the room.\n";
    }
}

class User {
    // Constructor for ropm object
    public function __construct($name){
        $this->name = $name;
    }
}

$instruction = "Welcome to the world! <br>";
$instruction .= "If you need a glance of documentation, please enter 'help'.<br>";

// echo $instruction;
$current_room = new Room('transparent', 0);
$user = new User("test");
// Dummy data of other users
$users = 5;

// //Get command from user
// while (true) {
//     $line = readline("> ");
//     $line = trim($line);
//     if(strtolower($line) == "quit") break;
//     parse_command($line);
// }

// function parse_command($line) {
//     // If the user want to send a chat message to everyone in the room 
//     if(stripos($line, 'say') === 0){
//         // Extract user's words.
//         $words = trim(explode('say', $line)[1])."\n";
//         print($words);
//     }
//     else if(stripos($line, 'help') === 0){
//         $doc .= "'say <dialog>': send a message to everyone in the room\n";
//         $doc .= "'tell <person_name> <dialog>': send to a specific person\n";
//         $doc .= "'yell <dialog>': yell across the entire world\n";
//         $doc .= "'<direction>': move around\n"; 
//         echo $doc;
//     }
//     else {
//         $error_message = "ERROR: illegal instruction, please try again ";
//         $error_message .= "(enter 'help' to check the documentation)\n";
//         echo $error_message;
//     }
    
// }

?>

