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
    let userInfo = {
        username: $("#username").val(),
        channel: $("#roomName").val()
    };
    websocket.send(JSON.stringify(userInfo));
}

function dateTimestamp() {
    let date = new Date();
    let hour = date.getHours() > 12 ? date.getHours() - 12 : date.getHours();
    let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    let days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    return days[date.getDay()] + " " + months[date.getMonth()] + " " + date.getDate() + ", " + date.getFullYear() + " " + hour + ":" + date.getMinutes() + ":" + date.getSeconds();
}

function onMessage(event) {
    let data = JSON.parse(event.data);
    let username = $("#username").val();
    let senderId = data["username"];
    if (senderId === username) {
        displayMessage(data["message"], "self", dateTimestamp(), data["messageId"], data["username"]);
    }
    else {
        displayMessage(data["message"], "other", dateTimestamp(), data["messageId"], data["username"]);
    }
}

function initializeSocketEventHandlers(socket) {
    socket.onerror = (event) => displayErrorMessage("An error occurred when attempting to connect to the chat server.\nTry reloading the page.");
    socket.onopen = onOpen;
    socket.onmessage = onMessage;
}

function attemptSocketConnection() {
    clearTimeout(attemptSocketConnection);
    console.log("Attempting to create a socket and connect it to the server.");
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

function getMessageTypes() {
    return ["", "notifyFriendRequest", "MoveToChannel"];
}

function sendFriendRequestNotification(fromUsername, toUsername) {
    let message = {
        action: "notifyFriendRequest",
        username: fromUsername,
        toUsername: toUsername
    };
    sendMessage(message);
}