<?php 
namespace App\Service\GoogleOAuth2;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;

class GetToken {
  private $code;
  public function __construct($infos)
  {
    $this->code = $infos['code'];
  }
  public function getToken(){
    $client = HttpClient::create();
    try{
      $response = $client->request('POST', 'https://oauth2.googleapis.com/token', [
        'headers' => [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ],
        'body' => http_build_query([
          'client_id'=>$_ENV['GOOGLE_CLIENT_ID'],
          'client_secret'=>$_ENV['GOOGLE_CLIENT_SECRET'],
          'code'=>$this->code,
          'grant_type'=>'authorization_code',
          'redirect_uri'=> 'http://localhost:8000/api/users/external/login',
        ])
      ]);
      return $response->getContent();
    }catch (ClientException $err) {
      // // Affichez les détails de l'exception pour le débogage
      // dump($err->getMessage());
      // // Vous pouvez également vérifier le code de statut HTTP de la réponse
      // dump($err->getResponse()->getStatusCode());
      return false;
    }
  }
}