<?php

class Room {
    // Constructor for rom object.
    public function __construct($status){
        $this->status = $status;
    }

    public function echo_status(){
        echo "The status of the room is ".$this->status.".\n";
    }

    public function description($users){
        echo "There are ".$users."users in the room.\n";
    }
}

$current_room = new Room('transparent');
$newroom->echo_status();
// Dummy data of other users.
$users = 5;

//get 3 commands from user
for ($i=0; $i < 3; $i++) {
    $line = readline("Command: ");
    echo $line."\n";
}
?>

