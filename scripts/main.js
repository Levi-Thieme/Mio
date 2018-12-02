
  function update()
{  
   
    $.post("../php/message.php", {}, function(data){ 
    
                var string = data;
                var allData = new Array();
                var allData = string.split(",");
                for (var i=0; i<allData.length; i++) {
                    temp = new Array();
                    var classStyle = "other";
                    var temp = allData[i].split("/");
                    if(temp[1]=="1"){
                        classStyle="self";
                    }
                    if($('#'+temp[3]).length){
                        
                    }
                    else{
                        displayMessage(temp[0],classStyle,temp[2],temp[3]);
                    }
                };
    }); 
  setTimeout('update()', 10);
}

$(document).ready(
 
function() 
    {  
     update();
    });

  function insertData() {
    var message=$("#message").val();
     var dt = new Date();
    var currentDate= (dt.getFullYear())+"-"+(dt.getMonth()+1)+"-"+(dt.getDate())+" "+(dt.getHours())+":"+(dt.getMinutes())+":"+(dt.getSeconds());
// AJAX code to send data to php file.
        $.ajax({
            type: "POST",
            async: true,
            url: "../php/main.php",
            data: {message: message, time: currentDate},
            dataType: "JSON",
      
        });
    clearMessage();
}

    
   function displayMessage(message,classStyle,time,id)
   {
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
   
