<?php

namespace ProfInterior;

final class All_Pictures extends Api {
  protected function needImplementation() {}

  public function __construct( App &$app ) {
    parent::__construct( $app );
    $this->allPictures();
  }

  private function allPictures() : void {
    $this->checkAccessLevel( anonymousIsAllowed: true );

    switch( $this->app->requestMethod ) {
      case 'GET':
        $this->allPicturesGET();
        break;
      case 'DELETE':
        $this->allPicturesDELETE();
        break;
      default:
        $this->printError( 405, 106 );
        break;
    }
  }

  private function allPicturesGET() : void {
    $myRole = $this->app->user['role'];
    $myUserId = $this->app->user['user_id'];

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    $projectId = intval( $this->app->get['projectId'] ?? 0 );
    $pictureId = intval( $this->app->get['pictureId'] ?? 0 );

    if ( !$projectId ) {
      $q1 = $this->app->db->query( "SELECT * FROM pictures ORDER BY id ASC" );
    }
    else {
      if ( !$pictureId ) {
        $q1 = $this->app->db->query( "SELECT * FROM pictures 
          WHERE project_id = {$projectId} ORDER BY id ASC" );
      }
      else {
        $q1 = $this->app->db->query( "SELECT * FROM pictures 
          WHERE project_id = {$projectId} AND id = {$pictureId} ORDER BY id ASC" );
      }
    }

    $pictures = [];

    while ( $row = $q1->fetch_assoc() ) {
      $dt->setTimestamp( intval( $row['created_at'] ) );
      $createdAt = $this->formatDateTimeRepresentation( $dt );

      $pictures[] = [
        'id' => intval( $row['id'] ),
        'projectId' => intval( $row['project_id'] ),
        'path' => $row['path'],
        'createdAt' => $createdAt,
      ];
    }

    $q1->free();

    $this->printResponse( [ 'count' => count( $pictures ), 'pictures' => $pictures ] );
  }

  private function allPicturesDELETE() : void {
    $myRole = $this->app->user['role'];
    $myUserId = $this->app->user['user_id'];

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    if ( $myRole !== 'admin' ) {
      $this->printError( 403, 103 );
    }

    $id = intval( $this->app->get['pictureId'] ?? 0 );

    if ( !$id ) {
      $this->printError( 403, 151 );
    }

    $q1 = $this->app->db->query( "SELECT * FROM pictures WHERE id = {$id}" );

    if ( !$q1->num_rows ) {
      $this->printError( 403, 152 );
    }

    $picture = $q1->fetch_assoc();
    $q1->free();

    $this->app->db->query( "DELETE FROM pictures WHERE id = {$id}" );

    @unlink( $this->app->uploadsDir . "/project_{$picture['project_id']}/{$picture['path']}" );

    $this->printResponse();
  }
}