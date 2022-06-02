<?php

namespace ProfInterior;

final class Language {
    public array $lang;

    public function __construct( string $lang ) {
        switch( $lang ) {
            case 'en':
            default:
                $this->lang = $this->getEN();
            break;
        }
    }

    private function getEN() : array {
        return [
            'api_code_1000' => "Internal error: invalid language parameter.",
            'api_code_101' => "Email or password is empty.",
            'api_code_102' => "Email or password is incorrent.",
            'api_code_103' => "You don't have permissions to access to this section.",
            'api_code_104' => "Too many authorization attempts, try again later.",
            'api_code_105' => "Not yet implemented.",
            'api_code_106' => "Method is not allowed.",
            'api_code_107' => "Authentication credentials is not presented.",
            'api_code_108' => "Authentication credentials is invalid.",
            'api_code_109' => "Invalid request.",
            'api_code_131' => "These accounts are not found.",
            'api_code_132' => "This account is banned.",
            'api_code_133' => "You have been banned.",
            'api_code_140' => "Category name should not be empty.",
            'api_code_141' => "Category id should be greater than 0.",
            'api_code_142' => "Category does not exists.",
            'api_code_143' => "One or more projects are associated with this category. Change the binding to another category and try again.",
            'api_code_151' => "Picture id should be greater than 0.",
            'api_code_152' => "Picture does not exists.",
            'api_code_160' => "Project title should not be empty.",
            'api_code_161' => "Category id should be greater than 0.",
            'api_code_162' => "Category does not exists.",
            'api_code_163' => "Project does not exists.",
            'api_code_164' => "Project id should be greater than 0.",
            'api_code_171' => "Category id should be greater than 0.",
            'api_code_172' => "Category does not exists.",
            'api_code_173' => "Project does not exists.",
            'api_code_174' => "Project id should be greater than 0.",
            'api_code_175' => "Upload limit (%d) for this project is reached. Please delete at least one picture and try again.",
            'api_code_176' => "No one file has been selected.",
            'api_code_177' => "Could not create category folder.",
            'api_code_179' => "Could not upload the file (error #1).",
            'api_code_180' => "Uploaded file is too large.",
            'api_code_181' => "Invalid file format.",
            'api_code_182' => "Could not upload the file (error #2).",
            'api_code_190' => "Minimal password length is 8 symbols.",
            'api_code_191' => "Invalid email.",
            'api_code_192' => "Email is taken.",
            'api_code_193' => "Could not create user.",
            'api_code_194' => "User does not exists.",
            'api_code_195' => "User id should be greater than 0.",
            'module_not_found' => 'Module is not found.',
        ];
    }
}