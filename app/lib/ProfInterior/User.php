<?php

namespace ProfInterior;

final class User extends Api {
  protected function needImplementation() {}

  public function __construct( App &$app ) {
    parent::__construct( $app );
    $this->user();
  }

  private function user() : void {
    $this->checkAccessLevel( anonymousIsAllowed: true );

    switch( $this->app->requestMethod ) {
      case 'GET':
        $this->userGET();
        break;
      case 'POST':
        $this->userPOST();
        break;
      case 'DELETE':
        $this->userDELETE();
        break;
      default:
        $this->printError( 405, 106 );
        break;
    }
  }

  private function userGET() : void {
    $myRole = $this->app->user['role'];
    $myUserId = $this->app->user['user_id'];

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    $userId = $myRole === 'admin' ? intval( $this->app->get['userId'] ?? 0 ) : $myUserId;

    if ( !$userId )
      $q1 = $this->app->db->query( "SELECT * FROM users ORDER BY user_id ASC" );
    else
      $q1 = $this->app->db->query( "SELECT * FROM users WHERE user_id = {$userId}" );

    $users = [];

    while ( $row = $q1->fetch_assoc() ) {
      $dt->setTimestamp( intval( $row['created'] ) );
      $createdAt = $this->formatDateTimeRepresentation( $dt );

      $dt->setTimestamp( intval( $row['updated'] ) );
      $updatedAt = $this->formatDateTimeRepresentation( $dt );

      $dt->setTimestamp( intval( $row['last_activity'] ) );
      $lastActivity = $this->formatDateTimeRepresentation( $dt );
  
      $users[] = [
        'userId' => intval( $row['user_id'] ),
        'email' => $row['email'],
        'role' => $row['role'],
        'name' => $row['name'],
        'createdAt' => $createdAt,
        'updatedAt' => $updatedAt,
        'lastActivity' => $lastActivity,
        'banned' => boolval( $row['banned'] ),
      ];
    }
  
    $q1->free();
  
    $this->printResponse( [ 'count' => count( $users ), 'users' => $users ] );
  }

  private function userPOST() : void {
    $myRole = $this->app->user['role'];
    $myUserId = $this->app->user['user_id'];

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    $data = trim( @file_get_contents('php://input') );
    $data = @json_decode( $data );

    if ( !is_object( $data ) ) {
      $this->printError( 403, 109 );
    }

    $newRole = 'user';

    $q1 = $this->app->db->query( "SELECT * FROM users WHERE role = 'admin'" );

    if ( !$q1->num_rows ) {
      $newRole = 'admin';
    }

    $q1->free();

    $password = strval( $data->password ?? "" );

    $usersTableDataset = [];

    if ( !empty( $data->email ) ) 
      $usersTableDataset['email'] = $this->app->db->extendedEscape( $data->email );

    if ( !$password || mb_strlen( $password ) < 8 ) {
      $this->printError( 403, 190 );
    }

    $usersTableDataset['pswd_h'] = $this->app->db->extendedEscape( password_hash( $password, Settings::PASSWORD_HASH_ALGO ) );

    if ( empty( $usersTableDataset['email'] ) || !filter_var( $usersTableDataset['email'], FILTER_VALIDATE_EMAIL ) ) {
      $this->printError( 403, 191 );
    }

    $q1 = $this->app->db->query( "SELECT user_id FROM users WHERE email = \"{$usersTableDataset['email']}\"" );
      
    if ( $q1->num_rows ) {
      $q1->free();
      $this->printError( 403, 192 );
    }

    $usersTableDataset['role'] = $newRole;

    if ( !empty( $data->name ) ) 
      $usersTableDataset['name'] = $this->app->db->extendedEscape( $data->name );

    $usersTableDataset['created'] = $currentTime;

    $valuesLine = $this->app->db->getSQLLine( $usersTableDataset );

    $this->app->db->query( "INSERT INTO users SET {$valuesLine}" );
    $userId = intval( $this->app->db->insert_id );
  
    $q1 = $this->app->db->query( "SELECT * FROM users WHERE user_id = {$userId}" );
      
    if ( !$q1->num_rows ) {
      $this->printError( 500, 193 );
    }

    $user = $q1->fetch_assoc();

    $q1->free();

    $dt->setTimestamp( intval( $user['created'] ) );
    $createdAt = $this->formatDateTimeRepresentation( $dt );

    $dt->setTimestamp( intval( $user['updated'] ) );
    $updatedAt = $this->formatDateTimeRepresentation( $dt );

    $dt->setTimestamp( intval( $user['last_activity'] ) );
    $lastActivity = $this->formatDateTimeRepresentation( $dt );

    $this->printResponse([
      'userId' => intval( $user['user_id'] ),
      'email' => $user['email'],
      'role' => $user['role'],
      'name' => $user['name'],
      'createdAt' => $createdAt,
      'updatedAt' => $updatedAt,
      'lastActivity' => $lastActivity,
      'banned' => boolval( $user['banned'] ),
    ]);
  }

  private function userDELETE() : void {
    $myRole = $this->app->user['role'];
    $myUserId = $this->app->user['user_id'];

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    if ( $myRole !== 'admin' ) {
      $this->printError( 403, 103 );
    }

    $id = intval( $this->app->get['userId'] ?? 0 );

    if ( !$id ) {
      $this->printError( 403, 195 );
    }

    $q1 = $this->app->db->query( "SELECT user_id FROM users WHERE user_id = {$id}" );

    if ( !$q1->num_rows ) {
      $this->printError( 403, 194 );
    }

    $q1->free();

    $this->app->db->query( "DELETE FROM sessions WHERE user_id = {$id}" );
    $this->app->db->query( "DELETE FROM users WHERE user_id = {$id}" );

    $this->printResponse();
  }
}