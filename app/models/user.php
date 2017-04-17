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
                             'validate_description', 'validate_email');
    }
    
    public static function all() {
        $rows = Util::dbQuery(
                'SELECT * FROM Users',
                array(), true);
        $users = array();
        
        foreach ($rows as $row) {
            $users[] = self::getUser($row);
        }

        return $users;
    }

    public static function find($id) {
        $row = Util::dbQuery(
                'SELECT * FROM Users WHERE id = :id',
                array('id' => $id), false);
        
        if ($row) {
            $user = self::getUser($row);
            return $user;
        }
        
        return null;
    }
    
    public static function findByName($username) {
        $row = Util::dbQuery(
                'SELECT * FROM Users WHERE username = :username LIMIT 1',
                array('username' => $username), false);
        
        if ($row) {
            $user = self::getUser($row);
            return $user;
        }
        
        return null;
    }
    
    public function save() {
        $row = Util::dbQuery(
                'INSERT INTO Users (username, realname, password, description, email, public_profile, '.
                'administrator, registration_date, last_seen) VALUES (:username, :realname, '.
                ':password, :description, :email, :public_profile, :administrator, :registration_date, '.
                ':last_seen) RETURNING id',
                array('username' => $this->username, 'realname' => $this->realname, 
                      'password' => $this->password, 'description' => $this->description, 
                      'email' => $this->email, 
                      'administrator' => $this->administrator ? 't' : 'f',
                      'public_profile' => $this->public_profile ? 't' : 'f',
                      'registration_date' => $this->registration_date, 
                      'last_seen' => $this->last_seen));
        $this->id = $row['id'];
    }
    
    public function update() {
        Util::dbQuery(
                'UPDATE Users SET realname = :realname, description = :description, email = :email, '.
                'public_profile = :public_profile, last_seen = :last_seen WHERE id = :id',
                array('realname' => $this->realname, 'description' => $this->description,
                      'email' => $this->email, 
                      'public_profile' => $this->public_profile ? 't' : 'f',
                      'last_seen' => $this->last_seen,
                      'id' => $this->id), false);
    }
    
    public function getUserinfo($user) {
        $rows = Util::dbQuery(
                'SELECT id FROM Messages WHERE userid = :userid',
                array('userid' => $user->id), true);
        $messages = \count($rows);
        
        $rows = Util::dbQuery(
                'SELECT userid FROM Followed WHERE userid = :userid',
                array('userid' => $user->id), true);
        $followed = \count($rows);;
        
        $rows = Util::dbQuery(
                'SELECT userid FROM Followed WHERE followed_userid = :userid',
                array('userid' => $user->id), true);
        $followers = \count($rows);
        
        $userinfo = array('registration' => Util::getMonthAsString($user->registration_date),
                          'messages' => $messages,
                          'followed' => $followed,
                          'followers' => $followers);
        return $userinfo;
    }

    public function follows($follow_id) {
        $rows = Util::dbQuery(
                'SELECT userid FROM Followed WHERE userid = :userid AND '.
                'followed_userid = :followed_userid',
                array('userid' => $this->id, 'followed_userid' => $follow_id), true);
        return $rows != null;
    }
    
    public function follow($id) {
        Util::dbQuery(
                'INSERT INTO Followed VALUES (:userid, :followed_userid)',
                array('userid' => $this->id, 'followed_userid' => $id), false);
    }
    
    public function unfollow($id) {
        Util::dbQuery(
                'DELETE FROM Followed WHERE userid = :userid AND followed_userid = :followed_userid',
                array('userid' => $this->id, 'followed_userid' => $id), false);
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
    
    public function auth($username, $password) {
        $row = Util::dbQuery(
                'SELECT * FROM Users WHERE username = :username AND password = :password LIMIT 1',
                array('username' => $username, 'password' => $password), false);
        if ($row) {
            return self::getUser($row);
        }
        
        return null;
    }
    
    public function validate_username() {
        $errors = array();
        
        if (strlen($this->username) < 4 || 
            strlen($this->username) > 32) {
            $errors['username'] = 'Käyttäjänimen tulee olla 4-32 merkkiä pitkä!';
        }
        if (self::findByName($this->username)) {
            $errors['username_taken'] = 'Käyttäjänimi on varattu!'; // hmh
        }
        
        return $errors;
    }
    
    public function validate_password() {
        $errors = array();
        
        if (strlen($this->password) < 8 ||
            strlen($this->password) > 32) {
            $errors['password'] = 'Salasanan tulee olla 4-32 merkkiä pitkä!';
        }
        
        return $errors;
    }
    
    public function validate_realname() {
        $errors = array();
        
        if (strlen($this->realname) > 64) {
            $errors['realname'] = 'Oikea nimi saa olla korkeintaan 64 merkkiä pitkä!';
        }
        
        return $errors;
    }
    
    public function validate_email() {
        $errors = array();
        
        if ($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Sähköpostiosoite ei ole kelvollinen!';
        }
        
        return $errors;
    }
    public function validate_description() {
        $errors = array();
        
        if (strlen($this->description) > 255) {
            $errors['description'] = 'Kuvaus saa olla korkeintaan 255 merkkiä pitkä!';
        }
        
        return $errors;
    }
}