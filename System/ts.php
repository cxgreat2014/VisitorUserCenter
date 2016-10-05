<?php
require 'vuc.php';
$vuc=new VUC();
$enc=$vuc->PrivateEncrypt('大');
$dec=$vuc->PublicDecrypt($enc);
echo $enc.'<br/>'.$dec;