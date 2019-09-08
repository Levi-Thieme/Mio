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
    div.dataset.toRoom = roomId;
    div.dataset.roomName = roomName;
    div.innerText = roomName;
    let leaveIcon = document.createElement("i");
    leaveIcon.dataset.leaveRoom = roomId;
    leaveIcon.classList.add("fa", "fa-trash", "fa-fw");
    let inviteIcon = document.createElement("i");
    inviteIcon.dataset.addToRoom = roomId;
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
            rooms.forEach((room)=> {
                let roomDiv = createRoomDiv(room.id, room.name);
                if (room.id === $("#roomId").val()) {
                    roomDiv.classList.add("active");
                }
                roomList.append(roomDiv);
                roomDiv.parentNode = roomList;
            });
            if (roomList.children.length === 1) {
                let room = roomList.firstChild;
                $("#roomId").val(room.dataset.toRoom);
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

//Event listener for the sidebars icons and rooms
document.addEventListener("click", function(event) {
    let src = event.target;
    let userId = $("#userId").val();
    if ("leaveRoom" in src.dataset) {
        let roomId = src.dataset.leaveRoom;
        leaveRoom(userId, roomId,
            function(data) { refreshRoomList(userId); clearMessages(); clearRoom(); },
            function(data) { alert("Unable to leave room: " + name); }
        )
    }
    else if ("addToRoom" in src.dataset) {
        let chatName = src.parentElement.innerText;
        openInviteToChatModal(chatName);
    }
    else if ("deleteFriend" in src.dataset) {
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
    else if ("toRoom" in src.dataset) {
        let toRoomId = src.dataset.toRoom;
        let toRoomName = src.innerText;
        let fromRoomId = $("#roomId").val();
        if (toRoomId !== fromRoomId) {
            removeClassFromChildren($("#roomList"), "active");
            $(src).addClass("active");
            gotoRoom($("#userId").val(), $("#username").val(), $("#roomName").val(), toRoomId, toRoomName);
            $("#roomId").val(toRoomId);
            $("#roomName").val(toRoomName);
            clearMessages();
        }
    }
});

function setAllNodesHidden(nodeClass) {
    let nodes = document.querySelectorAll(nodeClass);
    console.log(nodes);
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
    })
});

function leaveRoom(userId, roomId, onComplete, onFailure) {
    $.ajax({
        url: controllersPath + "roomHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "leaveRoom",
            userId: userId,
            roomId: roomId
        },
        datatype: "JSON",
        complete: onComplete,
        failure: onFailure
    });
}

function gotoRoom(userId, username, currentRoomName, toRoomId, toRoomName) {
    let message = {
        id: userId,
        username: username,
        currentChannelName: currentRoomName,
        message: "",
        action: "MoveToChannel",
        channelToId: toRoomId,
        channelToName: toRoomName
    };
    websocket.send(JSON.stringify(message));
}

//removes className from node's children
function removeClassFromChildren(parent, className) {
    let children = $(parent).children();
    for (let i = 0; i < children.length; i++) {
        $(children[i]).removeClass(className);
    }
}
