/* Open the sidenav */
function openSlider() {
    document.getElementById("slider").style.width = "100%";
}

/* Close/hide the sidenav */
function closeSlider() {
    document.getElementById("slider").style.width = "0";
}

//opens the slider and configures it for creating a chat room.
function openCreateRoomSlider() {
    document.getElementById("sliderTitle").innerHTML = "Chat Name";
    document.getElementById("sliderSubmitBtn").innerHTML = "Create";
    $("#sliderSubmitBtn").one("click", function() {
        let newChatName = $("#sliderInput").val().trim();
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
            complete: function() { refreshRoomList(); $("#sliderInput").val("");  closeSlider(); },
            failure: function(data) { alert(data["reason"]); }
        });
    });
    openSlider();
}

//opens the slider and configures it for inviting someone to a chat.
function openInviteToChatSlider(chatName) {
    document.getElementById("sliderTitle").innerHTML = "Invite A Friend to " + chatName;
    document.getElementById("sliderSubmitBtn").innerHTML = "Invite";
    $("#sliderSubmitBtn").one("click", function() {
        let friendName = $("#sliderInput").val().trim();
        if (friendName === "") {
            return;
        }
        $.ajax({
            url: relativeRoot + "roomHandler.php",
            type: "GET",
            async: true,
            data: {
                request: "addToRoom",
                userToAdd: friendName,
                roomName: chatName
            },
            complete: function (data) { $("#sliderInput").val(""); closeSlider(); },
            failure: function (data) { alert("Failed to invite " + friendName + " to " + $("$currentRoom")); }
        });
    });
    openSlider();
}

//opens the slider and configures for sending a friend request.
function openFriendRequestSlider() {
    document.getElementById("sliderTitle").innerHTML = "Send A Friend Request";
    document.getElementById("sliderSubmitBtn").innerHTML = "Send";
    $("#sliderSubmitBtn").one("click", function() {
        let friendName = $("#sliderInput").val().trim();
        if (friendName === "") {
            return;
        }
        $.ajax({
            url: relativeRoot + "friendHandler.php",
            type: "GET",
            async: true,
            data: {
                request: "sendFriendRequest",
                receiver: friendName
            },
            dataType: "JSON",
            complete: function () { refreshFriendsList(); $("#sliderInput").val(""); closeSlider(); },
            failure: function () { alert("Failed to send friend request to " + friendName); }
        });
    });
    openSlider();
}

//Lookup usernames based on #addName's value
$("#sliderInput").one("keyup", function(event) {
    //don't send empty strings
    if ($("#sliderInput").val().trim().length === 0) {
        //clear the suggestions
        $("#optionList").html("");
        return false;
    }
});

function searchFriends() {
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