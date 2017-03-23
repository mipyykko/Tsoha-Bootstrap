<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  View::make('home.html');
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      View::make('helloworld.html');
    }
    
    public static function rekisteroidy() {
        View::make('suunnitelmat/rekisterointi.html');
    }
    
    public static function kayttajasivu() {
        View::make('suunnitelmat/kayttajasivu.html');
    }
  }
