var roomInvite;
var websocket;
//websocket readyState constants
let CONNECTING = 0;
let OPEN = 1;
let CLOSING = 2;
let CLOSED = 3;
//end websocket readyState constants

//Relative root from main.php to project directory(Mio)
var relativeRoot = "../request_handlers/";
//Relative path to the controllers directory
var controllersPath = "../controllers/";

/* Open the sidenav */
function openSlider() {
    document.getElementById("slider").style.width = "100%";
}
    
/* Close/hide the sidenav */
function closeSlider() {
    document.getElementById("slider").style.width = "0";
}

/*
Set the text for the slider's button
*/
function setSliderAction(action) {
    document.getElementById("sliderAction").innerHTML = action;
}

/*
Set the mode for the slider
*/
function setSliderMode(mode) {
    if (mode === "addFriend") {
        document.getElementById("sliderName").innerHTML = "Friend Name";
        setSliderAction("Send Request");
    }
    else if (mode === "addChat") {
        document.getElementById("sliderName").innerHTML = "Chat Name";
        setSliderAction("Create");
    }
    else if (mode === "addToRoom") {
        document.getElementById("sliderName").innerHTML = "Friend Name";
        setSliderAction("Invite");
    }
}

/*
Refreshes the friend list
*/
function refreshFriendsList() {
    $.ajax({
        type: "GET",
        url: relativeRoot + "friendHandler.php",
        async: true,
        dataType: "HTML",
        data: {
            request: "getFriendDivs"
        },
        success: function(data) { $("#friendsCollapse").html(data); },
        failure: function(data) { alert("Unable to load friend list."); }
    });
}

/*
Refreshes the room list
*/
function refreshRoomList() {
    $.ajax({
        type: "GET",
        url: relativeRoot + "/roomHandler.php",
        async: true,
        dataType: "HTML",
        data: {
            request: "getRooms"
        },
        success: function(data) { $("#roomCollapse").html(data); },
        failure: function(data) { alert("Unable to load room list."); }
    });
}


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

/*
Retrieves messages for a given room and displays them in the chat
*/
function getNewMessages() {  
    let roomName = $("#roomName").val();
    let userId = $("#userId").val();
    
    $.ajax({
        type: "POST",
        url: "../php/message/getMessages.php", 
        data: { 
            currentRoom: "" + roomName + "",
            messageCount: messagesReceived
        }, 
        complete: function(data) {
            let response = data.responseText;
            if (response.trim() !== "") {
                let responseJSON = JSON.parse(response);
                /*
                Messages are retrieved in Descending order by Timestamp, so reversal 
                of the array shows them in the correct Ascending order.
                */
                responseJSON.reverse().map(function(currentElement) {
                    messagesReceived += 1;
                    let senderId = currentElement["userId"];
                    if (senderId === userId) {
                        displayMessage(currentElement["content"], "self", currentElement["time"], currentElement["messageId"], currentElement["username"]);
                    }
                    else {
                        displayMessage(currentElement["content"], "other", currentElement["time"], currentElement["messageId"], currentElement["username"]);
                    }
                });
                window.scrollTo(0, window.innerHeight);
            }
        }
    }); 
    updateChatTimeoutId = setTimeout("getNewMessages()", 500);
}

/*
Creates a room with given name
*/
function createRoom(name) {
    $.ajax({
        url: relativeRoot + "roomHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "createRoom",
            roomName: ""+name+""
        },
        failure: function(data) {alert("Failed to create room: " + name);},
        complete: function(data) { refreshRoomList(); }
    });
}

/*
Adds a friend with the given name
*/
function sendFriendRequest(name) {
    console.log("sending friend request to " + name);
    $.ajax({
        url: relativeRoot + "friendHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "sendFriendRequest",
            receiver: ""+name+""
        },
        dataType: "JSON",
        failure: function(data) { alert("Failed to send friend request to " + name); },
        complete: function(data) { refreshFriendsList(); }
    });
}

