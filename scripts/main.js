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
    console.log("Refresh Friends");
    $("#friendsCollapse").load("../php/friends/getFriends.php");
}


function deleteFriend(friendName) {
    let username = friendName.id
    $.ajax({
        url: "../php/friends/killFriend.php",
        type: "POST",
        async: true,
        data: {friend: username},
        datatype: "JSON",
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

    
    function displayMessage(message,classStyle,time,id) {
        if (message == "") {
            return;
        }
        else {
                 //Create and append the message to the chat
            var codeBlock ='<li id ="'+id+'" class="'+classStyle+'"><div class="avatar"><img src="../imgs/user.png" /></div><div class="messages"><p>'+message+'</p><time>'+time+'</time></div></li>';
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
        $.post("../php/message.php", {roomId, roomId}, function(data){ 
                    if(data==""){
                        
                    }
                    else{
                        var string = data;
                        var allData = new Array();
                        var allData = string.split(",");
                        for (var i=0; i<allData.length; i++) {
                            temp = new Array();
                            var classStyle = "other";
                            var temp = allData[i].split("/");
                            if(temp[1]==userId){
                                classStyle="self";
                            }
                            if($('#'+temp[3]).length){
                            
                            }
                            else{
                                displayMessage(temp[0],classStyle,temp[2],temp[3]);
                            }
                        };
                    }
                  
        }); 
      setTimeout('update()', 500);
}
    
$(document).ready(function() {
    
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
        let mode = document.getElementById("sliderAction").innerHTML;
        let name = document.getElementById("addName").value;
        
        if (name.trim() === "") {
            return false;
        }
        else if(mode === "Send Request") {
            console.log("Sending Request\n");

            $.ajax({
                url: "../php/friends/addFriend.php", 
                type: "POST",
                async: true,
                data: { 
                    receiver: ""+name+""
                },
            });
            
            refreshFriendsList();
            closeSlider();
        }
    }
    
    $("#sliderAction").attr("onclick", "addCreate()");

    update();
});