<?php
//include_once('ipaddress.php');
define("TOKEN","easychat");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
  public function valid()
  {
    $echoStr = $_GET["echostr"];
    if($this->checkSignature())
    {
      echo $echoStr;
      exit;
    }
  }

  public function responseMsg()
  {
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    if (!empty($postStr))
    {
      $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
      $fromUsername = $postObj->FromUserName;
      $toUsername = $postObj->ToUserName;
      $keyword = trim($postObj->Content);
      $time = time();                    
      $textTpl = "<xml>
      <ToUserName><![CDATA[%s]]></ToUserName>
      <FromUserName><![CDATA[%s]]></FromUserName>
      <CreateTime>%s</CreateTime>
      <MsgType><![CDATA[%s]]></MsgType>
      <Content><![CDATA[%s]]></Content>
      <FuncFlag>0</FuncFlag>
      </xml>";
      
      if(!empty( $keyword ))
      {
        $msgType = "text";
        $commonInfo = new commonInfo();
        $ipaddr = $commonInfo->getClientIp();
        $cityinfo = $commonInfo->getIpCity($ipaddr);
        $cityname = $cityinfo;
        $exinfo = $cityinfo;
        $pos = strpos($cityinfo, ' ', 3);
        if ($pos > -1)
        {
          $cityname = substr($cityinfo, 0, $pos); 
          $exinfo = substr($cityinfo, $pos + 1, strlen($cityinfo) - $pos - 1);
        }
        if (strlen($cityname) > 3)
        {
          $cityflag = substr($cityname, 0, strlen($cityname) - 3);
        }
        else
        {
          $cityflag = "地球";
        }
        $welcomeinfo = "灵感机器人欢迎你!";
        $contentStr = "来自".$cityflag.$exinfo."的朋友".$welcomeinfo."\r\n";
        $contentStr .= "你的IP地址是：" . $ipaddr . "\r\n";
        
        if(strtolower($keyword) == 'weather' || $keyword == '天气')
        {
          //Wether Report
          $contentStr .= $commonInfo->getWeatherInfo($cityflag);
        }
        else
        {
          //Robot
          $talk = new talk();
          $reply = $talk->reply($keyword);
          if (empty($reply)) 
          {
            //虫洞查询 
            $tipinfo = "你可以输入‘help’教我学习聊天，或者输入‘天气’看看天气情况。\r\n你说的内容我不太懂，所以上网查了一下 ^_^\r\n\r\n";
            $queryinfo = file_get_contents("http://wap.unidust.cn/api/searchout.do?type=client&ch=1001&info=".$keyword);
            $pos = strpos($queryinfo,$keyword);
            $queryinfo = mb_strcut($queryinfo,$pos,1024,'utf-8');
            $queryinfo = str_replace("uzoo.cn","www.easyapple.net",$queryinfo);
            $queryinfo = str_replace("虫洞","EasyApple",$queryinfo);
            $reply = $tipinfo.$queryinfo;
            
            //自主学习
            //$answer = $talk->getAnswer($keyword, substr($queryinfo,strlen($keyword),512)); 
            
            //小i机器人
            $post_data = array ('requestContent=' . $keyword);
            $post_data = implode ( '&', $post_data );
            $url = 'http://nlp.xiaoi.com/robot/demo/wap/wap-demo.action';            
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_POST, 1 );
            curl_setopt ( $ch, CURLOPT_URL, $url );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
            ob_start ();
            curl_exec ( $ch );
            $result = ob_get_contents ();
            ob_end_clean ();         
            $preg = '/<\/span>(.*)<\/p>/iUs';
            preg_match_all ( $preg, $result, $match );
            $response_msg = $match [0] [0];
            $preg = "/<\/?[^>]+>/i";
            $response_msg = preg_replace ( $preg, '', $response_msg );
            if ("hello,how are you" == $response_msg || "how do you do" == $response_msg) {
                $response_msg = "小i机器人欢迎您，小i机器人不断学习中，欢迎各种调戏.../:,@-D"; // 欢迎语
            }
            $answer = trim ( $response_msg );
            $answer = str_replace("小i","EasyRobot",$answer);
          
            if(empty($answer))
            {
              $contentStr .= $reply;
            }
            else
            {
              $contentStr = $answer;
            }
          }
          else
          {
            $contentStr = $reply;
          }          
        }
        
        if (empty($contentStr)) 
        {
          $contentStr = "亲爱的，跟你聊天我学到了不少东西，此时我不知道说什么好。。。";
        }
        $resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
        echo $resultStr;
      }
      else
      {
        echo "Input something…";
      }
    }
    else
    {
      echo "";
      exit;
    }
  }

  private function checkSignature()
  {
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    $token = TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );
    if( $tmpStr == $signature )
    {
      return true;
    }
    else
    {
      return false;
    }
  }
}


