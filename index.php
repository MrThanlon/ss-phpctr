<?php
$config_file = "config.json"; //ss配置文件
$ill_file = "illegal-input.conf"; //所有非法输入，一行一个
$ss_dir = "/etc/init.d/shadowsocks"; //shadowsocks守护进程目录，debian/ubuntu也可以写service ssserver
//$config_str = file_get_contents($config_file);
//$config_json = json_decode($config_str , true); //array
?>
<html>
<header>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ss-password</title>
</header>
<body>
<div align="center">
<p>请不要输入非法内容</p>
<form action="test.php" method="post">
<p>端口号：<input type="number" name="port" value="8381" min="1" max="65535" /></p>
<p>原密码：<input type="password" name="oldpassword" maxlength="20" /></p>
<p>新密码：<input type="password" name="newpassword" maxlength="20" /></p>
<p>再次输入：<input type="password" name="newpassword2" maxlength="20" /></p>
<p>
<?php
function str_check($str) {//检验字符串输入，合法返回true，非法返回false
    $lenth0 = strlen($str);
    if($lenth0 == 0) {
        echo "非法输入。";
        return false;
    }
    foreach($ill_lines as $ill_line) {
        if(strpos($str , $ill_line) != -1) {
            return false;
        }
    }
    /*
    $str = str_replace("and","",$str);
    $str = str_replace("execute","",$str);
    $str = str_replace("update","",$str);
    $str = str_replace("count","",$str);
    $str = str_replace("chr","",$str);
    $str = str_replace("mid","",$str);
    $str = str_replace("master","",$str);
    $str = str_replace("truncate","",$str);
    $str = str_replace("char","",$str);
    $str = str_replace("declare","",$str);
    $str = str_replace("select","",$str);
    $str = str_replace("create","",$str);
    $str = str_replace("delete","",$str);
    $str = str_replace("insert","",$str);
    $str = str_replace("'","",$str);
    $str = str_replace("\"","",$str);
    $str = str_replace(" ","",$str);
    $str = str_replace("or","",$str);
    $str = str_replace("=","",$str);
    $str = str_replace("%20","",$str);
    if($lenth0 != strlen($str)){
        echo "非法输入。";
        return false;
    }*/
    return true;
}
//print($config_json["port_password"]["8381"]);
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $port_int = (int)$_POST["port"];
    $port_str = $_POST["port"];
    $oldpassword = $_POST["oldpassword"];
    $newpassword = $_POST["newpassword"];
    $newpassword2 = $_POST["newpassword2"];
    //检测字符串合法
    if(str_check($port_str) and str_check($oldpassword)
    and str_check($newpassword) and str_check($newpassword)
    and ($port_int >= 1) and ($port_int <= 65535)
    and (strlen($oldpassword) <= 20) and (strlen($newpassword) <= 20) and 
    (strlen($newpassword2) <= 20) ) {
        if($newpassword != $newpassword2) {
            echo "新密码输入不一致。";
        }
        $config_str = file_get_contents($config_file);
        $config_json = json_decode($config_str , true); //array
        if($config_json["port_password"][$port_str] == "") {
            echo "端口错误。";
        }
        elseif ($config_json["port_password"][$port_str] != $oldpassword) {
            echo "密码错误";
        }
        else { //验证成功，替换文本，重启ss
            $config_json["port_password"][$port_str] = $newpassword;
            $config_str = json_encode($config_json , 
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); //处理中文字符并格式化
            if($config_str) {
                file_put_contents($config_file , $config_str);
                //passthru($ss_dir." restart");
                echo "密码修改成功，请稍等片刻等待启用。";
            }
            else {
                echo "系统错误，请联系管理员。";
            }
        }
    }
    else{
        echo "非法输入。";
    }
}
?>
</p>
<input type="submit" />
</form>
</div>
</body>
</html>