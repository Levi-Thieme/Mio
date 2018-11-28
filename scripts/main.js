
  function insertData() {
    var message=$("#message").val();
// AJAX code to send data to php file.
        $.ajax({
            type: "POST",
            url: "../php/main.php",
            data: {message: message},
            dataType: "JSON",
      
        });
    displayMessage();
}

    
   function displayMessage()
   {
        var message = $("#message").val();
        if (message == "") {
            return;
        }
        else {
                 //Create and append the message to the chat
            var codeBlock ='<li class="self"><div class="avatar"><img src="../imgs/user.png" /></div><div class="messages"><p>'+message+'</p><time datetime="2009-11-13T20:14">37 mins</time></div></li>';
            $(".discussion").append(codeBlock);
            //set message input textarea to empty string to clear out the sent message

            $("#message").val("");
        }
   }
