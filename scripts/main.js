var roomInvite;
var updateChatTimeoutId;

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
        type: "POST",
        url: "../php/friends/getFriends.php",
        async: true,
        dataType: "HTML",
        success: function(data) { $("#friendsCollapse").html(data); },
        failure: function(data) { alert("Unable to load friend list."); }
    });
}

/*
Refreshes the room list
*/
function refreshRoomList() {
    $.ajax({
        type: "POST",
        url: "../php/mainRequests/getRooms.php",
        async: true,
        dataType: "HTML",
        success: function(data) { $("#roomCollapse").html(data); },
        failure: function(data) { alert("Unable to load room list."); }
    });
}


/*
Sends the user's message to main.php through AJAX
*/
function sendMessage() {
    let message = $("#message").val();
    if (message.trim() == "") {
        return;
    }
    let roomName = $("#roomName").val();
    let date = new Date();
    let currentDate = (date.getFullYear())+"-"+(date.getMonth()+1)+"-"+(date.getDate())+" "+(date.getHours())+":"+(date.getMinutes())+":"+(date.getSeconds());

    $.ajax({
        type: "POST",
        async: true,
        url: "../php/message/storeMessage.php",
        dataType: "JSON",
        data: {
            message: "" + message + "", 
            currentRoom: "" + roomName + "", 
            time: currentDate
        },
        failure: function(data) { console.log("Failed to send message: " + message); },
        success: function(data) { console.log("Message sent.") }
    });
    $("#message").val("");
}

/*
Appends a message div into the chat
*/
function displayMessage(message, classStyle, time, id, name) {
    if (message != "")  {
        let messageItem =
        '<li id ="'+id+'" class="'+classStyle+'">'+
            '<div class="avatar"><img src="../imgs/user.png" /></div>'+
            '<div class="messages"><p id = "username">'+name+'</p><p>'+message+'</p><time>'+time+'</time></div>'+
        '</li>';
        $("#messageList").append(messageItem);
        $("#message").val("");
    }
}

/*
Clears the chat's messages
*/
function clearMessages() {
    $("#messageList").html("");
}

/*
Retrieves messages for a given room and displays them in the chat
*/
function updateChat() {  
    let roomName = $("#roomName").val();
    let userId = $("#userId").val();
    
    $.ajax({
        type: "POST",
        url: "../php/message/getMessages.php", 
        data: { currentRoom: "" + roomName + "" }, 
        complete: function(data) {
            console.log(data.responseText);
            let response = JSON.parse(data.responseText);
            response.map(function(currentElement) {
                console.log(currentElement);
                let senderId = currentElement["userId"];
                if (senderId == userId) {
                    displayMessage(currentElement["content"], "self", currentElement["time"], currentElement["messageId"], currentElement["username"]);
                }
                else {
                    displayMessage(currentElement["content"], "other", currentElement["time"], currentElement["messageId"], currentElement["username"]);
                }
            })
        }
    }); 
    updateChatTimeoutId = setTimeout("updateChat()", 500);
}

/*
Creates a room with given name
*/
function createRoom(name) {
    $.ajax({
        url: "../php/roomBuilder.php",
        type: "POST",
        async: true,
        data: { newRoomName: ""+name+"" },
        failure: function(data) {alert("Failed to create room: " + name);},
        complete: function(data) { refreshRoomList(); }
    });
}

/*
Adds a friend with the given name
*/
function sendFriendRequest(name) {
    $.ajax({
        url: "../php/friends/sendRequest.php", 
        type: "POST",
        async: true,
        data: { receiver: ""+name+"" },
        dataType: "JSON",
        failure: function(data) { alert("Failed to send friend request to " + name); },
        complete: function(data) { refreshFriendsList(); }
    });
}

/*
Deletes a friend
*/
function deleteFriend(friendName) {
    $.ajax({
        url: "../php/friends/deleteFriend.php",
        type: "POST",
        async: true,
        data: { friend: ""+friendName+"" },
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
        url: "../php/friends/approveFriendRequest.php",
        type: "POST",
        async: true,
        data: { requester: ""+requesterName+"" },
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
        url: "../php/rooms/addToRoom.php",
        type: "POST",
        async: true,
        data: {
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
                url: "../php/rooms/leaveRoom.php",
                type: "POST",
                async: true,
                data: { roomName: "" + name + "" },
                datatype: "JSON",
                complete: function(data) { refreshRoomList(); },
                failure: function(data) { alert("Unable to leave room: " + name); }
            });
        }
        else if ("addToRoom" in src.dataset) {
            //TODO update this to use a function in setSliderMode instead of using a global variable for saving state
            roomInvite = src.parentElement.childNodes[0].innerText;
            setSliderMode("addToRoom");
            openSlider();
        }
        else if ("deleteFriend" in src.dataset) {
            let name = src.parentElement.childNodes[0].innerText;
            deleteFriend(name);
        }
        else if ("approveFriendRequest" in src.dataset) {
            let name = src.parentElement.childNodes[0].innerText;
            approveFriendRequest(name);
        }
    }
    else if ("toRoom" in src.dataset) {
        let name = src.parentElement.childNodes[0].innerText;
        if (name !== $("#roomName").val()) {
            clearMessages();
            clearTimeout(updateChatTimeoutId);
            clearMessages();
            console.log("Cleared chat for: " + $("#roomName").val());
            $("#roomName").val(name);
            
            console.log("Switching to room: " + $("#roomName").val());
            updateChat();
        }
    }
});
    
$(document).ready(function() {
    //Add handler for the slider pane's buttons
    $("#sliderAction").attr("onclick", "addCreate()");
    $("#closeBtn").attr("onclick", "closeSlider()");

    $("#addRoomBtn").on("click", function() {
        setSliderMode("addChat");
        openSlider();
    });
    
    $("#addFriendBtn").on("click",  function() {
        setSliderMode("addFriend");
        openSlider();
    });
    
    //Lookup usernames based on #addName's value
    $("#addName").keyup(function(event) {
        
        //don't send empty strings
        if ($("#addName").val().trim().length == 0) {
            //clear the suggestions
            $("#optionList").html("");
            return false;
        }
        else if ($("#sliderAction").html() === "Send Request" || $("#sliderAction").html() === "Invite") {
            $.ajax({
                url: "../php/friends/searchFriend.php",
                type: "POST",
                async: true,
                data: { friendName: $("#addName").val() },
                datatype: "HTML",
                success: function(data) { $("#optionList").html(data); },
                failure: function(data) { console.log("Failed to search for friend: " + $("#addName").val()); }
            });
        }
    });
    
    $("#sendMessageButton").on("click", sendMessage);
    
    //Update the chat pane's content
    updateChat();
});