<?php 
namespace App\Service\GoogleOAuth2;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;

class GetUserInfos {
  private $token = "";
  public function __construct($token)
  {
    $this->token = $token;
  }
  public function getUserInfos(){
    $jwt = $this->token->id_token;
    $explodedJwt = explode(".",$jwt);
    if(!isset($explodedJwt[1])) return false; 
    $userInfos = json_decode(base64_decode($explodedJwt[1]),false);
    return $userInfos;
  }
}