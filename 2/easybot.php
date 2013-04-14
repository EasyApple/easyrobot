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
    if (isset ($_REQUEST['say']))
    {
      $say = $_REQUEST['say'];
    }
    else
    {
      $say = $s;
    }

    if(empty($display))
    {
      $display = "What are you saying about?";
    }
    return $display;
  }

  private function learn($say,$reply)
  {
    
  }
}

?>