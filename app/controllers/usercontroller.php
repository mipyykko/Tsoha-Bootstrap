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

    public static function handleregister() {
        $params = $_POST;
        $user = new User(array(
            'username' => $params['username'],
            'password' => $params['password'],
            'passwordcheck' => $params['passwordcheck'],
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
            self::handlelogin();
        }
        View::make('user/register.html', array('user' => $user, 'errors' => $errors));
    }

    public static function passwordchange() {
        if (!self::check_logged_in()) {
            View::make('user/register.html');
        }
        View::make('user/password.html');
    }

    public static function handlepasswordchange() {
        if (!self::check_logged_in()) {
            View::make('user/register.html');
        }

        $params = $_POST;
        $user = self::get_user_logged_in();

        // last minute cram-in-kludge:
        $new_user = clone $user;
        $new_user->password = $params['newpassword'];
        $new_user->passwordcheck = $params['newpasswordcheck'];
        $errors = $new_user->errors();
        if ($user->password != $params['newpassword']) {
            $errors['wrongpassword'] = 'Salasana väärin!';
        }

        unset($errors['username_taken']);

        if (count($errors) == 0) {
            $user = $new_user;
            $user->update();
            Redirect::to("/user/" . $user->id);
        }

        View::make('user/password.html', array('user' => self::get_user_logged_in(),
            'errors' => $errors));
    }

    public static function settings() {
        if (!self::check_logged_in()) {
            View::make('user/register.html');
        }
        View::make('user/settings.html', array('user' => self::get_user_logged_in()));
    }

    public static function adminsettings($id) {
        if (!self::check_logged_in() || !self::admin_logged_in()) {
            View::make('user/' . $id);
        }
        View::make('user/settings.html', array('user' => User::find($id), 'admin' => true));
    }

    public static function handlelogin() {
        $params = $_POST;

        $user = User::auth($params['username'], $params['password']);

        if (!$user) {
            Redirect::to("/", array('login_error' => true));
            MessageController::index();
        } else {
            $_SESSION['user'] = $user->id;
            Redirect::to("/user/" . $user->id);
        }
    }

    public static function handlesettings($id = null) {
        $params = $_POST;
        $user = User::find($params['userid']);
        $user->realname = $params['realname'];
        $user->description = $params['description'];
        $user->email = $params['email'];
        $user->public_profile = isset($params['public_profile']) ? true : false;
        $user->last_seen = date('Y-m-d H:i:s');

        $errors = $user->errors();
        if (isset($errors['username_taken'])) {
            unset($errors['username_taken']); // hmm
        }
        if (isset($errors['password'])) {
            unset($errors['password']);
        }
        if (count($errors) == 0) {
            $user->update();
            Redirect::to('/user/' . $user->id);
        }
        View::make('user/settings.html', array('user' => $user, 'errors' => $errors));
    }

    public static function follow($id) {
        $user_logged_in = self::get_user_logged_in();
        if ($user_logged_in && !$user_logged_in->follows($id)) {
            $user_logged_in->follow($id);
        }
        Redirect::to('/user/' . $id);
    }

    public static function unfollow($id) {
        $user_logged_in = self::get_user_logged_in();
        if ($user_logged_in && $user_logged_in->follows($id)) {
            $user_logged_in->unfollow($id);
        }
        Redirect::to('/user/' . $id);
    }

    public static function logout() {
        $_SESSION['user'] = null;

        Redirect::to('/');
    }

}