/*
Deletes a friend
*/
function deleteFriend(friendName) {
    console.log("Delete " + friendName);
    $.ajax({
        url: relativeRoot + "friendHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "deleteFriend",
            friendName: ""+friendName+""
        },
        datatype: "JSON",
        failure: function(data) { alert("Failed to delete friend: " + friendName); },
        complete: function(data) { refreshFriendsList(); }
    });
}

/*
Approves a friend request
*/
function approveFriendRequest(requesterName) {
    $.ajax({
        url: relativeRoot + "friendHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "acceptFriendRequest",
            requester: ""+requesterName+""
        },
        dataType: "JSON",
        failure: function(data) { alert("Failed to approve friend request from" + requesterName + "."); },
        complete: function(data) { refreshFriendsList(); }
    });
}

/*
Invites a user to a room
*/
function addToRoom(friendName) {
    $.ajax({
        url: relativeRoot + "roomHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "addToRoom",
            userToAdd: "" + friendName + "",
            roomName: "" + roomInvite + ""
        },
        complete: function(data) { closeSlider(); },
        failure: function(data) { alert("Failed to invite " + friendName + " to " + $("$currentRoom")); }
    });
}

/*
Event handler for the slider pane's button.
Handles room creation, friend request sending, and room invitations.
*/
function addCreate() {
        let mode = $("#sliderAction").html();
        let name = $("#addName").val();
        
        if (name.trim() === "") {
            return false;
        }
        if (mode === "Create") {
            createRoom(name);
            closeSlider();
        }
        else if(mode === "Send Request") {
            console.log("Calling sendFriendRequest function.");
            sendFriendRequest(name);
            closeSlider();
        }
        else if (mode === "Invite") {
            addToRoom(name);
            closeSlider();
        }
        $("#addName").val("");
        $("#optionList").html("");
}

//Event listener for the sidebars icons and rooms
document.addEventListener("click", function(event) {
    let src = event.target;
    if (src.classList[0] === "fa") {
        if ("leaveRoom" in src.dataset) {
            let name = src.parentElement.childNodes[0].innerText;
            $.ajax({
                url: relativeRoot + "roomHandler.php",
                type: "GET",
                async: true,
                data: {
                    request: "leaveRoom",
                    roomName: "" + name + ""
                },
                datatype: "JSON",
                complete: function(data) { 
                    refreshRoomList();
                    clearMessages();
                    clearRoom();
                },
                failure: function(data) { alert("Unable to leave room: " + name); }
            });
        }
        else if ("addToRoom" in src.dataset) {
            roomInvite = src.parentElement.childNodes[0].innerText;
            setSliderMode("addToRoom");
            openSlider();
        }
        else if ("deleteFriend" in src.dataset) {
            let name = src.parentElement.id;
            deleteFriend(name);
        }
        else if ("approveFriendRequest" in src.dataset) {
            let name = src.parentElement.id;
            console.log("Accept request from " + name);
            approveFriendRequest(name);
        }
    }
    else if ("toRoom" in src.dataset) {
        let toRoomName = src.parentElement.childNodes[0].innerText;
        let currentRoom = $("#roomName").val();
        if (toRoomName !== currentRoom) {
            let message = {
                username: $("#username").val(),
                channel: currentRoom,
                message: "",
                action: "MoveToChannel",
                channelTo: toRoomName
            };
            websocket.send(JSON.stringify(message));
            clearMessages();
            $("#roomName").val(toRoomName);
        }
    }
});

function createSocket() {
    let socket = new WebSocket("ws://localhost:8080/php/manager_classes/socketServer.php");
    initializeSocketEventHandlers(socket);
    return socket;
}

function onOpen() {
    let userInfo = {
        username: $("#username").val(),
        channel: $("#roomName").val()
    };
    websocket.send(JSON.stringify(userInfo));
}

