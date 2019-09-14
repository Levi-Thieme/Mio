var websocket;
let websocketUrl = "ws://localhost:8080/php/chat_server/socketServer.php";
//websocket readyState constants
let CONNECTING = 0;
let OPEN = 1;
let CLOSING = 2;
let CLOSED = 3;
//end websocket readyState constants

function createSocket() {
    let socket = new WebSocket(websocketUrl);
    initializeSocketEventHandlers(socket);
    return socket;
}

function onOpen() {
    setTimeout(function() {
        joinRoom($("#userId").val(), $("#username").val(), $("#roomId").val(), $("#roomName").val());
    }, 1500);
}

function dateTimestamp() {
    return currentDate(true) + " " + timestamp();
}

//return date in weekDay? MM DD, YYYY
function currentDate(includeWeekday) {
    let date = new Date();
    let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    let days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    let day = includeWeekday ? days[date.getDay()] : "";
    return day + " " + months[date.getMonth()] + " " + date.getDate() + ", " + date.getFullYear();
}

//returns time in HH:MM:SS format
function timestamp() {
    let date = new Date();
    let hour = date.getHours() > 12 ? date.getHours() - 12 : date.getHours();
    let minutes = date.getMinutes();
    minutes = minutes < 10 ? "0" + minutes : minutes;
    let seconds = date.getSeconds();
    seconds = seconds < 10 ? "0" + seconds : seconds;
    return hour + ":" + minutes + ":" + seconds;
}

function onMessage(event) {
    let data = JSON.parse(event.data);
    if (data["type"] === "message") {
        let content = data["content"];
        if (content["fromId"] === $("#userId").val()) {
            displayMessage(data["message"], "self", dateTimestamp(), data["fromId"], data["fromUsername"]);
        }
        else {
            displayMessage(data["message"], "other", dateTimestamp(), data["fromId"], data["fromUsername"]);
        }
    }
    else if (data["type"] === "sendFriendNotification" && data["action"] === "newRequest") {
        let content = data["content"];
        displayToast("New Friend Request", "You have a friend request from " + content["fromUsername"]);
    }
    else if (data["type"] === "sendRoomNotification") {
        if (data["action"] === "join") {
            console.log("toast");
            displayToast(data["roomName"], data["fromUsername"] + " has joined " + data["roomName"] + ".");
        }
    }
    else {
        console.log(data);
    }
}

function initializeSocketEventHandlers(socket) {
    socket.onerror = (event) => displayErrorMessage("An error occurred when attempting to connect to the chat server.\nTry reloading the page.");
    socket.onopen = onOpen;
    socket.onmessage = onMessage;
}

function attemptSocketConnection() {
    clearTimeout(attemptSocketConnection);
    websocket = createSocket();
}

/*
 * Handles a failure to send a message.
 * A message may fail to send because the websocket is undefined, or its readyState is not open.
 * This function's responsibility is to let the user know why their message failed to send, and
 * attempt to establish a new socket connection if needed.
 */
function handleSendMessageFailure() {
    if (websocket == null || websocket.readyState === CLOSED || websocket.readyState === CLOSING) {
        displayErrorMessage("An error occurred when sending your message to the chat server.\nAttempting to reconnect.");
        setTimeout(attemptSocketConnection, 3000);
    }
    else if (websocket.readyState === CONNECTING) {
        displayErrorMessage("Currently Connecting to the server.");
    }
}

/*
 * Sends message to websocket.
 * Returns true if the message was successfully sent, else false.
 */
function sendMessage(message) {
    if (websocket != null && websocket.readyState === OPEN) {
        websocket.send(JSON.stringify(message));
        return true;
    }
    return false;
}

/*
Sends a notification that the client with id = toId that clientUsername has
sent them a friend request.
*/
function sendFriendRequestNotification(clientId, clientUsername, toId) {
    let message = {
        clientId: clientId,
        type: "sendFriendNotification",
        action: "newRequest",
        content: {
            toId: toId,
            fromUsername: clientUsername
        }
    };
    sendMessage(message);
}