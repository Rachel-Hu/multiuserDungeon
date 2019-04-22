# About the challenge

## Basic instructions and several explation
- The deployed url is: http://ec2-18-224-109-118.us-east-2.compute.amazonaws.com/index.php
- User need to login (without password) to begin the game
- Most commands are case-insensitive

## Behind the scene (descriptions of database structure)
All codes are included in this repo but the structure of SQL database cannot be included
Here is an presentation of the database structure:
- There is only one database.
- Inside the database there are four tables: 
    | Tables_in_world | 
    | --------------- |
    | announcement    | 
    | messages        | 
    | room            | 
    | user            | 
    
    - Announcement is to store all messages "yelled" by any user (since it is not user-specific):
        | Field        | Type         | Null | Key | Default | Extra          |
        | ------------ | ------------ | ---- | --- | ------- | -------------- |
        | id           | int(32)      | NO   | PRI | NULL    | auto_increment |
        | announcement | varchar(140) | NO   |     | NULL    |                |
        | send_time    | datetime     | NO   |     | NULL    |                |
    - Messages is to store messages that belongs to each user:
        | Field     | Type         | Null | Key | Default | Extra          |
        | --------- | ------------ | ---- | --- | ------- | -------------- |
        | id        | int(32)      | NO   | PRI | NULL    | auto_increment |
        | user_id   | int(32)      | NO   |     | NULL    |                |
        | messages  | varchar(140) | NO   |     | NULL    |                |
        | send_time | datetime     | NO   |     | NULL    |                |
    - User table includes username and room id:
        | Field    | Type        | Null | Key | Default | Extra          |
        | -------- | ----------- | ---- | --- | ------- | -------------- |
        | id       | int(32)     | NO   | PRI | NULL    | auto_increment |
        | username | varchar(32) | NO   |     | NULL    |                |
        | room_id  | int(32)     | NO   |     | NULL    |                |
    - Room is hard-coded here. The whole world is designed as a 2*2*2 cube with repeated boundaries,   and only room 4 and 6 is desgined to be solid.
        | Field  | Type       | Null | Key | Default | Extra          |
        | ------ | ---------- | ---- | --- | ------- | -------------- |
        | id     | int(32)    | NO   | PRI | NULL    | auto_increment |
        | status | tinyint(1) | NO   |     | NULL    |                |

## Further thoughts
- Q: Please Consider any race conditions that might arise with many users in the same room at once (100+).
  A: Considering the current implementation, possible race conditions I could think of are "say" and "move" commands. That is, if one user "say" to the whole room, while someone happens to enter or leave the room at the same time, will they receive the message? Furthermore, when there are thousands of players, it will take some time to find out all players in one room but the player might be moving around and already left the room. <br>
  My idea is to keep a cache of players in one room in advance, document the movement of players in that cache, and only update the cache to the database by a cartain timestep (that is relatively larger). That is similar to the idea of "write-ahead logging" in transaction. It will also be a good idea to user a better data structure such as B-Tree in MySQL, to speed up the searching.   

- Q: What kinds of problems can you foresee as the number of users in your virtual world scales up?    What about if you start interacting with multiple monsters at once?
  A: Actually if commands such as "pickup", "fight" or "put" are implemented, such race conditions will certainly become more severe. Players might "pickup" at the same time, or "fight" while both players have rather low hp (and cannot decide who's hp is the first to be empty). For "put" command, there is an issue related with space design. That is, if players leave multiple items in the same locations, how would the items appear. <br>
  Also, as the world scales up, will there be a limit to the players one room could accommodate? From the prespective of design, it is a must to set such a limit, but problem will occur when the room is almost full and there are multiple players trying to enter into the room. <br>
  If players start to interact with multiple monsters at once, the attacks (from monsters) to the player may also have race conditions with other monsters attacks, the player's defense, (or other player's assistance) etc. A possible solution would be two-phase locking for the process. It is possible to strictly arrange attacks and defenses by time, and hold all locks on attacks and defenses until commit or rollback has completed.