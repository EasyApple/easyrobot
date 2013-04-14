<?php


class EasyRobot
{
  public function reply($s)
  {
    $display = "";
    $thisFile = __FILE__;

    require_once ('config/global_config.php');
    require_once ('chatbot/conversation_start.php');
    

    if(empty($display))
    {
      $display = '<script type="text/javascript"><!--
  google_ad_client = "ca-pub-3914685173905728";
  /* 长条横幅2 */
  google_ad_slot = "8282754143";
  google_ad_width = 728;
  google_ad_height = 90;
  //-->
  </script>
  <script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
  </script>'."EasyRobot is being developed...";
    }
    return $display;
  }

  private function learn($say,$reply)
  {
    
  }
}

?>