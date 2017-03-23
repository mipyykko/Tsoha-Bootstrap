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
