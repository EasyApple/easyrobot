<?php


class EasyRobot
{
  public function reply($s)
  {
    /*
    $display = "";
    $thisFile = __FILE__;
    $say = $s;
    require_once ('config/global_config.php');
    require_once ('chatbot/conversation_start.php');
    */

/*
    $thisFile = __FILE__;
    session_start();
    $time_start = microtime(true);
    require_once ("config/global_config.php");
    //load shared files
    include_once (_LIB_PATH_ . "db_functions.php");
    include_once (_LIB_PATH_ . "error_functions.php");
    //load all the chatbot functions
    include_once (_BOTCORE_PATH_ . "aiml" . $path_separator . "load_aimlfunctions.php");
    //load all the user functions
    include_once (_BOTCORE_PATH_ . "conversation" . $path_separator . "load_convofunctions.php");
    //load all the user functions
    include_once (_BOTCORE_PATH_ . "user" . $path_separator . "load_userfunctions.php");
    //load all the user addons
    include_once (_ADDONS_PATH_ . "load_addons.php");
    //open db connection
    $con = db_open();
    //initialise globals
    $convoArr = array();
    $display = "";
    //if the user has said something
    */
    if (trim($s) != ""))
    {
      $say = trim($s);
      //add any pre-processing addons
      $say = run_pre_input_addons($convoArr, $say);
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
    }
    /*
    if(empty($display))
    {
      $display = "EasyRobot is being developed...";
    }
    */
    return $display;
  }

  private function learn($say,$reply)
  {
    
  }
}

?>