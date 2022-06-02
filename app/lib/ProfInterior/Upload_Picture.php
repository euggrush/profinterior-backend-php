<?php

declare(strict_types=1);

namespace ProfInterior;

final class Upload_Picture extends Api {
  private string $myRole;
  private int $myUserId, $currentTime;
  private \DateTime $dt;

  private const UPLOAD_FOR_CATEGORY = 110;
  private const UPLOAD_FOR_PROJECT = 111;

  protected function needImplementation() {}

  public function __construct( App &$app ) {
    parent::__construct( $app );

    if ( $this->app->requestMethod !== 'POST' ) {
      $this->printError( 405, 106 );
    }

    $this->upload();
  }

  private function upload() : void {
    $this->checkAccessLevel( anonymousIsAllowed: false );

    $this->myRole = $this->app->user['role'];
    $this->myUserId = $this->app->user['user_id'];

    $this->dt = new \DateTime();
    $this->currentTime = $this->dt->getTimestamp();

    if ( $this->myRole !== 'admin' ) {
      $this->printError( 403, 103 );
    }

    $this->app->get['act'] ??= "";

    switch( $this->app->get['act'] ) {
      case 'category':
        $this->uploadForCategory();
        break;
      case 'project':
        $this->uploadForProject();
        break;
      default:
        $this->printError( 403, 103 );
        break;
    }
  }

  private function uploadForCategory() : void {
    $categoryId = intval( $this->app->get['categoryId'] ?? 0 );

    if ( !$categoryId ) {
      $this->printError( 403, 171 );
    }

    $q1 = $this->app->db->query( "SELECT * FROM categories WHERE id = {$categoryId}" );

    if ( !$q1->num_rows ) {
      $this->printError( 403, 172 );
    }

    $category = $q1->fetch_assoc();
    $q1->free();

    $q1 = $this->app->db->query( "SELECT * FROM category_images WHERE category_id = {$categoryId}" );

    if ( $q1->num_rows ) {
      $categoryImage = $q1->fetch_assoc();
      $q1->free();

      @unlink( $this->app->uploadsDir . "/category_{$category['id']}/{$categoryImage['path']}" );

      $this->app->db->query( "DELETE FROM category_images WHERE category_id = {$categoryId}" );
    }

    $categoryFolder = $this->app->uploadsDir . "/category_{$category['id']}";

    $this->uploadFile( static::UPLOAD_FOR_CATEGORY, $categoryFolder, $categoryId );
  }

  private function uploadForProject() : void {
    $projectId = intval( $this->app->get['projectId'] ?? 0 );

    if ( !$projectId ) {
      $this->printError( 403, 174 );
    }

    $q1 = $this->app->db->query( "SELECT * FROM projects WHERE id = {$projectId}" );

    if ( !$q1->num_rows ) {
      $this->printError( 403, 173 );
    }

    $project = $q1->fetch_assoc();

    $q1->free();

    $q1 = $this->app->db->query( "SELECT * FROM pictures WHERE project_id = {$projectId}" );

    $numRows = $q1->num_rows;
    $q1->free();

    if ( $numRows >= Settings::UPLOADS_PER_PROJECT ) {
      $this->printError( 403, 175, Settings::UPLOADS_PER_PROJECT );
    }

    $projectFolder = $this->app->uploadsDir . "/project_{$project['id']}";

    $this->uploadFile( static::UPLOAD_FOR_PROJECT, $projectFolder, $projectId );
  }

  private function uploadFile( int $uploadType, string $dirName, int $targetId ) : void {
    if ( !file_exists( $dirName ) ) {
      $cfState = @mkdir( directory : $dirName, recursive : true );
    
      if ( !$cfState ) {
        $this->printError( 500, 178 );
      }
    }

    if ( empty( $this->app->files ) || empty( $this->app->files['asset'] ) ) {
      $this->printError( 403, 176 );
    }

    $fileName = $this->app->files['asset']['tmp_name'];

    if ( $this->app->files['asset']['error'] !== UPLOAD_ERR_OK ) {
      $this->deleteUploadedFiles( $fileName );
      $this->printError( 500, 179 );
    }

    if ( $this->app->files['asset']['size'] > Settings::UPLOAD_MAX_FILESIZE ) {
      $this->deleteUploadedFiles( $fileName );
      $this->printError( 403, 180 );
    }

    $detectedMime = @mime_content_type( $fileName );

    if ( !in_array( needle: $detectedMime, haystack: [ 
      "image/gif",
      "image/pjpeg",
      "image/jpeg", 
      "image/png", 
    ], strict: true ) ) {
      $this->deleteUploadedFiles( $fileName );
      $this->printError( 403, 181 );
    }

    $passedFileName = basename( $this->app->files['asset']['name'] );
    $ext = mb_strrchr( $passedFileName, '.' ) ?: ".000";
    $ext = str_replace( ['..', '/', '\\'], '', $ext );
    $newFileName = microtime( true ) * 10000 . $ext;
  
    if ( !move_uploaded_file( $fileName, $dirName . "/{$newFileName}" ) ) {
      $this->deleteUploadedFiles( $fileName );
      $this->printError( 500, 182 );
    }

    $dirName = str_replace( $_SERVER['DOCUMENT_ROOT'] . '/', '', $dirName );

    if ( $uploadType === static::UPLOAD_FOR_CATEGORY ) {
      $categoryImagesTableDataset = [];

      $categoryImagesTableDataset['category_id'] = $targetId;
      $categoryImagesTableDataset['path'] = $this->app->db->extendedEscape( $newFileName );
      $categoryImagesTableDataset['created_at'] = $this->currentTime;

      $valuesLine = $this->app->db->getSQLLine( $categoryImagesTableDataset );

      $this->app->db->query( "INSERT INTO category_images SET {$valuesLine}" );
      $categoryImageId = intval( $this->app->db->insert_id );

      $this->dt->setTimestamp( $this->currentTime );
      $createdAt = $this->formatDateTimeRepresentation( $this->dt );

      $this->printResponse([
        'id' => $categoryImageId,
        'categoryId' => $targetId,
        'fullPath' => $dirName . "/{$newFileName}",
        'path' => $newFileName,
        'createdAt' => $createdAt
      ]);
    }
    else if ( $uploadType === static::UPLOAD_FOR_PROJECT ) {
      $picturesTableDataset = [];

      $picturesTableDataset['project_id'] = $targetId;
      $picturesTableDataset['path'] = $this->app->db->extendedEscape( $newFileName );
      $picturesTableDataset['created_at'] = $this->currentTime;

      $valuesLine = $this->app->db->getSQLLine( $picturesTableDataset );

      $this->app->db->query( "INSERT INTO pictures SET {$valuesLine}" );
      $pictureId = intval( $this->app->db->insert_id );

      $this->dt->setTimestamp( $this->currentTime );
      $createdAt = $this->formatDateTimeRepresentation( $this->dt );

      $this->printResponse([
        'id' => $pictureId,
        'projectId' => $targetId,
        'fullPath' => $dirName . "/{$newFileName}",
        'path' => $newFileName,
        'createdAt' => $createdAt
      ]);
    }
  }

  private function deleteUploadedFiles( string|array $files ) : void {
    if ( isset( $files ) ) {
      if ( !is_array( $files ) ) {
        if ( file_exists( $files ) ) @unlink( $files );
      }
      else {
        foreach( $files as $file ) {
          if ( file_exists( $file ) ) @unlink( $file );
        }
      }
    }
  }
}