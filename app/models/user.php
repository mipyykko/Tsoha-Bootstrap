<?php

use Util;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author pyykkomi
 */
require 'app/util/util.php';

class User extends BaseModel {
    public $id, $username, $realname, $password, $description, $email, $administrator,
           $public_profile, $registration_date, $last_seen;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
    }
    
    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Users');
        $query->execute();
        $rows = $query->fetchAll();
        $users = array();
        
        foreach ($rows as $row) {
            $users[] = self::getUser($row);
        }

        return $users;
    }

    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Users WHERE id = :id');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if ($row) {
            $user = self::getUser($row);
            return $user;
        }
        
        return null;
    }
    
    public static function findByName($username) {
        $query = DB::connection()->prepare('SELECT * FROM Users WHERE username = :username LIMIT 1');
        $query->execute(array('username' => $username));
        $row = $query->fetch();
        
        if ($row) {
            $user = self::getUser($row);
            return $user;
        }
        
        return null;
    }
    
    public function getUserinfo($user) {
        $userinfo = array('registration' => Util::getMonthAsString($user),
                          'posts' => 0,
                          'followed' => 0,
                          'followers' => 0);
        return $userinfo;
    }
    
    private function getUser($row) {
        $user = new User(array(
                'id' => $row['id'],
                'username' => $row['username'],
                'realname' => $row['realname'],
                'password' => $row['password'],
                'description' => $row['description'],
                'email' => $row['email'],
                'administrator' => $row['administrator'],
                'public_profile' => $row['public_profile'],
                'registration_date' => $row['registration_date'],
                'last_seen' => $row['last_seen']));

        return $user;
    }
    
}
