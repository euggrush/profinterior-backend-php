<?php

namespace ProfInterior;

abstract class Api {
  protected App $app;
  protected array $httpStatuses;

  protected abstract function needImplementation();

  protected function __construct( App &$app ) {
    $this->app = &$app;
    $this->httpStatuses = require 'http-status-codes.php';
  }

  protected function checkAccessLevel( bool $anonymousIsAllowed = false ) : void {
    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    $this->app->ip_addr = $this->app->db->extendedEscape( $this->app->ip_addr );

    $this->app->db->query( "DELETE FROM sessions WHERE expires < {$currentTime}" );

    $accessToken = $_SERVER['HTTP_AUTHORIZATION'] ?? "";
    $accessToken = trim( str_replace( 'Bearer ', '', $accessToken ) );

    if ( !mb_strlen( $accessToken ) && !$anonymousIsAllowed ) {
      $this->printError( 401, 107 );
    }

    $accessToken = $this->app->db->extendedEscape( $accessToken );
    $accessTokenHashed = hash_hmac( "sha256", $accessToken, Settings::ACCESS_TOKEN_HASH_SECRET );

    $q0 = $this->app->db->query( "SELECT * FROM sessions 
      WHERE access_token = \"{$accessTokenHashed}\" AND expires >= {$currentTime}" );

    $numRows = $q0->num_rows;

    if ( !$numRows && !$anonymousIsAllowed ) {
      $this->printError( 401, 108 );
    }
    else if ( !$numRows && $anonymousIsAllowed ) {
      $this->app->user = [];
      $this->app->user['user_id'] = 0;
      $this->app->user['role'] = 'anonymous';
    }
    else if ( $numRows ) {
      $session = $q0->fetch_assoc();
      $q0->free();

      $userId = intval( $session['user_id'] );

      $q1 = $this->app->db->query( "SELECT * FROM users WHERE user_id = {$userId}" );
      
      if ( !$q1->num_rows ) {
        $this->printError( 403, 131 );
      }

      $this->app->user = $q1->fetch_assoc();
      $q1->free();

      $userIsBanned = boolval( $this->app->user['banned'] );

      if ( $userIsBanned ) {
        $this->printError( 403, 133 );
      }

      $this->app->user['user_id'] = intval( $this->app->user['user_id'] );

      $this->app->db->query( "UPDATE users SET last_activity = {$currentTime} WHERE user_id = {$userId}" );
    }
  }

  protected function getAccessToken() : array {
    $accessToken = hash( 'sha256', random_bytes(16) );

    $dt = new \DateTime();
    $accessTokenCreatedTimestamp = $dt->getTimestamp();

    $dt->add( new \DateInterval( Settings::TOKEN_EXPIRATION_INTERVAL ) );
    $accessTokenExpiresTimestamp = $dt->getTimestamp();
    $accessTokenExpires = $this->formatDateTimeRepresentation( $dt );

    return [ $accessToken, $accessTokenCreatedTimestamp, $accessTokenExpiresTimestamp, $accessTokenExpires ];
  }

  protected function formatDateTimeRepresentation( \DateTimeInterface $dt ) : string {
    //return $dt->format( 'Y-m-d\TH:i:s.' ) . substr( $dt->format('u'), 0, 3 ) . 'Z';
    //return $dt->format( 'Y-m-d\TH:i:s.vp' );
    return $dt->format( 'Y-m-d\TH:i:s.v' ) . 'Z';
  }

  protected function printError( int $httpCode = 404, int $code = 0, mixed ...$args ) : void {
    $this->printHeaders( $httpCode );

    if ( $httpCode === 401 ) {
      header( 'WWW-Authenticate: Bearer realm="DefaultRealm"' );
    }

    $text = $this->app->lang[ 'api_code_' . $code ] ?? $this->app->lang[ 'api_code_1000' ];
    $text = sprintf( $text, ...$args );

    echo json_encode( [
      "state" => "fail",
      "code" => $code,
      "message" => $text
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    exit();
  }

  protected function printResponse( array $data = [] ) : void {
    $this->printHeaders( 200 );

    $myRole = $this->app->user['role'] ?? "";

    if ( $myRole === 'admin' ) {
      $executionTime = round( microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3 );
      $data = [ "executionTime" => $executionTime ] + $data;
    }

    $data = [ "state" => "ok" ] + $data;

    echo json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    exit();
  }

  protected function printHeaders( int $httpCode = 200 ) : void {
    // preflight request
    if ( $this->app->requestMethod === 'OPTIONS' ) {
      header( "{$this->app->http_protocol} 204 No Content" );
      header( "Access-Control-Allow-Origin: {$this->app->http_origin}" );
      header( "Vary: Origin" );
      header( "Access-Control-Allow-Credentials: true" );
      header( "Access-Control-Allow-Headers: Content-Type, Authorization, Cookie" );
      header( "Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS" );
      header( "Access-Control-Max-Age: 86400" );
      exit();
    }
    // GET, POST, PUT, DELETE
    else if ( in_array( $this->app->requestMethod, [ 'GET', 'POST', 'PUT', 'DELETE' ] ) ) {
      header( "{$this->app->http_protocol} {$httpCode} {$this->httpStatuses[$httpCode]}" );
      header( "Access-Control-Allow-Origin: {$this->app->http_origin}" );
      header( "Vary: Origin" );
      header( "Access-Control-Allow-Credentials: true" );
      header( "Access-Control-Expose-Headers: X-Extra-Data" );
      header( "Access-Control-Max-Age: 86400" );
      header( "Content-Type: application/json; charset=UTF-8" );
    }
    else {
      header( "{$this->app->http_protocol} 501 Not Implemented" );
      die("Not implemented.");
    }
  }
}