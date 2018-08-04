<?php
require("phpMQTT.php");
$host = "m10.cloudmqtt.com"; 
$port = 12056;
$username = "zcabjmcz"; 
$password = "ty5p0DBl15bp"; 
$messageMQTT = "";
//MQTT client id to use for the device. "" will generate a client id automatically
$mqtt = new phpMQTT($host, $port, "ClientID".rand()); 
$access_token = 'Kg+BrORXmqZel24gvGKyij8kpdtywALpj8/Qooi9IvlQqEQ46oV03oZ8cHJQ9lSqy2KlvhA4T6q21H9KrcVeAj5YMJOK/8M46MWZ3Dm00E8uSEgZ1vomumFToCtQALP/z1Cd5BbKAHHAqOEKYPbMzgdB04t89/1O/w1cDnyilFU=';
//$proxy = <YOUR_PROXY_FROM_FIXIE>;
//$proxyauth = <YOUR_PROXYAUTH_FROM_FIXIE>;
// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
    // Loop through each event
   foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			
			$bufferMessages = [3];
			// Get text sent
			$text = $event['message']['text'];
			// Get replyToken
			$replyToken = $event['replyToken'];
			$messages01 = [
				'type' => 'text',
				'text' => 'มีอะไรให้รับใช้ครับ'
			];
			$messages03 = [
				'type' => 'text',
				'text' => 'ควย'
			];
			$messages02 = [
				'type' => 'text',
				'text' => 'โอเครจ้า'
			];
			$stickerMessage = [ 
				'type' => 'sticker',
				'packageId' => '1',
				'stickerId' => '106'
			];
			// Build message to reply back
			if ($event['message']['text'] == "สัส"){
				$bufferMessages[0] = $messages03;
			}
			if ($event['message']['text'] == "สวัสดี"){
				$bufferMessages[0] = $messages01;
			}
			if ($event['message']['text'] == "รับทราบ/PointA"){
				if ($mqtt->connect(true,NULL,$username,$password)) {
					$mqtt->publish("/IoT/Parking/PointA/Response/S1","Okay"); // ตัวอย่างคำสั่งเปิดทีวีที่จะส่งไปยัง mqtt server
					$mqtt->close();
					echo 'Okay';
				}
				$bufferMessages[0] = $messages02;
			}
			if($event['message']['text'] == "เริ่มใหม่"){
				if ($mqtt->connect(true,NULL,$username,$password)){
					$mqtt->publish("/IoT/Parking/PointA/Response/S2","Reset"); // ตัวอย่างคำสั่งเปิดทีวีที่จะส่งไปยัง mqtt server
					$mqtt->close();
					echo 'connected';
				}
				$bufferMessages[0] = $stickerMessage;
			}
			
			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => $bufferMessages,
			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer '.$access_token);
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			//curl_setopt($ch, CURLOPT_PROXY, $proxy);
			//curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
			$result = curl_exec($ch);
			curl_close($ch);
			echo $result."\r\n";
		}
	}
}
echo 'OK';
