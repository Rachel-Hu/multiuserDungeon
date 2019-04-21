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

function updateMessage(message) {
    $("li").remove();
    var public = message['public'];
    var private = message['private'];
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
}

window.onload = getMessage;
window.setInterval(getMessage, 5000);