<?php 
$fields = array(
    "message" => "Hello Arunkumar, your booking for timeslot 11am - 12pm on date 17/10/2023 has been confirmed at 8TH COMPLEX SEMINAR HALL-(8004).",
    "language" => "english",
    "route" => "q",
    "numbers" => "8015130984",
    "sender_id" => "KARESLOTS"
);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode($fields),
  CURLOPT_HTTPHEADER => array(
    "authorization: qI7QQxqDnIrBudGFKwwK64MH4cjDy4YGCLa91FM53ViSGOEhzwsYfMKz7aAG",
    "accept: */*",
    "cache-control: no-cache",
    "content-type: application/json"
    
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
?>