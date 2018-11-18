
$(document).ready(function(){
    
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