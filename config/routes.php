<?php

  $routes->get('/', function() {
    MessageController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  $routes->get('/rekisterointi', function() {
    HelloWorldController::rekisteroidy();
  });
  
  $routes->get('/kayttajasivu', function() {
    HelloWorldController::kayttajasivu();
  });
  
  $routes->get('/register', function() {
      UserController::register();
  });
  
  $routes->post('/register', function() {
      UserController::handleregister();
  });

  $routes->get('/password', function() {
     UserController::passwordchange();
  });
  
  $routes->post('/password', function() {
      UserController::handlepasswordchange();
  });
  
  $routes->post('/login', function() {
      UserController::handlelogin();
  });
  
  $routes->get('/message', function() {
      MessageController::index();
  });
  
  $routes->post('/search', function() {
      MessageController::search();
  });
  
  $routes->get('/settings', function() {
      UserController::settings();
  });
  
  $routes->post('/settings', function() {
      UserController::handlesettings();
  });

  $routes->post('/settings/password', function() {
      //todo
  });
  
  $routes->get('/user/:id/edit', function($id) {
      UserController::adminsettings($id);
  });
  
  $routes->post('/user/:id/edit', function($id) {
      UserController::handlesettings($id);
  });
  
  $routes->get('/user/:id/follow', function($id) {
      UserController::follow($id);
  });
  
  $routes->get('/user/:id/unfollow', function($id) {
      UserController::unfollow($id);
  });
  
  $routes->get('/followed', function() {
      MessageController::followed();
  });
  
  $routes->get('/logout', function() {
      UserController::logout();
  });
  
  $routes->get('/username/:username', function($username) {
      MessageController::userindex($username);
  });
  
  $routes->post('/user/:id', function() {
      MessageController::store();
  });
  
  $routes->get('/user/:id', function($id) {
      MessageController::userindex($id);
  });

  $routes->get('/tag/:tag', function($tag) {
      MessageController::tagindex($tag);
  });
  
  $routes->get('/remove/:id', function($id) {
      MessageController::remove($id);
  });