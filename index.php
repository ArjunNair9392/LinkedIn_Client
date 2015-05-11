<?php
namespace LinkedIn;
require 'LinkedIn.php';


$li = new LinkedIn(
  array(
    'api_key' => '7752iv95bfdtho', 
    
    'api_secret' => 'gfdyHZcOQKT6oapY', 
    'callback_url' => 'http://localhost/linkdenlogin/userDetails.php'
  )
);



$url = $li->getLoginUrl(
  array(
    LinkedIn::SCOPE_FULL_PROFILE, 
    LinkedIn::SCOPE_EMAIL_ADDRESS, 
    LinkedIn::SCOPE_NETWORK
  )
);

echo "<a href =' ".$url."'> Login with LinkedIn </a>";


?>

