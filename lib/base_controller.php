<?php

  class BaseController{

    public static function get_user_logged_in(){
        if (isset($_SESSION['user'])) {
            $id = $_SESSION['user'];
            $user = User::find($id);
            
            return $user;
        }

        return null;
    }

    public static function check_logged_in(){
        if (!self::get_user_logged_in()) {
            return false;
        }
        
        return true;
        // Jos käyttäjä ei ole kirjautunut sisään, ohjaa hänet toiselle sivulle (esim. kirjautumissivulle).
    }
    
    public static function admin_logged_in() {
        if (self::check_logged_in()) {
            return self::get_user_logged_in()->administrator;
        }
        
        return false;
    }

  }
