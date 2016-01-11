<?php
/*
//this is what gets sent over to our server through the slack web API(example values are shown)
token=rpxkNKeMfEexwUP5CKTOk9XT
team_id=T0001
channel_id=C2147483705
channel_name=test
timestamp=1355517523.000005
user_id=U2147483697
user_name=Steve
text=googlebot: What is the air-speed velocity of an unladen swallow?
trigger_word=googlebot:
*/
/*GET POST DATA*/
$token = $_POST['token'];
$team_id = $_POST['team_id'];
$channel_id = $_POST['channel_id'];
$channel_name= $_POST['channel_name'];
$user_id = $_POST['user_id'];
$timestamp = $_POST['timestamp'];
$user_name = $_POST['user_name'];
$text = $_POST['text'];
$trigger_word = $_POST['trigger_word'];

/*DATA LOGGING*/
$response = array("token" => $token, "team_id" => $team_id, "channel_id" => $channel_id, "channel_name" => $channel_name, "user_id" => $user_id, "timestamp" => $timestamp, "user_name" => $user_name, "text" => $text, "trigger_word" => $trigger_word);
file_put_contents('logs/response.txt', print_r($response, true), FILE_APPEND);

/*TURN LIGHT ON/OFF*/
if($trigger_word == "#light_on")
{
	//if light is off, turn on light
	if(readLightState() == 0)
	{
		setLightState(1);
	}
	else sendChannelResponse('light already on!');
}
else if($trigger_word == "#light_off")
{
	//if light is on, turn light off
	if(readLightState() == 1)
	{
		setLightState(0);
	}
	else sendChannelResponse('light already off!');
}
else if($trigger_word == "#status")
{
	$lightState = readLightState();
	if($lightState) $state = 'On';
	else $state = 'Off';
	sendChannelResponse('Light state is: ' . $state);
}
else
{
	//other handlers if desired: status, halt, etc
}

function readLightState()
{
	$last_line = system('gpio read 0', $retval);
	return $last_line;
}

function setLightState($state)
{
	$last_line = system('gpio write 0 ' . $state , $retval);
	return $last_line;
}

function sendChannelResponse($message)
{	
	//You would need to replace 'YourToken' , and 'Channel' below to make this
	$c = curl_init();
	$url = 'https://slack.com/api/chat.postMessage?token=' . 'YourToken' . '&channel=' . 'Channel' . '&text=' . $message; 
	$url = str_replace(" ","%20",$url);
	curl_setopt($c, CURLOPT_URL,$url); 
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); 
	$result = curl_exec($c);
	file_put_contents('logs/response.txt', $url, FILE_APPEND);
	file_put_contents('logs/response.txt', $message, FILE_APPEND);
	file_put_contents('logs/response.txt', $result, FILE_APPEND);
}

?>
