<?php


class EasyRobot
{
  public function reply($s)
  {
    $display = "";
    $thisFile = __FILE__;

    session_start();
    session_regenerate_id();
    $new_id = session_id();
    session_destroy();

    //TODO WHICH ONE IS IT?
    $_GET['convo_id'] = $new_id;
    $_POST['convo_id'] = $new_id;
    $_REQUEST['convo_id'] = $new_id;
    $_REQUEST['say'] = $s;

    $_REQUEST['bot_id'] = 1;
    $_REQUEST['format'] = "html";

    require_once ('config/global_config.php');
    require_once ('chatbot/conversation_start.php');

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