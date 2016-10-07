<?php
require 'vuc.php';
$vuc=new VUC();
$vuc->SetConfig('version', 17);
echo $vuc->GetConfig('version');
$enc_dec=new Enc_Dec;
echo $enc_dec->PublicDecrypt($enc_dec->PrivateEncrypt('aaa'));