 var loaded = 0;
  function update()
{
   var dt = new Date();
   var currentDate= (dt.getFullYear())+"-"+(dt.getMonth()+1)+"-"+(dt.getDate())+" "+(dt.getHours())+":"+(dt.getMinutes())+":"+(dt.getSeconds());
    $.post("../php/message.php", {currentDate: currentDate}, function(data){ 
       
                var string = data;
                var allData = new Array();
                allData = string.split(",");
                for (var i=0; i<allData.length; i++) {
                    var temp = new Array();
                    var classStyle = "other";
                    temp = allData[i].split("/");
                    if(temp[1]=="1"){
                        classStyle="self";
                    }
                    displayMessage(temp[0],classStyle);
                };});  
 
    setTimeout('update()', 1000);
}
function loadMessages(){
  
    $.post("../php/message.php", {}, function(data){ 
       
                var string = data;
                var allData = new Array();
                allData = string.split(",");
                for (var i=0; i<allData.length; i++) {
                    var temp = new Array();
                    var classStyle = "other";
                    temp = allData[i].split("/");
                    if(temp[1]=="1"){
                        classStyle="self";
                    }
                    displayMessage(temp[0],classStyle);
                };});  
}
$(document).ready(
 
function() 
    {
    if(loaded==0){
        loadMessages()
        loaded == 1;
    }
     update();
    });

  function insertData() {
    var message=$("#message").val();
     var dt = new Date();
    var currentDate= (dt.getFullYear())+"-"+(dt.getMonth()+1)+"-"+(dt.getDate())+" "+(dt.getHours())+":"+(dt.getMinutes())+":"+(dt.getSeconds());
// AJAX code to send data to php file.
        $.ajax({
            type: "POST",
            url: "../php/main.php",
            data: {message: message, time: currentDate},
            dataType: "JSON",
      
        });
    displayMessage(message,"self");
    clearMessage();
}

    
   function displayMessage(message,classStyle)
   {
        if (message == "") {
            return;
        }
        else {
                 //Create and append the message to the chat
            var codeBlock ='<li class="'+classStyle+'"><div class="avatar"><img src="../imgs/user.png" /></div><div class="messages"><p>'+message+'</p><time datetime="2009-11-13T20:14">37 mins</time></div></li>';
            $(".discussion").append(codeBlock);
            //set message input textarea to empty string to clear out the sent message

         
        }
   }
   function clearMessage(){
       $("#message").val("");
   }
   
