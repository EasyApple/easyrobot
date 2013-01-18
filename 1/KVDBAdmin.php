<?php

 

@session_start();

 

$password = "123456"; //管理密码

 

function js($code)

{

   $str = str_replace("reload", "window.location.reload()", $code);

   $str = str_replace("go", "window.location.href", $code);

   echo "<script type=\"text/javascript\">{$str}</script>";

}

 

$kv = new SaeKV(); //实例化

 

$ret = $kv->init(); //初始化

 

if (!$ret)

{

   die(js("reload"));

}

 

?>

<html>

<head>

<meta charset="utf-8" />

<title>KVDB - Administrator's Control Panel</title>

<style type="text/css">

body{font-size: 12px;color: #00bbcc;}

input{color: #00bbcc;background: white;border:1px dashed #00bb22;}

a:link{color: #006688}

a:visited{color: #006688}

a:hover{color: #006688}

</style>

<script src="http://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

<script src="http://lib.sinaapp.com/js/jquery/1.7.2/jquery.js" type="text/javascript"></script>

</head>

<body>

<?php

 

if ($_SESSION['status'] == "login")

{

   echo "choose: <a href=\"t.php?action=manage\">INDEX</a> | <a href=\"t.php?action=set\">SET</a> | <a href=\"t.php?action=list\">LIST</a><!-- | <a href=\"t.php?action=del\">DEL</a>--> | <a href=\"t.php?action=logout\">LOGOUT</a><br />";

}

 

switch ($_GET['action'])

{

   case "login":

       if (!empty($_POST['pwd']))

       {

           if ($_POST['pwd'] == $password)

               $_SESSION['status'] = "login";

           else

               js("alert('Password error!')");

       }

 

       if ($_SESSION['status'] == "login")

           js("go('?action=manage')");

 

?>

<form action="#" method="post">

Password: <input type="password" name="pwd" /> <input type="submit" value="Login Control Panel" />

</form>

<?

 

       break;

   case "manage":

       if ($_SESSION['status'] == "login")

       {

           $ret = $kv->get_info();

           foreach ($ret as $k => $v)

           {

               echo "$k=>$v<br />";

           }

       } else

       {

           js("go('?action=login');");

       }

       break;

   case "list":

       if ($_SESSION['status'] != "login")

       {

           js("go('?action=login')");

       }

       $list = $kv->pkrget("", 100);

       while ($list)

       {

           foreach ($list as $k => $v)

           {

               if (strrchr($k, '.') == "jpg" || "jpeg" || "png" || "ico" || "gif" || "bmp")

               {

                   echo "Key: {$k} >> <a href=\"?action=del&file={$k}\" onclick=\"confirm('This action could delete file, Are sure?');\">DEL</a> | <a href=\"kv.php?file={$k}\" target=\"_blank\">Preview</a><br />";

               } elseif ($k == "")

               {

                   echo "not have file.";

               } else

               {

                   echo "Key: {$k} >> <a href=\"?action=del&file={$k}\" onclick=\"confirm('This action could delete file, Are sure?');\">DEL</a><br />";

               }

           }

           break;

       }

       break;

   case "del":

       if ($_SESSION['status'] != "login")

       {

           js("go('?action=login')");

       }

       $file = $_GET['file'];

       if (!$kv->delete($file))

       {

           js("alert('Delete failed,please try again!');go('?action=manage')");

       } else

       {

           js("go('?action=list')");

       }

       break;

   case "set":

       if ($_SESSION['status'] != "login")

       {

           js("go('?action=login')");

       }

       echo "Old(Now):<br />";

       $ret = $kv->get_options();

       foreach ($ret as $k => $v)

       {

           echo "{$k}=>{$v}<br />";

       }

 

?>

<br /><br /><br />

<form action="#" method="post">

New: <br />

<input type="radio" value="0" name="compression" checked="checked" />compression=&gt;0&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" value="1" name="compression" />compression=&gt;1

<br />

<input type="radio" value="0" name="mspolicy" checked="checked" />mspolicy=&gt;0&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" value="1" name="mspolicy" />mspolicy=&gt;1

<br />

<input type="radio" value="0" name="checksum" checked="checked" />checksum=&gt;0&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" value="1" name="checksum" />checksum=&gt;1

<br />

<input type="radio" value="0" name="urlencode" checked="checked" />URLENCODE=&gt;0&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" value="1" name="urlencode" />URLENCODE=&gt;1

<br />

<input type="submit" value="SET to OPTION" />

</form>

<?

 

       if (!empty($_POST['checksum']) && !empty($_POST['compression']) && !empty($_POST['urlencode']) &&

           !empty($_POST['mspolicy']))

       {

           $option = array();

           $option['encodekey'] = $_POST['urlencode'];

           $option['compression'] = $_POST['compression'];

           $option['mspolicy'] = $_POST['mspolicy'];

           $option['checksum'] = $_POST['checksum'];

           if (!$kv->set_options($option))

           {

               js("alert('Set failed!');go('?action=set')");

           } else

           {

               js("alert('Set success!');go('?action=set')");

           }

       }

       break;

   case "logout":

       unset($_SESSION['status']);

       js("go('./')");

       break;

   default:

       if ($_SESSION['status'] == "login")

       {

           js("go('?action=manage')");

       } else

       {

           js("go('?action=login')");

       }

       break;

}

 

?>

</body>

</html>