function onMessage(event) {
    let data = JSON.parse(event.data);
    let username = $("#username").val();
    let senderId = data["username"];
    if (senderId === username) {
        displayMessage(data["message"], "self", data["time"], data["messageId"], data["username"]);
    }
    else {
        displayMessage(data["message"], "other", data["time"], data["messageId"], data["username"]);
    }
};

function displayErrorMessage(message) {
    displayMessage(message, "self", Date(), 0, $("#username").val());
}

function initializeSocketEventHandlers(socket) {
    socket.onerror = (event) => displayErrorMessage("An error occurred when attempting to connect to the chat server.\nTry reloading the page.");
    socket.onopen = onOpen;
    socket.onmessage = onMessage;
}

function attemptSocketConnection() {
    clearTimeout(attemptSocketConnection);
    console.log("Attempting to create a socket and connect it to the server.");
    websocket = createSocket();
}

/*
 * Handles a failure to send a message.
 * A message may fail to send because the websocket is undefined, or its readyState is not open.
 * This function's responsibility is to let the user know why their message failed to send, and
 * attempt to establish a new socket connection if needed.
 */
function handleSendMessageFailure() {
    if (websocket == null || websocket.readyState === CLOSED || websocket.readyState === CLOSING) {
        displayErrorMessage("An error occurred when sending your message to the chat server.\nAttempting to reconnect.");
        setTimeout(attemptSocketConnection, 3000);
    }
    else if (websocket.readyState === CONNECTING) {
        displayErrorMessage("Currently Connecting to the server.");
    }
}

/*
 * Sends message to websocket.
 * Returns true if the message was successfully sent, else false.
 */
function sendMessage(message) {
    if (websocket != null && websocket.readyState === OPEN) {
        websocket.send(JSON.stringify(message));
        return true;
    }
    return false;
}

$(document).ready(function() {
    websocket = createSocket();

    //Add handler for the slider pane's buttons
    $("#sliderAction").attr("onclick", "addCreate()");
    $("#closeBtn").attr("onclick", "closeSlider()");

    $("#addRoomBtn").on("click", function() {
        setSliderMode("addChat");
        openSlider();
    });
    
    $("#addFriendBtn").on("click",  function() {
        console.log("add friend btn clicked.");
        setSliderMode("addFriend");
        openSlider();
    });
    
    //Lookup usernames based on #addName's value
    $("#addName").keyup(function(event) {
        
        //don't send empty strings
        if ($("#addName").val().trim().length === 0) {
            //clear the suggestions
            $("#optionList").html("");
            return false;
        }
        else if ($("#sliderAction").html() === "Send Request" || $("#sliderAction").html() === "Invite") {
            $.ajax({
                url: relativeRoot + "friendHandler.php",
                type: "GET",
                async: true,
                data: {
                    request: "searchFriend",
                    friendName: $("#addName").val()
                },
                datatype: "HTML",
                success: function(data) { $("#optionList").html(data); },
                failure: function(data) { console.log("Failed to search for friend: " + $("#addName").val()); }
            });
        }
    });
    
    $("#sendMessageButton").on("click", function() {
        let userInfo = {
            username: $("#username").val(),
            channel: $("#roomName").val(),
            message: $("#message").val()
        };
        //send the username and channel, so that server can store accordingly
        if (sendMessage(userInfo)) {
            $("#message").val("");
        }
        else {
            handleSendMessageFailure();
        }
    });
    
    //Add enter button listener for message input
    $("#message").on("keyup", function(event) {
        event.preventDefault();
        if (event.keyCode === 13) {
            let userInfo = {
                username: $("#username").val(),
                channel: $("#roomName").val(),
                message: $("#message").val()
            };
            //send the username and channel, so that server can store accordingly
            if (sendMessage(userInfo)) {
                $("#message").val("");
            }
            else {
                handleSendMessageFailure();
            }
        }
    });
});