<?php


class EasyRobot
{
  public function reply($s)
  {
    $display = "EasyRobot is being developed...";
    $thisFile = __FILE__;
    $say = $s;
    require_once ('config/global_config.php');
    require_once ('chatbot/conversation_start.php');

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