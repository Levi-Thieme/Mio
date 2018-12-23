
//Stores the name of the currently selected room
var currentRoom;

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

function refreshFriendsList() {
    $.ajax({
        type: "POST",
        url: "../php/friends/getFriends.php",
        async: true,
        dataType: "HTML",
        success: function(data) { $("#friendsCollapse").html(data); },
        failure: function(data) { alert("Unable to load friend list."); }
    })
}

function refreshRoomList() {
    $("#roomCollapse").load("../php/mainRequests/getRooms.php");
}

/*
Clears the message input box
*/
function clearMessage(){
    $("#message").val("");
}

/*

*/
function insertData() {
    var message=$("#message").val();
    var dt = new Date();
    var roomId = $("#roomId").val();
    var currentDate= (dt.getFullYear())+"-"+(dt.getMonth()+1)+"-"+(dt.getDate())+" "+(dt.getHours())+":"+(dt.getMinutes())+":"+(dt.getSeconds());
    // AJAX code to send data to php file.
    $.ajax({
        type: "POST",
        async: true,
        url: "../php/main.php",
        data: {message: message, nowRoomId:roomId, time: currentDate},
        dataType: "JSON",
        failure: function(data) { alert("Failed to insert data: " + message); }
    });
    clearMessage();
}

/*
Appends a message div into the chat
*/
function displayMessage(message,classStyle,time,id,name) {
    if (message != "")  {
        var codeBlock ='<li id ="'+id+'" class="'+classStyle+'"><div class="avatar"><img src="../imgs/user.png" /></div><div class="messages"><p id = "username">'+name+'</p><p>'+message+'</p><time>'+time+'</time></div></li>';
        $(".discussion").append(codeBlock);
        //set message input textarea to empty string to clear out the sent message
    }
}

/*
Retrieves messages for a given room and displays them in the chat
*/
function updateChat() {  
    var roomId = $("#roomId").val();
    var userId = $("#userId").val();
    $.post("../php/message/message.php", {roomId, roomId}, function(data){ 
                if(data==""){
                    
                }
                else{
                    var string = data;
                    var allData = new Array();
                    allData = string.split(">>>");
                    for (var i=0; i<allData.length; i++) {
                        temp = new Array();
                        var classStyle = "other";
                        var temp = allData[i].split("//");
                        if(temp[1]==userId){
                            classStyle="self";
                        }
                        if($('#'+temp[3]).length){
                        
                        }
                        else{
                            displayMessage(temp[0],classStyle,temp[2],temp[3],temp[4]);
                        }
                    };
                }
              
    }); 
    setTimeout('updateChat()', 500);
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
            roomName: "" + currentRoom + ""
        },
        complete: function(data) { closeSlider(); },
        failure: function(data) { alert("Failed to invite " + friendName + " to " + currentRoom); }
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
    };

//Event listener for the sidebars icons
document.addEventListener("click", function(event) {
    let src = event.target;
    if (src.classList[0] === "fa") {
        if ("leaveRoom" in src.dataset) {
            let name = src.parentElement.textContent;
            $.ajax({
                url: "../php/rooms/leaveRoom.php",
                type: "POST",
                async: true,
                data: {
                    roomName: "" + name + "",
                    success: false
                },
                datatype: "JSON",
                complete: function(data) { refreshRoomList(); },
                failure: function(data) { alert("Unable to leave room: " + name); }
            });
        }
        else if ("addToRoom" in src.dataset) {
            let name = src.parentElement.textContent;
            currentRoom = name;
            setSliderMode("addToRoom");
            openSlider();
        }
        else if ("deleteFriend" in src.dataset) {
            let name = src.parentElement.textContent;
            deleteFriend(name);
        }
        else if ("approveFriendRequest" in src.dataset) {
            let name = src.parentElement.textContent;
            approveFriendRequest(name);
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
    
    //Update the chat pane's content
    updateChat();
});