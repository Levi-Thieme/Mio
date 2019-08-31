
//opens the slider and configures it for creating a chat room.
function openCreateRoomModal() {
    $("#optionList").empty();
    document.getElementById("modalTitle").innerHTML = "Chat Name";
    document.getElementById("modalSubmitBtn").innerHTML = "Create";
    $("#modalSubmitBtn").one("click", function() {
        let newChatName = $("#modalInput").val().trim();
        if (newChatName === "") {
            return;
        }
        $.ajax({
            url: relativeRoot + "roomHandler.php",
            type: "GET",
            async: true,
            data: {
                request: "createRoom",
                roomName: newChatName
            },
            complete: function() { refreshRoomList($("#userId").val()); $("#modalInput").val(""); $("#myModal").hide(); },
            failure: function(data) { alert(data["reason"]); }
        });
    });
    $("#myModal").modal();
}

//opens the slider and configures it for inviting someone to a chat.
function openInviteToChatModal(chatName) {
    $("#optionList").empty();
    document.getElementById("modalTitle").innerHTML = "Invite A Friend to " + chatName;
    document.getElementById("modalSubmitBtn").innerHTML = "Invite";
    $("#modalSubmitBtn").one("click", function() {
        console.log("Invite to " + chatName);
        let selectedNames = Array.from(document.getElementById("optionList").getElementsByClassName("list-group-item active"));
        console.log(selectedNames);
        selectedNames.map(selectedName => {
            let name = selectedName.innerText;
            $.ajax({
                url: relativeRoot + "roomHandler.php",
                type: "GET",
                async: true,
                data: {
                    request: "addToRoom",
                    userToAdd: name,
                    roomName: chatName
                },
                complete: function (data) { $("#modalInput").val("");  $("#myModal").hide();; },
                failure: function (data) { alert("Failed to invite " + name + " to " + $("#currentRoom")); }
            });
        });
    });
    $("#myModal").modal();
}

//opens the slider and configures for sending a friend request.
function openFriendRequestModal() {
    $("#optionList").empty();
    document.getElementById("modalTitle").innerHTML = "Send A Friend Request";
    document.getElementById("modalSubmitBtn").innerHTML = "Send";
    $("#modalSubmitBtn").one("click", function() {
        let selectedNames = Array.from(document.getElementsByClassName("list-group-item active"));
        selectedNames.map(selectedName => {
            let name = selectedName.innerText;
            $.ajax({
                url: relativeRoot + "friendHandler.php",
                type: "GET",
                async: true,
                data: {
                    request: "sendFriendRequest",
                    receiver: name
                },
                dataType: "JSON",
                complete: function () { sendFriendRequestNotification($("#username").val(), name);  },
                failure: function () { alert("Failed to send friend request to " + name); }
            });
            refreshFriendsList($("#userId").val());
            $("#modalInput").val("");
            $("#myModal").hide();
            });
        });
    $("#myModal").modal();
}

$(document).ready(function() {
    $("#modalInput").on("keyup", function(event) {
        if ($("#modalInput").val().trim().length === 0) {
            $("#optionList").html("");
        }
        else {
            searchFriends($("#modalInput").val());
        }
    });

    $("#optionList").click(function(event) {
        if (event.target.tagName === "LI") {
            if (event.target.classList.contains("active")) {
                event.target.classList.remove("active");
            }
            else {
                event.target.classList.add("active");
            }
        }
    });
});

function searchFriends(name) {
    $.ajax({
        url: relativeRoot + "friendHandler.php",
        type: "GET",
        async: true,
        data: {
            request: "searchFriend",
            friendName: name
        },
        datatype: "HTML",
        success: function(data) { $("#optionList").html(data); },
        failure: function(data) { console.log("Failed to search for friend: " + $("#addName").val()); }
    });
}