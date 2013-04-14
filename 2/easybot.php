<?php

/***************************************
* http://www.program-o.com
* PROGRAM O
* Version: 2.0.9
* FILE: gui/plain/index.php
* AUTHOR: ELIZABETH PERREAU
* DATE: 19 JUNE 2012
* DETAILS: simple example gui
***************************************/
$display = "";
$thisFile = __FILE__;
require_once ('../../config/global_config.php');
require_once ('../chatbot/conversation_start.php');
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


class EasyRobot
{
  public function EasyRobot()
  {
    
  }

  public function reply($say)
  {
    return $display;
  }

  private function learn($say,$reply)
  {
    
  }
}