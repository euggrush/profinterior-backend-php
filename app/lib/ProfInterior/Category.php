<?php

namespace ProfInterior;

final class Category extends Api {
  protected function needImplementation() {}

  public function __construct( App &$app ) {
    parent::__construct( $app );
    $this->category();
  }

  private function category() : void {
    $this->checkAccessLevel( anonymousIsAllowed: true );

    switch( $this->app->requestMethod ) {
      case 'GET':
        $this->categoryGET();
        break;
      case 'POST':
        $this->categoryPOST();
        break;
      case 'PUT':
        $this->categoryPUT();
        break;
      case 'DELETE':
        $this->categoryDELETE();
        break;
      default:
        $this->printError( 405, 106 );
        break;
    }
  }

  private function categoryGET() : void {
    $myRole = $this->app->user['role'];
    $myUserId = $this->app->user['user_id'];

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    $categories = [];

    $q1 = $this->app->db->query( "SELECT * FROM categories ORDER BY id ASC" );

    while( $category = $q1->fetch_assoc() ) {
      $categoryId = intval( $category['id'] );

      $image = "";

      $q2 = $this->app->db->query( "SELECT * FROM category_images WHERE category_id = {$categoryId}" );

      if ( $q2->num_rows ) {
        $picture = $q2->fetch_assoc();

        $image = $this->app->uploadsDir . "/category_{$categoryId}/{$picture['path']}";
        $image = str_replace( $_SERVER['DOCUMENT_ROOT'] . '/', '', $image );
      }

      $q2->free();

      $categories[] = [
        'categoryId' => $categoryId,
        'name' => $category['name'],
        'picture' => $image
      ];
    }

    $q1->free();

    $this->printResponse( [ 'count' => count( $categories ), 'categories' => $categories ] );
  }

  private function categoryPOST() : void {
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

    $categoryName = $this->app->db->extendedEscape( $data->name ?? "" );

    if ( empty( $categoryName ) ) {
      $this->printError( 403, 140 );
    }

    $categoriesTableDataset = [];

    $categoriesTableDataset['name'] = $categoryName;

    $valuesLine = $this->app->db->getSQLLine( $categoriesTableDataset );

    $this->app->db->query( "INSERT INTO categories SET {$valuesLine}" );
    $categoryId = intval( $this->app->db->insert_id );

    $this->printResponse([
      'categoryId' => $categoryId,
      'name' => $categoryName,
    ]);
  }

  private function categoryPUT() : void {
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

    $id = intval( $data->categoryId ?? 0 );
    $categoryName = $this->app->db->extendedEscape( $data->name ?? "" );

    if ( !$id ) {
      $this->printError( 403, 141 );
    }

    $q1 = $this->app->db->query( "SELECT * FROM categories WHERE id = {$id}" );

    if ( !$q1->num_rows ) {
      $this->printError( 403, 142 );
    }

    $q1->free();

    if ( empty( $categoryName ) ) {
      $this->printError( 403, 140 );
    }

    $categoriesTableDataset = [];

    $categoriesTableDataset['name'] = $categoryName;

    $valuesLine = $this->app->db->getSQLLine( $categoriesTableDataset );

    $this->app->db->query( "UPDATE categories SET {$valuesLine} WHERE id = {$id}" );

    $this->printResponse([
      'categoryId' => $id,
      'name' => $categoryName,
    ]);
  }

  private function categoryDELETE() : void {
    $myRole = $this->app->user['role'];
    $myUserId = $this->app->user['user_id'];

    $dt = new \DateTime();
    $currentTime = $dt->getTimestamp();

    if ( $myRole !== 'admin' ) {
      $this->printError( 403, 103 );
    }

    $id = intval( $this->app->get['categoryId'] ?? 0 );

    if ( !$id ) {
      $this->printError( 403, 141 );
    }

    $q1 = $this->app->db->query( "SELECT * FROM categories WHERE id = {$id}" );

    if ( !$q1->num_rows ) {
      $this->printError( 403, 142 );
    }

    $q1->free();

    $q1 = $this->app->db->query( "SELECT * FROM projects WHERE category_id = {$id}" );

    if ( $q1->num_rows ) {
      $this->printError( 403, 143 );
    }

    $q1->free();

    $q1 = $this->app->db->query( "SELECT * FROM category_images WHERE category_id = {$id}" );

    if ( $q1->num_rows ) {
      $picture = $q1->fetch_assoc();
      $q1->free();

      $this->app->db->query( "DELETE FROM category_images WHERE category_id = {$id}" );
      @unlink( $this->app->uploadsDir . "/category_{$id}/{$picture['path']}" );
    }

    $this->app->db->query( "DELETE FROM categories WHERE id = {$id}" );

    $this->printResponse();
  }
}