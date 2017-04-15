// Inspired by http://codepen.io/lilgreenland/pen/pyVvqB

var messages = [], //array that hold the record of each string in chat
  lastUserMessage = "", //keeps track of the most recent input string from the user
  botName = 'Chatbot' //name of the chatbot

function newEntry() {
  //if the message from the user isn't empty then run 
  if (document.getElementById("chatbox").value != "") {
    //pulls the value from the chatbox ands sets it to lastUserMessage
    lastUserMessage = document.getElementById("chatbox").value;
    //sets the chat box to be clear
    document.getElementById("chatbox").value = "";
    //sets the variable botMessage in response to lastUserMessage
    callBot(lastUserMessage);
    }
}

function callBot(question){
	addReply("<b>Asking</b> "  + question );
    params = chatbox_params;
    params.question = question;
	jQuery.ajax({
	  url: chatbox_params.chatbox_ajax_url,
      method: 'POST',
	  data: params,
      dataType: 'json',
	  success: function( result ) {
	    console.log( result );
	    addReply("<b>" + botName + ":</b> " + result.answer );
	  }
	});
}

function addReply(answer){
    messages.push(answer );
    for (var i = 1; i < 8; i++) {
      if (messages[messages.length - i])
        document.getElementById("chatlog" + i).innerHTML = messages[messages.length - i];
    }
}

//runs the keypress() function when a key is pressed
document.onkeypress = keyPress;
//if the key pressed is 'enter' runs the function newEntry()
function keyPress(e) {
  var x = e || window.event;
  var key = (x.keyCode || x.which);
  if (key == 13 || key == 3) {
    //runs this function when enter is pressed
    newEntry();
  }
  if (key == 38) {
    console.log('hi')
      //document.getElementById("chatbox").value = lastUserMessage;
  }
}

//clears the placeholder text ion the chatbox
//this function is set to run when the users brings focus to the chatbox, by clicking on it
function placeHolder() {
  document.getElementById("chatbox").placeholder = "";
}