<?php


class EasyRobot
{
  public function reply($s)
  {
    $display = "";
    $thisFile = __FILE__;

    require_once ('config/global_config.php');
    require_once ('chatbot/conversation_start.php');
    if (isset ($_REQUEST['bot_id']))
    {
      $bot_id = $_REQUEST['bot_id'];
    }
    else
    {
      $bot_id = 1;
    }
    if (isset ($_REQUEST['convo_id']))
    {
      $convo_id = $_REQUEST['convo_id'];
    }
    else
    {
    //session started in the conversation_start.php
      $convo_id = session_id();
    }
    if (isset ($_REQUEST['format']))
    {
      $format = $_REQUEST['format'];
    }
    else
    {
      $format = "html";
    }

    $say = $s;
    //add any pre-processing addons
    $say = run_pre_input_addons($convoArr, $say);
    #die('say = ' . $say);
    runDebug(__FILE__, __FUNCTION__, __LINE__, "Details:\nUser say: " . $_REQUEST['say'] . "\nConvo id: " . $_REQUEST['convo_id'] . "\nBot id: " . $_REQUEST['bot_id'] . "\nFormat: " . $_REQUEST['format'], 2);
    //get the stored vars
    $convoArr = read_from_session();
    //now overwrite with the recieved data
    $convoArr = check_set_bot($convoArr);
    $convoArr = check_set_convo_id($convoArr);
    $convoArr = check_set_user($convoArr);
    $convoArr = check_set_format($convoArr);
    $convoArr['time_start'] = $time_start;
    //if totallines = 0 then this is new user
    if (isset ($convoArr['conversation']['totallines']))
    {
    //reset the debug level here
      $debuglevel = get_convo_var($convoArr, 'conversation', 'debugshow', '', '');
    }
    else
    {
    //load the chatbot configuration
      $convoArr = load_bot_config($convoArr);
      //reset the debug level here
      $debuglevel = get_convo_var($convoArr, 'conversation', 'debugshow', '', '');
      //insita
      $convoArr = intialise_convoArray($convoArr);
      //add the bot_id dependant vars
      $convoArr = add_firstturn_conversation_vars($convoArr);
      $convoArr['conversation']['totallines'] = 0;
      $convoArr = get_user_id($convoArr);
    }
    $convoArr['aiml'] = array();
    //add the latest thing the user said
    $convoArr = add_new_conversation_vars($say, $convoArr);
    //parse the aiml
    $convoArr = make_conversation($convoArr);
    $convoArr = log_conversation($convoArr);
    $convoArr = log_conversation_state($convoArr);
    $convoArr = write_to_session($convoArr);
    $convoArr = get_conversation($convoArr);
    $convoArr = run_post_response_useraddons($convoArr);
    //return the values to display
    $display = $convoArr['send_to_user'];

    if(empty($display))
    {
      $display = "EasyRobot is being developed...";
    }
    return $display;
  }

  private function learn($say,$reply)
  {
    
  }
}

?>