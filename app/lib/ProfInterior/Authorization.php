<?php

namespace ProfInterior;

final class Authorization extends Api {
  protected function needImplementation() {}

  public function __construct( App &$app ) {
    parent::__construct( $app );
    $this->authorization();
  }

  private function authorization() : void {
    if ( $this->app->requestMethod !== 'POST' )
      $this->printError( 405, 106 );

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    $dt->sub( new \DateInterval( Settings::AUTH_ATTEMPTS_INTERVAL ) );
    $banTime = $dt->getTimestamp();

    $this->app->db->query( "DELETE FROM auth_attempts WHERE last_time < {$banTime}" );

    $q0 = $this->app->db->query( "SELECT * FROM auth_attempts WHERE ip = \"{$this->app->ip_addr}\" AND last_time >= {$banTime}" );

    if ( $attempts = $q0->num_rows ) {
      if ( $attempts >= Settings::AUTH_ATTEMPTS ) {
        $this->printError( 429, 104 );
      }

      $q0->free();
    }

    $this->app->db->query( "INSERT INTO auth_attempts SET ip = \"{$this->app->ip_addr}\", last_time = {$currentTime}" );

    $data = trim( @file_get_contents('php://input') );
    $data = @json_decode( $data );

    if ( !is_object( $data ) || empty( $data->email ) || empty( $data->password ) ) {
      $this->printError( 403, 101 );
    }

    $email = $this->app->db->extendedEscape( $data->email );
    $password = $data->password;

    $q1 = $this->app->db->query( "SELECT * FROM users WHERE email = \"{$email}\"" );
    
    if ( !$q1->num_rows ) {
      $this->printError( 403, 102 );
    }

    $this->app->user = $q1->fetch_assoc();
    $q1->free();

    if ( !password_verify( $password, $this->app->user['pswd_h'] ) ) {
      $this->printError( 403, 102 );
    }

    $userIsBanned = boolval( $this->app->user['banned'] );

    if ( $userIsBanned ) {
      $this->printError( 403, 132 );
    }

    $this->app->user['user_id'] = intval( $this->app->user['user_id'] );

    [ $accessToken, $accessTokenCreatedTimestamp, $accessTokenExpiresTimestamp, $accessTokenExpires ] = $this->getAccessToken();

    $accessTokenHashed = hash_hmac( "sha256", $accessToken, Settings::ACCESS_TOKEN_HASH_SECRET );

    $this->app->db->query( "DELETE FROM sessions WHERE expires < {$currentTime}" );

    $this->app->db->query( "INSERT INTO sessions SET 
      user_id = {$this->app->user['user_id']}, 
      ip = \"{$this->app->ip_addr}\", 
      access_token = \"{$accessTokenHashed}\",
      created = {$accessTokenCreatedTimestamp},
      expires = {$accessTokenExpiresTimestamp}
    " );

    $country = $this->app->db->extendedEscape( $this->app->geo_country );
    $userAgent = $this->app->db->extendedEscape( $this->app->user_agent );

    $this->printResponse([
      'token' => $accessToken,
      'tokenExpirationTime' => $accessTokenExpires,
      'accountId' => intval( $this->app->user['user_id'] ),
      'email' => $this->app->user['email'],
      'role' => $this->app->user['role'],
      'name' => $this->app->user['name'],
    ]);
  }
}