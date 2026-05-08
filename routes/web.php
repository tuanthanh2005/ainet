<?php

/**
 * Define Web Routes
 */

return [
    '/' => 'HomeController@index',
    '/admin' => 'AdminController@adminDashboard',
    '/admin/save' => 'AdminController@adminSaveSettings',
];
