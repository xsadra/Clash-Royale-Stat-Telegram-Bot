<?php


function send($text) {
    return apiRequest("sendMessage", array('chat_id' => '107616269', "text" => $text));
}

function sendChatAction($chat_id, $action) {
    // typing, upload_photo, record_video ,upload_video, record_audio, upload_audio, upload_document, find_location , record_video_note, upload_video_note
    return apiRequest("sendChatAction", array('chat_id' => $chat_id, 'action' => $action));
}

function sendMessage($chat_id, $text) {
    return apiRequest("sendMessage", array('chat_id' => $chat_id, 'parse_mode' => 'Markdown', "text" => $text));
}

function sendMessageNoNot($chat_id, $text) {
    return apiRequest("sendMessage", array('chat_id' => $chat_id, 'parse_mode' => 'Markdown', 'disable_notification' => true, "text" => $text));
}

function sendMessageNoWeb($chat_id, $text) {
    return apiRequest("sendMessage", array('chat_id' => $chat_id, 'parse_mode' => 'Markdown', 'disable_web_page_preview' => true, "text" => $text));
}

function sendMessageKey($chat_id, $text, $keyboard) {
    return apiRequest("sendMessage", array('chat_id' => $chat_id, 'parse_mode' => 'Markdown', "text" => $text, 'reply_markup' => $keyboard));
}

function sendReplyMessage($chat_id, $message_id, $text) {
    return apiRequestWebhook("forwardMessage", array('chat_id' => $chat_id, 'parse_mode' => 'Markdown', "reply_to_message_id" => $message_id, "text" => $text));
}

function sendForwardMessage($chat_id, $from_chat_id, $message_id) {
    return apiRequest("forwardMessage", array('chat_id' => $chat_id, 'from_chat_id' => $from_chat_id, 'message_id' => $message_id));
}

function sendAudio($chat_id, $audio, $caption) {
    return apiRequest("sendAudio", array('chat_id' => $chat_id, 'audio' => $audio, 'caption' => $caption));
}

function sendPhoto($chat_id, $photo, $caption) {
    return apiRequest("sendPhoto", array('chat_id' => $chat_id, 'photo' => $photo, 'caption' => $caption));
}

function sendPhotoKey($chat_id, $photo, $caption, $keyboard) {
    return apiRequest("sendPhoto", array('chat_id' => $chat_id, 'photo' => $photo, 'caption' => $caption, 'reply_markup' => $keyboard));
}

function sendVideo($chat_id, $video, $caption) {
    return apiRequest("sendVideo", array('chat_id' => $chat_id, 'video' => $video, 'caption' => $caption));
}

function sendVideoKey($chat_id, $video, $caption, $keyboard) {
    return apiRequest("sendVideo", array('chat_id' => $chat_id, 'video' => $video, 'caption' => $caption, 'reply_markup' => $keyboard));
}

function answerCallbackQuery($callback_query_id, $text, $show_alert = false) {
    return apiRequest("answerCallbackQuery", array('callback_query_id' => $callback_query_id, 'text' => $text, 'show_alert' => $show_alert));
}

function deleteMessage($chat_id, $message_id) {
    return apiRequest("deleteMessage", array('chat_id' => $chat_id, 'message_id' => $message_id));
}

function getUserProfilePhotos($user_id) {
    return apiRequest("getUserProfilePhotos", array('user_id' => $user_id));
}

/* *************** Controller *************** */
function logi($log) {
    file_put_contents("log/_Log" . time() . ".txt", print_r($log, true));
}

function logTo($log, $fileName) {
    file_put_contents("log/$fileName.txt", print_r($log, true));
}

function logAll($log) {
    file_put_contents('log/log_all.txt', print_r($log, true), FILE_APPEND);
    file_put_contents('log/log_all.txt', print_r("\n\n--------------------\n", true), FILE_APPEND);
}

function isBlockedUser($chat_id) {
    $path = "users/info/block.txt";
    $blockedIds = file_get_contents($path);
    return strpos($blockedIds, trim($chat_id)) !== false;
}

function blockUser($message) {
    $chat_id = $message['chat']['id'];

    if (!isset($message['reply_to_message']['forward_from'])) {
        $msg = getFailedBlockedUserNotification();
        sendMessage($chat_id, $msg);
        return;
    }
    $forward_from_id = $message['reply_to_message']['forward_from']['id'];
    saveInBlockedFile($forward_from_id);

    $msg = getSuccessBlockedUserNotification();
    sendMessage($chat_id, $msg);
}

function sendMessageToUser($message) {
    if (!isset($message['reply_to_message']['forward_from'])) {
        return;
    }
    $forward_from_id = $message['reply_to_message']['forward_from']['id'];
    $text = $message['text'];
    $text = str_replace("/admin","", $text);
    sendMessage($forward_from_id, $text);
}

function saveInBlockedFile($id) {
    file_put_contents('users/info/block.txt', $id . "\n", FILE_APPEND);
}

function saveNewMessageInfo($message) {
    $text = $message['text'];
    $from_id = $message['from']['id'];
    $from_username = $message['from']['username'];
    $from_first_name = $message['from']['first_name'];
    $from_last_name = isset($message['from']['last_name']) ? $message['from']['last_name'] : "";
    $message_date = date("Y/n/d - G:i:s", $message['date']);
    $info = $from_id . " ;\t " . $from_username . " ;\t " . $from_first_name . " ;\t " . $from_last_name . " ;\t " . $message_date . " ;\t " . $text . "\n";
    file_put_contents('botInfo/allMessages.txt', print_r($info, true), FILE_APPEND);
}

function saveStartState($message) {
    $from_username = $message['from']['username'];
    $from_first_name = $message['from']['first_name'];
    $chat_id = $message['chat']['id'];

    file_put_contents('botInfo/start_info_bot.txt', print_r($chat_id . " - " . $from_first_name . " - " . $from_username . "\n", true), FILE_APPEND);
    file_put_contents('botInfo/start_id.txt', print_r($chat_id . "\n", true), FILE_APPEND);
}

function getInlineMarkupWithLinkTo($text, $url) {
    return array(
        'inline_keyboard' => array(
            array(
                array('text' => $text, 'url' => $url)
            )
        )
    );
}
