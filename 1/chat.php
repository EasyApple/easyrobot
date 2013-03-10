<?php
header("Content-Type: text/html;charset=utf-8");
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
        $welcomeinfo = "灵感机器人欢迎你!";
        
        
        //Talk
        $talk = new talk();
        //获取分词信息
        $seg = new SaeSegment();
        $segments = $seg->segment($keyword, 1);
        $segCount = count($segments);
        $firstSeg = array_shift($segments);
        $segValue = current($firstSeg);
        $segType = next($firstSeg);
        
        if($segCount == 1 && $segType == POSTAG_ID_N )
        {
          //Queryinfo
          $contentStr = $commonInfo->getQueryinfo($keyword);
        }
        else if($segCount == 1 && $segType == POSTAG_ID_NS_Z )
        {
          //Weather
          $contentStr = $commonInfo->getCityWeather($keyword);
        }
        else
        {
          $reply = $talk->replyEx($keyword);
          if (!empty($reply)) 
          {
            $talk->learn($keyword,$reply);
          }
          else
          {
            $reply = $talk->reply($keyword);
            if (empty($reply)) 
            {
              //Queryinfo
              $answerinfo = $commonInfo->getQueryinfo($keyword);
              //Smart Answer
              $reply = $talk->getAnswer($keyword,$answerinfo);              
            }
          }
          $contentStr = $reply;    
        }
        
        if (empty($contentStr)) 
        {
          $contentStr = $welcomeinfo;
        }
        $resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
        echo $resultStr;
      }
      else
      {
        echo "亲爱的，说点啥吧。。。";
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
  public function getCityWeather($city)
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
    $weather = sprintf($contentTpl,$weatherinfo['city'],$weatherinfo['city_en'],$weatherinfo['date_y'],$weatherinfo['week'],$weatherinfo['fchh'],$weatherinfo['temp1'],$weatherinfo['weather1'],$weatherinfo['wind1'],$weatherinfo['index_d'],$weatherinfo['index_uv'],$weatherinfo['index_xc'],$weatherinfo['temp2'],$weatherinfo['weather2'],$weatherinfo['wind2'],$weatherinfo['temp3'],$weatherinfo['weather3'],$weatherinfo['wind3']);
    return $weather;
  }

  public function getQueryinfo($keyword)
  {
    //虫洞查询 
    $queryinfo = file_get_contents("http://wap.unidust.cn/api/searchout.do?type=client&ch=1001&info=".$keyword);
    $pos = strpos($queryinfo,$keyword);
    $queryinfo = mb_strcut($queryinfo,$pos,1024,'utf-8');
    $queryinfo = str_replace("uzoo.cn","www.easyapple.net",$queryinfo);
    $queryinfo = str_replace("虫洞","EasyApple",$queryinfo);
    return $queryinfo;
  }
}

class talk 
{
  public function learn($q,$a)
  {
    //整句学习
    $kv = new SaeKV ();
    $kv->init();
    $ret = $kv->get('know_' . md5($q));
    if ($ret === false || !is_array($ret))
      $ret = array();
    $ret[] = $a;
    $kv->set('know_' . md5($q), $ret);
    
    //分词学习
    $seg = new SaeSegment();
    $questionKeys = $seg->segment($q, 1);
    $answerKeys = $seg->segment($a, 1);    
    while(count($questionKeys) > 0)
    {
      $queKey = array_shift($questionKeys);
      $queValue = current($queKey);
      $queType = next($queKey);
    
      while(count($answerKeys) > 0)
      {
        $ansKey = array_shift($answerKeys);
        $ansValue = current($ansKey);
        $ansType = next($ansKey);
        if ($ansType == $queType)
        {
          $ret = $kv->get('know_seg_' . md5($queValue));
          if ($ret === false || !is_array($ret))
            $ret = array();
          $ret[] = $ansValue;
          $kv->set('know_seg_' . md5($queValue), $ret);          
          break;
        }
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
      return "要教我学习，请输入：\r\n?问题/答案 例如：?hi/hello";
    }
    if (substr($str, 0,1) == '?')
    {
      $pos = strpos($str, '/');
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
        
    //整句回应
    $answer = '';
    $ret = $kv->get('know_' . md5($str));
    if ($ret === false　|| !is_array($ret) || count($ret) == 0)
    {
      $answer = '';
    }
    else
    {
      //随机一个
      while(count($ret) > 1)
      {
        $re = array_shift($ret);
        if (rand(0, 1) == 0)
        {
          $answer = $re;
          break;
        }
      }
      if(empty($answer))
      {
      	$answer = array_shift($ret);
      }
    }
    
    //分词回应
    if(empty($answer))
    {
      $seg = new SaeSegment();
      $questionKeys = $seg->segment($str, 1);  
      while(count($questionKeys) > 0)
      {
        $queKey = array_shift($questionKeys);
        $queValue = current($queKey);
        $queType = next($queKey);
      
        $ret = $kv->get('know_seg_' . md5($queValue));
        if ($ret === false　|| !is_array($ret) || count($ret) == 0)
        {
          $answer .= $queValue;
        }
        else
        {
          //随机一个
          $ansSeg = '';
          while(count($ret) > 1)
          {
            $re = array_shift($ret);
            if (rand(0, 1) == 0)
            {
              $ansSeg = $re;
              break;
            }
          }
          if(empty($ansSeg))
          {
            $ansSeg = array_shift($ret);
          }
          
          $answer .= $ansSeg;
        }
      }
    }
    
    return $answer;
  }

  public function replyEx($str)
  {
    //小i机器人
    $post_data = array ('requestContent=' . $str);
    $post_data = implode ( '&', $post_data );
    //$url = 'http://nlp.xiaoi.com/robot/demo/wap/wap-demo.action';
    //$url = 'http://nlp.xiaoi.com/robot/demo/sms/'; 
    $url = 'http://nlp.xiaoi.com/robot/demo/wap/';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER,1);
    $user_agent = "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)";
    curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_REFERER, "nlp.xiaoi.com");
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    $r = curl_exec($ch);
    curl_close($ch);
    preg_match('/XISESSIONID=(.*);/', $r, $match);  //cookie为XISESSIONID
    $sid = $match[1];

    $key=urldecode($str);
    $post_data = array (
        'requestContent=' . $key
    );
    $post_data = implode ( '?', $post_data );
    $url = 'http://nlp.xiaoi.com/robot/demo/wap/wap-demo.action';
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    $user_agent = "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)";
    curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $cookie = 'XISESSIONID='.$sid;
    curl_setopt($ch, CURLOPT_COOKIE , $cookie);
    $result=curl_exec ( $ch );
    $preg = '/<\/span>(.*)<\/p>/iUs';
    preg_match_all ( $preg, $result, $match );
    $response_msg = $match [0] [0];
    $preg = "/<\/?[^>]+>/i";
    $response_msg = preg_replace ( $preg, '', $response_msg );
    
    /*
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
    */
    
    $answer = trim ( $response_msg );
    $answer = str_replace("小i","灵感机器人",$answer);
    $answer = str_replace("Xiao i","EasyRobot",$answer);
    $answer = str_replace("www.xiaoi.com","www.easyapple.net",$answer);
    $answer = str_replace("袁辉","灵感水手",$answer);
    $answer = str_replace("max","Jack",$answer);
    return $answer;
  }
  
  public function getAnswer($question, $answerinfo)
  {
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
          $ans = $ansValue;
          break;
        }
      }
      
      $answer .= $ans;
    }
    
    return $answer;
  }
}

?>