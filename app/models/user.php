<?php

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
class User extends BaseModel {
    public $id, $username, $realname, $password, $description, $email, $administrator,
           $public_profile, $registration_date, $last_seen;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_username', 'validate_password', 'validate_realname',
                             'validate_description');
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
    
    public function save() {
        $query = DB::connection()->prepare(
                'INSERT INTO Users (username, realname, password, description, email, public_profile, '.
                'administrator, registration_date, last_seen) VALUES (:username, :realname, '.
                ':password, :description, :email, :public_profile, :administrator, :registration_date, '.
                ':last_seen) RETURNING id');
        $query->execute(array('username' => $this->username, 'realname' => $this->realname, 
                              'password' => $this->password, 'description' => $this->description, 
                              'email' => $this->email, 
                              'administrator' => $this->administrator ? 't' : 'f',
                              'public_profile' => $this->public_profile ? 't' : 'f',
                              'registration_date' => $this->registration_date, 
                              'last_seen' => $this->last_seen));
        $row = $query->fetch();
        $this->id = $row['id'];
    }
    
    public function getUserinfo($user) {
        $userinfo = array('registration' => Util::getMonthAsString($user->registration_date),
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
                'public_profile' => $row['public_profile'] == 't' ? true : false,
                'registration_date' => $row['registration_date'],
                'last_seen' => $row['last_seen']));

        return $user;
    }
    
    public function validate_username() {
        $errors = array();
        
        if (strlen($this->username) < 4 || 
            strlen($this->username) > 32) {
            $errors[] = 'Käyttäjänimen tulee olla 4-32 merkkiä pitkä!';
        }
        if (self::findByName($this->username)) {
            $errors[] = 'Käyttäjänimi on varattu!';
        }
        
        return $errors;
    }
    
    public function validate_password() {
        $errors = array();
        
        if (strlen($this->password) < 8 ||
            strlen($this->password) > 32) {
            $errors[] = 'Salasanan tulee olla 4-32 merkkiä pitkä!';
        }
        
        return $errors;
    }
    
    public function validate_realname() {
        $errors = array();
        
        if (strlen($this->realname) > 64) {
            $errors[] = 'Oikea nimi saa olla korkeintaan 64 merkkiä pitkä!';
        }
        
        return $errors;
    }
    
    public function validate_description() {
        $errors = array();
        
        if (strlen($this->description) > 255) {
            $errors[] = 'Kuvaus saa olla korkeintaan 255 merkkiä pitkä!';
        }
        
        return $errors;
    }
}