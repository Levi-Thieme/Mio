//Refreshes the friend list
function refreshFriendsList(userId) {
    $("#friendsCollapse").html("");
    $.ajax({
        type: "GET",
        url: relativeRoot + "friendHandler.php",
        async: true,
        dataType: "HTML",
        data: {
            request: "getFriendDivs",
            userId: userId
        },
        success: function(data) { $("#friendsCollapse").html(data); },
        failure: function(data) { alert("Unable to load friend list."); }
    });
}

function createRoomDiv(roomId, roomName) {
    return $("<div id="+roomId+" class='list-group-item' data-to-room style='background-color: #222; color:white'>" +
        roomName +
        "<i data-leave-room class='fa fa-trash fa-fw' aria-hidden='true'></i>" +
        "<i data-add-to-room class='fa fa-plus fa-fw' aria-hidden='true'></i></div>")[0];
}

//Refreshes the room list
function refreshRoomList(userId) {
    $.ajax({
        type: "GET",
        url: relativeRoot + "/roomHandler.php",
        async: true,
        data: {
            request: "getRooms",
            userId: userId
        },
        success: function(data) {
            let roomList = document.getElementById("roomList");
            roomList.innerHTML = "";
            let rooms = JSON.parse(data);
            rooms.forEach((room)=> {
                let roomDiv = createRoomDiv(room.id, room.name);
                if (room.id === $("#roomId").val()) {
                    roomDiv.classList.add("active");
                }
                roomList.appendChild(roomDiv);
                roomDiv.parentNode = roomList;
            });
        },
        failure: function(data) { alert("Unable to load room list."); },
    });
}

//Deletes a friend
function deleteFriend(userId, friendName) {
    console.log("Delete " + friendName);
    $.ajax({
        url: relativeRoot + "friendHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "deleteFriend",
            userId: userId,
            friendName: ""+friendName+""
        },
        datatype: "JSON",
        failure: function(data) { alert("Failed to delete friend: " + friendName); },
        complete: function(data) { refreshFriendsList(userId); }
    });
}

//Approves a friend request
function approveFriendRequest(userId, requesterName) {
    $.ajax({
        url: relativeRoot + "friendHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "acceptFriendRequest",
            userId: userId,
            requester: ""+requesterName+""
        },
        dataType: "JSON",
        failure: function(data) { alert("Failed to approve friend request from" + requesterName + "."); },
        complete: function(data) { refreshFriendsList(userId); }
    });
}

//Event listener for the sidebars icons and rooms
document.addEventListener("click", function(event) {
    let src = event.target;
    if ("leaveRoom" in src.dataset) {
        let roomId = src.parentElement.id;
        $.ajax({
            url: relativeRoot + "roomHandler.php",
            type: "GET",
            async: true,
            data: {
                request: "leaveRoom",
                userId: $("#userId").val(),
                roomId: roomId
            },
            datatype: "JSON",
            complete: function(data) {
                refreshRoomList($("#userId").val());
                clearMessages();
                clearRoom();
            },
            failure: function(data) { alert("Unable to leave room: " + name); }
        });
    }
    else if ("addToRoom" in src.dataset) {
        let chatName = src.parentElement.childNodes[0].innerText;
        openInviteToChatModal(chatName);
    }
    else if ("deleteFriend" in src.dataset) {
        let name = src.parentElement.id;
        deleteFriend($("#userId").val(), name);
    }
    else if ("approveFriendRequest" in src.dataset) {
        let name = src.parentElement.id;
        approveFriendRequest($("#userId").val(), name);
    }
    else if ("toRoom" in src.dataset) {
        let children = $("#roomList").children();
        for (let i = 0; i < children.length; i++) {
            $(children[i]).removeClass("active");
        }
        let toRoomId = src.id;
        let currentRoomId = $("#roomId").val();
        if (toRoomId !== currentRoomId) {
            $(src).addClass("active");
            let message = {
                username: $("#username").val(),
                channel: $("#roomName"),
                message: "",
                action: "MoveToChannel",
                channelTo: toRoomId
            };
            //websocket.send(JSON.stringify(message));
            clearMessages();
            $("#roomId").val(src.id);
            $("#roomName").val(src.innerText);
        }
    }
});
