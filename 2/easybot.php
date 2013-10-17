<?php


class EasyRobot
{
  public function reply($s)
  {
    $display = "";
    $thisFile = __FILE__;
    $say = $s;
    $_GET['say'] = $s;
    $_POST['say'] = $s;

    require_once ('config/global_config.php');
    require_once ('chatbot/conversation_start.php');

    if (isset ($_REQUEST['bot_id']))
    {
      $bot_id = $_REQUEST['bot_id'];
    }
    else
    {
      $bot_id = 1;
      $_REQUEST['bot_id'] = $bot_id;
    }
    if (isset ($_REQUEST['convo_id']))
    {
      $convo_id = $_REQUEST['convo_id'];
    }
    else
    {
      //session started in the conversation_start.php
      $convo_id = session_id();
      $_REQUEST['convo_id'] = $convo_id;
    }
    if (isset ($_REQUEST['format']))
    {
      $format = $_REQUEST['format'];
    }
    else
    {
      $format = "html";
      $_REQUEST['format'] = $format;
    }

    //结果输出
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