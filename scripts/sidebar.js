
function joinRoom(userId, username, channelId, channelName) {
    document.getElementById("roomId").value = channelId;
    document.getElementById("roomName").value = channelName;
    let message = {
        action: "JoinChannel",
        clientId: userId,
        username: username,
        channelId: channelId,
        channelName: channelName,
        message: ""
    };
    sendMessage(message);
}

function removeRoom(userId, channelId) {
    $.ajax({
        url: controllersPath + "roomHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "removeRoom",
            clientId: userId,
            channelId: channelId
        },
        datatype: "JSON"
    });
}

function leaveRoom(userId, channelId, username) {
    clearRoom();
    clearMessages();
    let message = {
        action: "LeaveChannel",
        clientId: userId,
        channelId: channelId,
        username: username
    }
    sendMessage(message);
}

//Refreshes the friend list
function refreshFriendsList(userId) {
    console.log("refresh friends list for " + userId + "\n");
    $("#friendsCollapse").html("");
    $.ajax({
        type: "GET",
        url: controllersPath + "friendHandler.php",
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
    let div = document.createElement("div");
    div.classList.add("list-group-item", "roomDiv");
    div.dataset.moveToRoom = roomId;
    div.dataset.roomId = roomId;
    div.dataset.roomName = roomName;
    div.innerText = roomName;
    let leaveIcon = document.createElement("i");
    leaveIcon.dataset.leaveRoom = roomId;
    leaveIcon.classList.add("fa", "fa-trash", "fa-fw");
    let inviteIcon = document.createElement("i");
    inviteIcon.dataset.addToRoom = roomId;
    inviteIcon.dataset.roomName = roomName;
    inviteIcon.classList.add("fa", "fa-plus", "fa-fw");
    div.append(leaveIcon);
    div.append(inviteIcon);
    return div;
}

//Refreshes the room list
function refreshRoomList(userId) {
    $.ajax({
        type: "GET",
        url: controllersPath + "/roomHandler.php",
        async: true,
        data: {
            request: "getRooms",
            userId: userId
        },
        success: function(data) {
            let roomList = document.getElementById("roomList");
            roomList.innerHTML = "";
            let rooms = JSON.parse(data);
            let roomNotSelected = true;
            rooms.forEach((room)=> {
                let roomDiv = createRoomDiv(room.id, room.name);
                if (room.id === $("#roomId").val()) {
                    roomDiv.classList.add("active");
                    roomNotSelected = false;
                }
                roomList.append(roomDiv);
                roomDiv.parentNode = roomList;
            });
            if (roomList.children.length > 0 && roomNotSelected) {
                let room = roomList.firstChild;
                joinRoom($("#userId").val(), $("#username").val(), room.dataset.roomId, room.dataset.roomName);
                $("#roomId").val(room.dataset.roomId);
                $("#roomName").val(room.dataset.roomName);
                room.classList.add("active");
            }
        },
        failure: function(data) { alert("Unable to load room list."); },
    });
}

//Deletes a friend
function deleteFriend(userId, friendName, onComplete, onFailure) {
    console.log("Delete " + friendName);
    $.ajax({
        url: controllersPath + "friendHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "deleteFriend",
            userId: userId,
            friendName: ""+friendName+""
        },
        datatype: "JSON",
        complete: onComplete,
        failure: onFailure
    });
}

//Approves a friend request
function approveFriendRequest(userId, requesterName, onComplete, onFailure) {
    $.ajax({
        url: controllersPath + "friendHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "acceptFriendRequest",
            userId: userId,
            requester: ""+requesterName+""
        },
        dataType: "JSON",
        complete: onComplete,
        failure: onFailure
    });
}

function setAllNodesHidden(nodeClass) {
    let nodes = document.querySelectorAll(nodeClass);
    nodes.forEach((node) => node.style.visibility = "hidden");
}