class commonInfo
{
  //Get Client Real IP
  public function getClientIp() 
  {
    //PHP获取当前用户IP地址方法
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
      //check ip from share internet
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
      //to check ip is pass from proxy
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  //Get Ip Addr
  public function getIpCity($ip)
  {
    //从本地数据库获取
    //return ipCity($ip);
    
    //从有道获取
    $url='http://www.youdao.com/smartresult-xml/search.s?type=ip&q=';
    $xml=file_get_contents($url.$ip);
    $data=simplexml_load_string($xml);
    return $data->product->location;
    
    //ip138在线获取
    $ipsurl = "http://www.ip138.com/ips1388.asp?ip=".$ip."&action=2";
    $str=file_get_contents($ipsurl);
    preg_match("/<ul class=\"ul1\">(.*)<\/ul>/",$str,$m); 
    $pstr=str_replace('</li>','',$m[1]);
    $arr=explode('<li>',$pstr);
    $city=array_shift($arr);
    return $city;
  }
  
  // Get Weather Info By Cidy Name
  public function getWeatherInfo($city)
  {
    $post_data = array();
    $post_data['city'] = $city;
    $post_data['submit'] = "submit";
    $url='http://search.weather.com.cn/wap/search.php';
    $o="";
    foreach ($post_data as $k=>$v)
    {
      $o.= "$k=".urlencode($v)."&";
    }
    $post_data=substr($o,0,-1);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $result = curl_exec($ch);
    curl_close($ch);
    $result=explode('/',$result);
    $result=explode('.',$result['5']);
    $citynum = $result['0'];
    $weatherurl = "http://m.weather.com.cn/data/".$citynum.".html";
    $weatherjson = file_get_contents($weatherurl);
    $weatherarray = json_decode($weatherjson,true);
    $weatherinfo = $weatherarray['weatherinfo'];
    $contentTpl = "#这里是%s#(%s)
                  %s%s
                  %s时发布的天气预报：
                  今天天气：%s
                  %s，%s
                  穿衣指数：%s
                  紫外线指数：%s
                  洗车指数：%s
                  明天天气：%s
                  %s，%s
                  后天天气：%s
                  %s，%s";
    $info = sprintf($contentTpl,$weatherinfo['city'],$weatherinfo['city_en'],$weatherinfo['date_y'],$weatherinfo['week'],$weatherinfo['fchh'],$weatherinfo['temp1'],$weatherinfo['weather1'],$weatherinfo['wind1'],$weatherinfo['index_d'],$weatherinfo['index_uv'],$weatherinfo['index_xc'],$weatherinfo['temp2'],$weatherinfo['weather2'],$weatherinfo['wind2'],$weatherinfo['temp3'],$weatherinfo['weather3'],$weatherinfo['wind3']);
    return $info;
  }
}


class talk 
{

  public function getAnswer($question, $answerinfo)
  {
    $score = 0;
    $answer = "";
    $seg = new SaeSegment();
    $questionKeys = $seg->segment($question, 1);
    $answerKeys = $seg->segment($answerinfo, 1);
    
    while(count($questionKeys) > 0)
    {
      $queKey = array_shift($questionKeys);
      $queValue = current($queKey);
      $queType = next($queKey);
    
      $ans = $queValue;
      while(count($answerKeys) > 0)
      {
        $ansKey = array_shift($answerKeys);
        $ansValue = current($ansKey);
        $ansType = next($ansKey);
        if ($ansType == $queType)
        {
          //$score++;
          $ans = $ansValue;
          break;
        }
      }
      
      $answer .= $ans;
    }
    
    if($score > 0)
    {
      learn("?" . $question . ":" . $answer);
    }
    return $answer;
  }

  public function learn($str)
  {
    if (substr($str, 0,1) == '?')
    {
      $kv = new SaeKV ();
      $kv->init();
      $str = strtolower($str);  //转为小写
      $pos = strpos($str, ':');
      if ($pos > -1)
      {
        $q = substr($str, 1,$pos - 1);
        $a = substr($str, $pos + 1);
        $ret = $kv->get('know_' . md5($q));
        if ($ret === false || !is_array($ret))
          $ret = array();
        $ret[] = $a;
        $kv->set('know_' . md5($q), $ret);
      }
    }
  }
  
  public function reply($str)
  {
    $kv = new SaeKV ();
    $kv->init();
    $str = strtolower($str);  //转为小写
    if ($str == 'help' || $str == '求助')
    {
      return "要教我学习，请输入：\r\n?问题:答案 例如：?hi:hello";
    }
    if (substr($str, 0,1) == '?')
    {
      $pos = strpos($str, ':');
      if ($pos > -1)
      {
        $q = substr($str, 1,$pos - 1);
        $a = substr($str, $pos + 1);
        $ret = $kv->get('know_' . md5($q));
        if ($ret === false || !is_array($ret))
          $ret = array();
        $ret[] = $a;
        $kv->set('know_' . md5($q), $ret);
        return "known::" . $q . '/' . $a ;
      }
    }
    
    $ret = $kv->get('know_' . md5($str));
    if ($ret === false　|| !is_array($ret) || count($ret) == 0)
    {
      return '';
    }
    else
    {
      //随机一个
      while(count($ret) > 1)
      {
        $re = array_shift($ret);
        if (rand(0, 1) == 0)
          return $re;
      }
      return array_shift($ret);
    }    
  }
}

?>