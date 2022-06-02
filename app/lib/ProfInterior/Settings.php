<?php

namespace ProfInterior;

class Settings {
  
  protected $dbSettings = [
    'dbhost' => 'localhost',
    'dbuser' => '',
    'dbpass' => '',
    'dbname' => '',
    'dbcharset' => 'utf8mb4'
  ];

  public const SECRET = '';

  public const ACCESS_TOKEN_HASH_SECRET = '';

  public const DBNAME = '';

  public const AUTH_ATTEMPTS = 5;
  public const AUTH_ATTEMPTS_INTERVAL = "PT30M";
  public const TOKEN_EXPIRATION_INTERVAL = "PT6H";

  public const UPLOADS_PER_PROJECT = 10;
  public const UPLOAD_MAX_FILESIZE = 10_240_000;

  public const PASSWORD_HASH_ALGO = PASSWORD_DEFAULT;
}