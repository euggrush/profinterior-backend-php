<?php

if ( !defined('FROM_MAIN_ROUTER') || empty( $path ) ) {
  header( $_SERVER['SERVER_PROTOCOL'] . " 400 Bad Request" );
  die("400 Bad Request");
}

require_once __DIR__ . '/core.php';

$app = new ProfInterior\App();

$model = match( true ) {
  $path === '/' => ProfInterior\Template::pageFromHTML( file_get_contents( __DIR__ . '/view.html' ) ),
  $path === '/api/authorization' => new ProfInterior\Authorization( $app ),
  $path === '/api/categories' => new ProfInterior\Category( $app ),
  $path === '/api/pictures' => new ProfInterior\All_Pictures( $app ),
  $path === '/api/projects' => new ProfInterior\Project( $app ),
  $path === '/api/upload' => new ProfInterior\Upload_Picture( $app ),
  $path === '/api/user' => new ProfInterior\User( $app ),
  default => ProfInterior\Template::error( $app->lang['module_not_found'], 1 )
};
