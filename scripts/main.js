/* Open the sidenav */
function openSlider() {
    document.getElementById("slider").style.width = "100%";
}
    
/* Close/hide the sidenav */
function closeSlider() {
    document.getElementById("slider").style.width = "0";
}

function setSliderMode(mode) {
    if (mode === "addFriend") {
        document.getElementById("sliderName").innerHTML = "Friend Name";
        document.getElementById("sliderAction").innerHTML = "Send Request";
    }
    else if (mode === "addChat") {
        document.getElementById("sliderName").innerHTML = "Chat Name";
        document.getElementById("sliderAction").innerHTML = "Create";
    }
}

function refreshFriendsList() {
    console.log("refresh");
    $.ajax({
        url: "../php/friends/getFriends.php",
        type: "POST",
        datatype: "html",
        async: true,
        timeout: 2000,
        success: function(data) {
            $("#friendsCollapse").html(data);
        }
    });
}

var addCreate;
    
$(document).ready(function(){
    
    //Add handler for slider
    $("#createChatBtn").on("click", function() {
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
        else {
            $.ajax({
                url: "../php/friends/searchFriend.php",
                type: "GET",
                datatype: "html",
                async: true,
                timeout: 2000,
                data: {
                    friendName: $("#addName").val()
                },
                success: function(data) {
                    $("#optionList").html(data);
                }
            });
        }
    });
    
    
    addCreate = function() {
        let mode = document.getElementById("sliderAction").innerHTML;
        let name = document.getElementById("addName").value;
        
        if (name.trim() === "") {
            return false;
        }
        if (mode === "Create") {
            
        }
        else if(mode === "Send Request") {
            console.log("Sending Request\n");

            $.ajax({
                url: "../php/friends/addFriend.php", 
                type: "POST",
                async: true,
                timeout: 3000,
                data: { 
                    receiver: ""+name+""
                },
            });
            
            refreshFriendsList();
        }
    }
    $("#sliderAction").attr("onclick", "addCreate()");
    
    
    /*
    Click handler for message send button.
    */
    $("#submitButton").click(function() {
        var message = $("#message").val();
        if (message == "") {
            return;
        }
        else {
            //Create and append the message to the chat
            var codeBlock ='<li class="self"><div class="avatar"><img src="../imgs/user.png" /></div><div class="messages"><p>'+message+'</p><time datetime="2009-11-13T20:14">37 mins</time></div></li>';
            $(".discussion").append(codeBlock);
            //Create and append a fake response to the chat
            var codeBlockResponse ='<li class="other"><div class="avatar"><img src="../imgs/user.png" /></div><div class="messages"><p>Sample Response</p><time datetime="2009-11-13T20:14">37 mins</time></div></li>';
            $(".discussion").append(codeBlockResponse);
            //set message input textarea to empty string to clear out the sent message
            $("#message").val("");
            //Focus on message content body
            
        }
    });
});