<?php
  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  View::make('home.html');
    }

    public static function sandbox(){
        $test = User::find(1);
        $test2 = User::all();
        $test3 = Message::find(1);
        $test4 = Message::all();
        Kint::dump($test);
        Kint::dump($test2);
        Kint::dump($test3);
        Kint::dump($test4);
    }
    
    public static function rekisteroidy() {
        View::make('suunnitelmat/rekisterointi.html');
    }
    
    public static function kayttajasivu() {
        View::make('suunnitelmat/kayttajasivu.html');
    }
  }
