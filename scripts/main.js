
var currentRoom = "";

/* Open the sidenav */
function openSlider() {
    document.getElementById("slider").style.width = "100%";
}
    
/* Close/hide the sidenav */
function closeSlider() {
    document.getElementById("slider").style.width = "0";
}

function setSliderAction(action) {
    document.getElementById("sliderAction").innerHTML = action;
}

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
    $("#friendsCollapse").load("../php/friends/getFriends.php");
}

function refreshRoomList() {
    $("#roomCollapse").load("../php/mainRequests/getRooms.php");
}

//Event listener for leaving rooms
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
                complete: function(data) {
                    refreshRoomList();
                }
            });
        }
        if ("addToRoom" in src.dataset) {
            let name = src.parentElement.textContent;
            currentRoom = name;
            setSliderMode("addToRoom");
            openSlider();
        }
    }
});


function deleteFriend(friendName) {
    let username = friendName.id;
    $.ajax({
        url: "../php/friends/killFriend.php",
        type: "POST",
        async: true,
        data: {friend: username},
        datatype: "JSON"
    });
    refreshFriendsList();
}


var addCreate;


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
  
    });
    clearMessage();
}


function displayMessage(message,classStyle,time,id,name) {
    if (message == "") {
        return;
    }
    else {
        var codeBlock ='<li id ="'+id+'" class="'+classStyle+'"><div class="avatar"><img src="../imgs/user.png" /></div><div class="messages"><p id = "username">'+name+'</p><p>'+message+'</p><time>'+time+'</time></div></li>';
        $(".discussion").append(codeBlock);
        //set message input textarea to empty string to clear out the sent message

     
    }
}

function clearMessage(){
    $("#message").val("");
}


function update() {  
    var roomId = $("#roomId").val();
    var userId = $("#userId").val();
    $.post("../php/message/message.php", {roomId, roomId}, function(data){ 
                if(data==""){
                    
                }
                else{
                    var string = data;
                    var allData = new Array();
                    var allData = string.split(">>>");
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
    setTimeout('update()', 500);
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
        complete: function(data) { refreshRoomList(); },
        failure: function(data) {alert("Failed to create room.");}
    });
}

/*
Adds a friend with the given name
*/
function addFriend(name) {
    $.ajax({
        url: "../php/friends/addFriend.php", 
        type: "POST",
        async: true,
        data: { receiver: ""+name+"" },
        dataType: "JSON",
        failure: function(data) { alert("Failed to send friend request."); },
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
        complete: function(data) {
            closeSlider();
        },
        failure: function(data) {
            alert("Failed to invite " + friendName + " to " + currentRoom);
        }
    });
}
    
$(document).ready(function() {
    
    //Add handler for slider
    $("#addRoomBtn").on("click", function() {
        setSliderMode("addChat");
        openSlider();
    });
    
    $("#addFriendBtn").on("click",  function() {
        setSliderMode("addFriend");
        openSlider();
    });
    
    $("#closeBtn").attr("onclick", "closeSlider()");
    
    
    //Get results for searching for friends when typing
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
                data: {
                    friendName: $("#addName").val()
                },
                datatype: "HTML",
                success: function(data) {
                    $("#optionList").html(data);
                }
            });
        }
    });
    
    
    addCreate = function() {
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
            addFriend(name);
            closeSlider();
        }
        else if (mode === "Invite") {
            addToRoom(name);
            closeSlider();
        }
        $("#addName").val("");
        $("#optionList").html("");
    };
    
    $("#sliderAction").attr("onclick", "addCreate()");

    update();
});