function setAllNodesVisible(nodeClass) {
    let nodes = document.querySelectorAll(nodeClass);
    nodes.forEach((node) => node.style.visibility = "visible");
}

function showMiniSidebar(style) {
    let sidebar = document.getElementById("sidebar");
    sidebar.style.width = style.minWidth;
    setAllNodesHidden(".sidebarPanel")
    let hideShowDiv = document.getElementById("hideShowDiv");
    hideShowDiv.style.visibility = "visible";
    let gridContainer = document.getElementById("pageGridContainer");
    gridContainer.style.gridTemplateColumns = "60px auto";
}

function showDefaultSidebar() {
    let gridContainer = document.getElementById("pageGridContainer");
    gridContainer.style.gridTemplateColumns = "300px auto";
    let sidebar = document.getElementById("sidebar");
    setAllNodesVisible(".sidebarPanel");
    let style = window.getComputedStyle(sidebar);
    sidebar.style.width = style.maxWidth;
    
}

$(document).ready(function(){
    $("#signout").on("click", function(){ websocket.close(); });
    //Alternates between the minimized and default sidebar view.
    $("#hideShowSidebarBtn").on("click", function() {
        let sidebar = document.getElementById("sidebar");
        let style = window.getComputedStyle(sidebar);
        if (sidebar.style.width === style.maxWidth) {
            showMiniSidebar(style);
        }
        else {
            showDefaultSidebar();
        }
    });

    //Event Listener for room divs
    let roomList = document.getElementById("roomList");
    roomList.addEventListener("click", function(event) {
        let userId = document.getElementById("userId").value;
        let username = document.getElementById("username").value;
        let clickedElement = event.target;
        if ("moveToRoom" in clickedElement.dataset) {
            let roomId = clickedElement.dataset.roomId;
            let roomName = clickedElement.dataset.roomName;
            let currentRoomId = document.getElementById("roomId").value;
            if (roomId != currentRoomId) {
                //if the client is currently in a room, then remove them
                if (currentRoomId) {
                    leaveRoom(userId, currentRoomId, username);
                }
                removeClassFromChildren(roomList, "active");
                clickedElement.classList.add("active");
                joinRoom(userId, username, roomId, roomName);
            }
        } else if ("addToRoom" in clickedElement.dataset) {
            openInviteToChatModal(clickedElement.dataset.roomName);
        } else if ("leaveRoom" in clickedElement.dataset) {
            let channelIdToRemove = clickedElement.dataset.leaveRoom;
            roomList.removeChild(clickedElement.parentNode);
            leaveRoom(userId, channelIdToRemove, username);
            removeRoom(userId, channelIdToRemove);
            
            //TODO If this person was the room owner, then tell the ChannelManager to
            // kick out any other users currently in the room.

            if (roomList.children.length > 0) {
                let username = document.getElementById("username").value;
                let firstRoom = roomList.firstChild;
                let roomId = firstRoom.dataset.roomId;
                let roomName = firstRoom.dataset.roomName;
                joinRoom(userId, username, roomId, roomName);
                firstRoom.classList.add("active");
            }
        }
    });

//removes className from node's children
function removeClassFromChildren(parent, className) {
    let children = $(parent).children();
    for (let i = 0; i < children.length; i++) {
        $(children[i]).removeClass(className);
    }
}

    //Event listener for the sidebar friend related icons
    document.addEventListener("click", function(event) {
        let src = event.target;
        let userId = $("#userId").val();
        if ("deleteFriend" in src.dataset) {
            let name = src.parentElement.id;
            deleteFriend(userId, name,
                function(data) { refreshFriendsList(userId); },
                function(data) { alert("Failed to delete friend: " + name);}
            );
        }
        else if ("approveFriendRequest" in src.dataset) {
            let name = src.parentElement.id;
            approveFriendRequest(userId, name,
                function(data) { refreshFriendsList(userId); },
                function(data) { alert("Failed to approve friend request from" + name + "."); }
            );
        }
    });
});