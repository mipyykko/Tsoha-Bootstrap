<?php

  $routes->get('/', function() {
    HelloWorldController::index();
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
  
  $routes->get('/message', function() {
      MessageController::index();
  });
  
  $routes->get('/username/:username', function($username) {
      MessageController::userindex($username);
  });
  
  $routes->post('/user/:id', function($id) {
      MessageController::store();
  });
  
  $routes->get('/user/:id', function($id) {
      MessageController::userindex($id);
  });

  $routes->get('/tag/:tag', function($tag) {
      MessageController::tagindex($tag);
  });