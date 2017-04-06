<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of usercontroller
 *
 * @author pyykkomi
 */
class UserController extends BaseController {

    public static function register() {
        View::make('user/register.html');
    }
    
    public static function settings() {
        if (!self::check_logged_in()) {
            View::make('user/register.html');
        }
        View::make('user/settings.html', array('user' => self::get_user_logged_in()));
    }
    
    public static function handleregister() {
         $params = $_POST;
         $user = new User(array(
             'username' => $params['username'],
             'password' => $params['password'],
             'realname' => $params['realname'],
             'description' => $params['description'],
             'email' => $params['email'],
             'administrator' => false,
             'public_profile' => isset($params['public_profile']) ? true : false,
             'last_seen' => date('Y-m-d H:i:s'),
             'registration_date' => date('Y-m-d H:i:s')
         ));
         
         $errors = $user->errors();
         
         if (count($errors) == 0) {
             $user->save();
             Redirect::to('/user/'.$user->id);
         }
         View::make('user/register.html', array('user' => $user, 'errors' => $errors));
    }
    
    public static function handlelogin() {
        $params = $_POST;
        
        $user = User::auth($params['username'], $params['password']);
        
        if (!$user) {
            Redirect::to("/", array('login_error' => true));
            MessageController::index();
        } else {
            $_SESSION['user'] = $user->id;
            Redirect::to("/user/".$user->id);
        }
    }
    
    public static function handlesettings() {
        $params = $_POST;
        $user = self::get_user_logged_in();
        $user->realname = $params['realname'];
        $user->description = $params['description'];
        $user->email = $params['email'];
        $user->public_profile = isset($params['public_profile']) ? true : false;
        $user->last_seen = date('Y-m-d H:i:s');
        
        $errors = $user->errors();
        if (count($errors) == 0 || (count($errors) == 1 && isset($errors['username']))) {
            $user->update();
            Redirect::to('/user/'.$user->id);
        }
        View::make('user/settings.html', array('user' => $user, 'errors' => $errors));
    }
    
    public static function logout() {
        $_SESSION['user'] = null;
        
        Redirect::to('/');
    }
}
