//Relative root from main.php to project directory(Mio)
var relativeRoot = "../request_handlers/";
//Relative path to the controllers directory
var controllersPath = "../controllers/";

/*
Sends the user's message to main.php through AJAX
*/
function saveMessage() {
    let message = $("#message").val();
    if (message.trim() === "") {
        return;
    }
    let roomName = $("#roomName").val();
    if (roomName !== "") {
        $.ajax({
            type: "POST",
            async: true,
            url: "../message/storeMessage.php",
            dataType: "JSON",
            data: {
                message: "" + message + "", 
                currentRoom: "" + roomName + "", 
            },
            failure: function(data) { console.log("Failed to send message: " + message); }
        });
        $("#message").val("");
    }
}

/*
Appends a message div into the chat
*/
function displayMessage(message, classStyle, time, id, name) {
    if (message !== "")  {
        let messageItem = 
        '<li id ="'+id+'" class="'+classStyle+'">'+
            '<div class="avatar"><img src="../../imgs/user.png" /></div>'+
            '<div class="messages div-dark"><p class="username">'+name+'</p><p>'+message+'</p><time>'+time+'</time></div>'+
        '</li>';
        
        $("#messageList").append(messageItem);
        $("#message").val("");
        $("#messageContainer").scrollTop($("#messageContainer").prop("scrollHeight"));
    }
}

/*
Clears the chat's messages
*/
function clearMessages() {
    $("#messageList").html("");
}

/*
Clears the inputs for room name and id
*/
function clearRoom() {
    $("#roomName").val("");
    $("#roomId").val("");
}

function displayErrorMessage(message) {
    let messageItem =
        '<li class="self">'+
        '<div class="messages div-dark"><p class="username">'+name+'</p><p>'+message+'</p><time>'+Date()+'</time></div>'+
        '</li>';
    $("#messageList").append(messageItem);
    $("#messageContainer").scrollTop($("#messageContainer").prop("scrollHeight"));
}


$(document).ready(function() {
    websocket = createSocket();
    //Sends the username, channel, and message through websocket
    function sendMessageWithUserInfo() {
        let userInfo = {
            username: $("#username").val(),
            currentChannelName: $("#roomName").val(),
            message: $("#message").val()
        };
        if (sendMessage(userInfo)) {
            $("#message").val("");
        } else {
            handleSendMessageFailure();
        }
    }

    $("#sendMessageButton").on("click", sendMessageWithUserInfo);
    
    //Add enter button listener for message input
    $("#message").on("keyup", function(event) {
        event.preventDefault();
        if (event.keyCode === 13) {
            sendMessageWithUserInfo();
        }
    });
});