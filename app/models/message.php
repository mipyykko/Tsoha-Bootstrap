<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of message
 *
 * @author pyykkomi
 */
class Message extends BaseModel {

    public $id, $userid, $replyid, $text, $sent, $public_message;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_text');
    }

    public static function all() {
        $query = DB::connection()->prepare(
                'SELECT * FROM Messages ORDER BY sent DESC'
        );
        $query->execute();
        $rows = $query->fetchAll();
        $messages = array();

        foreach ($rows as $row) {
            $messages[] = self::newMessage($row, true);
        }

        return $messages;
    }

    public static function find($id) {
        $query = DB::connection()->prepare(
                'SELECT * FROM Messages WHERE id = :id ORDER BY sent DESC'
        );
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $message = self::newMessage($row, true);
            return $message;
        }

        return null;
    }

    public static function findByUser($userid) {
        $query = DB::connection()->prepare(
                'SELECT * FROM Messages WHERE userid = :userid ORDER BY sent DESC'
        );
        $query->execute(array('userid' => $userid));
        $rows = $query->fetchAll();
        $messages = array();

        foreach ($rows as $row) {
            $messages[] = self::newMessage($row, true);
        }

        return $messages;
    }

    public static function findByTag($id) {
        $query = DB::connection()->prepare(
                'SELECT * FROM Messages WHERE id IN ' .
                '(SELECT messageid FROM Tagged WHERE tagid = :tagid) ORDER BY sent DESC'
        );
        $query->execute(array('tagid' => $id));
        $rows = $query->fetchAll();
        $messages = array();

        foreach ($rows as $row) {
            $messages[] = self::newMessage($row, true);
        }

        return $messages;
    }

    public function save() {
        $query = DB::connection()->prepare(
                'INSERT INTO Messages (userid, text, sent, public_message) ' .
                'VALUES (:userid, :text, :sent, :public_message) RETURNING id'
        );
        $query->execute(array('userid' => $this->userid, 'text' => $this->text,
            'sent' => $this->sent, 'public_message' => $this->public_message));
        $row = $query->fetch();
        $this->id = $row['id'];
        $this->replyid = $row['id'];

        // Ei kovin kaunista mutta nyt on näin
        $query = DB::connection()->prepare('UPDATE Messages SET replyid = :replyid WHERE id = :id');
        $query->execute(array('id' => $this->id, 'replyid' => $this->replyid));

        Tag::parseAndSave($this);
    }

    public static function getMessageinfo($messages) {
        $messageinfo = array();
        $users = array();
        foreach ($messages as $message) {
            if (!isset($users[$message->userid])) {
                $users[$message->userid] = User::find($message->userid);
            }
            $messageinfo[] = array('message' => $message, 'user' => $users[$message->userid]);
        }

        return $messageinfo;
    }

    private function newMessage($row, $parse) {
        $message = new Message(array(
            'id' => $row['id'],
            'userid' => $row['userid'],
            'replyid' => $row['replyid'],
            'text' => $parse ? self::parsetags($row['text']) : $row['text'],
            'sent' => $row['sent'],
            'public_message' => $row['public_message']));

        return $message;
    }

    private function parsetags($message) {
        if (!$message) {
            return null;
        }
        
        $tags = array();
        \preg_match_all("/(#[\p{Pc}\p{N}\p{L}\p{Mn}]+)/u", $message, $tags);
        if ($tags) {
            foreach ($tags[1] as $tag) { // TODO:fix
                $message = \str_replace($tag, "<a href=\"/pitterpatter/tag/" . \substr($tag, 1) . "\">" . $tag . "</a>", $message);
            }
        }
        return $message;
    }
    
    public function validate_text() {
        $errors = array();
        if ($this->text == '' || strlen($this->text) < 1) {
            $errors[] = 'Viestissä ei ole sisältöä!';
        }
        
        return $errors;
    }

}
