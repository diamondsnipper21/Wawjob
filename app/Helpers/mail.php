<?php
/**
* Mail Handler
*
* Mar 7, 2016 - paulz
*/
if ( !function_exists('send_mail') ) {
  function send_mail($to, $from, $cc, $subject, $text, $html = '', $attachments = '') 
  {
    return false;
    
    /*
    if (empty($from)) {
      error_log("send_mail() - Invalid parameter - `from` is empty.");
      return false;
    }

    if (empty($to)) {
      error_log("send_mail() - Invalid parameter - `to` is empty.");
      return false;
    }

    if(trim($subject) == "") {
      $subject = "Hello";
    }

    # Instantiate the client.
    $mg = new Mailgun('your-key'); # @todo 
    $domain = "mailrsc.com";

    $postData = array();
    $postData['from'] = $from;
    $postData['to'] = $to;
    $postData['subject'] = $subject;

    if ($cc)
      $postData['cc'] =$cc;

    if ( $html )
      $postData['html'] = $html;

    if ($text )
      $postData['text'] = $text;

    if ( $is_bcc ) {
      $postData['bcc'] = $cc;
    }

    $postFiles = array();
    if ($attachments) {
      $postFiles["attachment"] = array();
      foreach($attachments as $attachment) {
        // Full path
        $postFiles["attachment"][] = $attachment[0];
      }
    }

    try {
      $result = $mg->sendMessage($domain, $postData, $postFiles);
    } catch (Exception $e) {
      error_log("send_mail() - From: $from, To: $to - " . $e->getMessage());
      return false;
    }

    return $result;
    */
  }
}