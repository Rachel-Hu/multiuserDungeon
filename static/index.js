// From client side, send a request to refresh page
function getMessage() {
    $.ajax({
        url: "src/refresh_page.php",
        dataType: "json",
        success: updateMessage,
        error: function(){
            console.log("Error");
        }
    });
}

// Update public and private messsage separately
function updateMessage(message) {
    // First remove all old messages
    $("li").remove();
    var public = message['public'];
    var private = message['private'];
    var intro = message['description'];
    // Then add the new ones
    $(public).each(function() {
        $("#public").append(
            '<li class="list-group-item"><span>' + this.announcement + 
                        '</span><span style="float: right;">' + 
                        this.send_time + '</span></li>'                   
        );
    });
    $(private).each(function() {
        $("#private").append(
            '<li class="list-group-item"><span>' + this.messages + 
                        '</span><span style="float: right;">' + 
                        this.send_time + '</span></li>'                   
        );
    });
    $('#room-description').remove();
    $('#des-box').append('<p class="text-center" id="room-description">' + 
                            intro + '</p>');
}

window.onload = getMessage;
window.setInterval(getMessage, 5000);