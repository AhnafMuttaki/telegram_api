<?php
//------------------ Configs ---------------------------------
$bot_token= "BOT_Token";
$host_name = "localhost";
$database = "telegram_bot";
$username = "root";
$password = "12345";
//-------------------Get Last Update ID------------------------------------------
// $last_update_id_temp = "225170903";
#############
// Create connection
$conn = new mysqli($host_name, $username, $password, $database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$get_last_update_id_query = "SELECT last_update_id FROM telegram_bot.track_update WHERE id = 1";
$result = $conn->query($get_last_update_id_query);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

        $last_update_id_temp = $row["last_update_id"];     
    }
} else {
    echo "0 results";
}
$conn->close();
#############
$last_update_id = $last_update_id_temp + 1;
//------------------ Get New Updates -------------------------------------------
$get_update_json = file_get_contents("https://api.telegram.org/bot$bot_token/getUpdates?offset=$last_update_id");
$msg_updates = json_decode($get_update_json,true);
$msg_array = $msg_updates['result'];
$insert_new_msg_query = "INSERT INTO telegram_bot.message_table(update_id,chat_id,first_name,last_name,msg,msg_date) VALUES";


If(count($msg_array)>0){
    //------------------ Read Updates ----------------------------------------------
    foreach($msg_array as $msg ){

        if($msg['update_id']){
            $update_id_temp = $msg['update_id'];    
        }
        else{
            $update_id_temp = 0;
        }
        if($msg['message']['chat']['id']){
            $chat_id_temp = $msg['message']['chat']['id'];
        }
        else{
            $chat_id_temp = 0;
        }
        if($msg['message']['from']['first_name']){
            $first_name_temp = $msg['message']['from']['first_name'];
        }else{
            $first_name_temp = 'NA';
        }
        if(array_key_exists('last_name', $msg['message']['from'])){
            $last_name_temp = $msg['message']['from']['last_name'];
        }
        else{
            $last_name_temp = 'NA';
        }
        
        if($msg['message']['text']){
            $message_temp = $msg['message']['text'];
        }else{
            $message_temp = 'NA';
        }
        if($msg['message']['date']){
            $date = $msg['message']['date'];
            //date_default_timezone_set("Asia/Dhaka");
            $dt = new DateTime("@$date",new DateTimezone('Asia/Dhaka'));  // convert UNIX timestamp to PHP DateTime
            $date_temp = $dt->format('Y-m-d H:i:s');
        }else{
            $date_temp = '0000-00-00 00:00:00';
        }
        
        //--------------- Prepare Reply -------------------------

        $reply = prepare_reply($message_temp);

        //-------------- Send Reply -----------------------------

        sendMessage($chat_id_temp,$reply, $bot_token);
        //-------------Prepare Insert Log------------------------
        $insert_new_msg_query .= "($update_id_temp,$chat_id_temp,"
                                ."'".$first_name_temp
                                ."','"
                                .$last_name_temp
                                ."','"
                                .$message_temp
                                ."','"
                                .$date_temp
                                ."'),";
        
        $last_update_id = $update_id_temp;

    }

    //------------------ Push Insert Log To DB ----------------
    $insert_new_msg_query = trim($insert_new_msg_query,','); 

    // Create connection

    $conn = mysqli_connect($host_name, $username, $password, $database);

    // Check connection

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    echo "<br>";
    echo "Connected successfully";
    
    if (mysqli_query($conn, $insert_new_msg_query)) {
        echo "<br>";
        echo "New record created successfully";
    } else {
        echo "<br>";
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    mysqli_close($conn);



    //--------------------- Update last update id ----------------
    ###################
    // Create connection
    $conn = new mysqli($host_name, $username, $password, $database);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    $update_last_update_track_query = "UPDATE telegram_bot.track_update SET last_update_id = $last_update_id WHERE id=1";
    $result = $conn->query($update_last_update_track_query);
    $conn->close();
    ###################

    

}else{
    echo "no new message";
}
//------------------- Utility Functions ----------------------
function sendMessage($chatID, $messaggio, $token) {
    echo "<br>";
    echo "sending message to " . $chatID;


    $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatID;
    $url = $url . "&text=" . urlencode($messaggio);
    $ch = curl_init();
    $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
    );
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    curl_close($ch);
}

function prepare_reply($msg){
    if($msg == 'Hi'){
        return "I dnt have time for HI/Hello";
    }
    elseif($msg == 'Hellow'){
        return "I dnt have time for HI/Hello";
    }
    elseif($msg == 'How are you?'){
        return "I am not well";
    }
    else{
        return "stop this bullshit !!";
    }

}
?>
