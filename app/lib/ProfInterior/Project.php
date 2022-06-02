<?php

namespace ProfInterior;

final class Project extends Api {
  protected function needImplementation() {}

  public function __construct( App &$app ) {
    parent::__construct( $app );
    $this->project();
  }

  private function project() : void {
    $this->checkAccessLevel( anonymousIsAllowed: true );

    switch( $this->app->requestMethod ) {
      case 'GET':
        $this->projectGET();
        break;
      case 'POST':
        $this->projectPOST();
        break;
      case 'PUT':
        $this->projectPUT();
        break;
      case 'DELETE':
        $this->projectDELETE();
        break;
      default:
        $this->printError( 405, 106 );
        break;
    }
  }

  private function projectGET() : void {
    $myRole = $this->app->user['role'];
    $myUserId = $this->app->user['user_id'];

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    $projectId = intval( $this->app->get['projectId'] ?? 0 );
    $categoryId = intval( $this->app->get['categoryId'] ?? 0 );

    if ( !$projectId && !$categoryId ) {
      $q1 = $this->app->db->query( "SELECT
          p.*, c.*, c.id AS category_id, p.id AS project_id 
          FROM projects p
          INNER JOIN categories c
          ON p.category_id = c.id
          ORDER BY p.id ASC
        " );
    }
    else {
      if ( $projectId ) {
        $q1 = $this->app->db->query( "SELECT
          p.*, c.*, c.id AS category_id, p.id AS project_id 
          FROM projects p
          INNER JOIN categories c
          ON p.category_id = c.id
          WHERE p.id = {$projectId} 
          ORDER BY p.id ASC
        " );
      }
      else if ( $categoryId ) {
        $q1 = $this->app->db->query( "SELECT
          p.*, c.*, c.id AS category_id, p.id AS project_id 
          FROM projects p
          INNER JOIN categories c
          ON p.category_id = c.id
          WHERE c.id = {$categoryId} 
          ORDER BY p.id ASC
        " );
      }
    }

    $projects = [];

    while ( $project = $q1->fetch_assoc() ) {
      $projectId = intval( $project['project_id'] );

      $pictures = [];

      $q2 = $this->app->db->query( "SELECT * FROM pictures WHERE project_id = {$projectId}" );

      while( $picture = $q2->fetch_assoc() ) {
        $image = $this->app->uploadsDir . "/project_{$projectId}/{$picture['path']}";
        $image = str_replace( $_SERVER['DOCUMENT_ROOT'] . '/', '', $image );

        $pictures[] = [
          'pictureId' => intval( $picture['id'] ),
          'fullPath' => $image
        ];
      }

      $q2->free();

      $dt->setTimestamp( intval( $project['created_at'] ) );
      $createdAt = $this->formatDateTimeRepresentation( $dt );

      $projects[] = [
        'projectId' => $projectId,
        'categoryId' => intval( $project['category_id'] ),
        'categoryName' => $project['name'],
        'title' => $project['title'],
        'description' => $project['description'],
        'pictures' => $pictures,
        'createdAt' => $createdAt,
      ];
    }

    $q1->free();

    $this->printResponse( [ 'count' => count( $projects ), 'projects' => $projects ] );
  }

  private function projectPOST() : void {
    $myRole = $this->app->user['role'];
    $myUserId = $this->app->user['user_id'];

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    if ( $myRole !== 'admin' ) {
      $this->printError( 403, 103 );
    }

    $data = trim( @file_get_contents('php://input') );
    $data = @json_decode( $data );

    if ( !is_object( $data ) ) {
      $this->printError( 403, 109 );
    }

    $title = $this->app->db->extendedEscape( $data->title ?? "" );
    $description = $this->app->db->extendedEscape( $data->description ?? "" );
    $categoryId = intval( $data->categoryId ?? 0 );

    if ( empty( $title ) ) {
      $this->printError( 403, 160 );
    }

    if ( !$categoryId ) {
      $this->printError( 403, 161 );
    }

    $q1 = $this->app->db->query( "SELECT * FROM categories WHERE id = {$categoryId}" );

    if ( !$q1->num_rows ) {
      $this->printError( 403, 162 );
    }

    $q1->free();

    $projectsTableDataset = [];

    $projectsTableDataset['title'] = $title;
    $projectsTableDataset['description'] = $description;
    $projectsTableDataset['category_id'] = $categoryId;
    $projectsTableDataset['created_at'] = $currentTime;

    $valuesLine = $this->app->db->getSQLLine( $projectsTableDataset );

    $this->app->db->query( "INSERT INTO projects SET {$valuesLine}" );
    $projectId = intval( $this->app->db->insert_id );

    $dt->setTimestamp( intval( $projectsTableDataset['created_at'] ) );
    $createdAt = $this->formatDateTimeRepresentation( $dt );

    $this->printResponse([
      'projectId' => $projectId,
      'title' => $projectsTableDataset['title'],
      'description' => $projectsTableDataset['description'],
      'categoryId' => $projectsTableDataset['category_id'],
      'createdAt' => $createdAt,
    ]);
  }

  private function projectPUT() : void {
    $myRole = $this->app->user['role'];
    $myUserId = $this->app->user['user_id'];

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    if ( $myRole !== 'admin' ) {
      $this->printError( 403, 103 );
    }

    $data = trim( @file_get_contents('php://input') );
    $data = @json_decode( $data );

    if ( !is_object( $data ) ) {
      $this->printError( 403, 109 );
    }

    $projectId = intval( $data->projectId ?? 0 );

    $q1 = $this->app->db->query( "SELECT * FROM projects WHERE id = {$projectId}" );

    if ( !$q1->num_rows ) {
      $this->printError( 403, 163 );
    }

    $q1->free();

    $title = $data->title ?? null;
    $description = $data->description ?? null;
    $categoryId = intval( $data->categoryId ?? 0 );

    $projectsTableDataset = [];

    if ( !is_null( $title ) ) $projectsTableDataset['title'] = $this->app->db->extendedEscape( $title );
    if ( !is_null( $description ) ) $projectsTableDataset['description'] = $this->app->db->extendedEscape( $description );

    if ( $categoryId > 0 ) {
      $q1 = $this->app->db->query( "SELECT * FROM categories WHERE id = {$categoryId}" );

      if ( !$q1->num_rows ) {
        $this->printError( 403, 162 );
      }

      $q1->free();

      $projectsTableDataset['category_id'] = $categoryId;
    }

    $valuesLine = $this->app->db->getSQLLine( $projectsTableDataset );

    $this->app->db->query( "UPDATE projects SET {$valuesLine} WHERE id = {$projectId}" );

    $q1 = $this->app->db->query( "SELECT * FROM projects WHERE id = {$projectId}" );

    $project = $q1->fetch_assoc();
    $q1->free();

    $dt->setTimestamp( intval( $project['created_at'] ) );
    $createdAt = $this->formatDateTimeRepresentation( $dt );

    $this->printResponse([
      'projectId' => $projectId,
      'title' => $project['title'],
      'description' => $project['description'],
      'categoryId' => intval( $project['category_id'] ),
      'createdAt' => $createdAt,
    ]);
  }

  private function projectDELETE() : void {
    $myRole = $this->app->user['role'];
    $myUserId = $this->app->user['user_id'];

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    if ( $myRole !== 'admin' ) {
      $this->printError( 403, 103 );
    }

    $id = intval( $this->app->get['projectId'] ?? 0 );

    if ( !$id ) {
      $this->printError( 403, 164 );
    }

    $q1 = $this->app->db->query( "SELECT id FROM projects WHERE id = {$id}" );

    if ( !$q1->num_rows ) {
      $this->printError( 403, 163 );
    }

    $q1->free();

    $q1 = $this->app->db->query( "SELECT * FROM pictures WHERE project_id = {$id}" );

    if ( $q1->num_rows ) {
      while( $picture = $q1->fetch_assoc() ) {
        @unlink( $this->app->uploadsDir . "/project_{$picture['project_id']}/{$picture['path']}" );
      }

      $q1->free();

      $this->app->db->query( "DELETE FROM pictures WHERE project_id = {$id}" );
    }

    $this->app->db->query( "DELETE FROM projects WHERE id = {$id}" );

    $this->printResponse();
  }
}