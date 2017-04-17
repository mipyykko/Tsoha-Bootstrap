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

    public static function all($logged_in) {
        $rows = Util::dbQuery(
                'SELECT * FROM Messages '.
                ($logged_in ? '' : ' WHERE public_message = \'t\' ').
                'ORDER BY sent DESC', 
                array(), true
        );
        $messages = array();

        foreach ($rows as $row) {
            $messages[] = self::newMessage($row, true);
        }

        return $messages;
    }

    public static function followed($id) {
        $rows = Util::dbQuery(
                'SELECT * FROM Messages WHERE userid IN '.
                '(SELECT followed_userid FROM Followed WHERE userid = :id) '.
                'ORDER BY sent DESC',
                array('id' => $id), true);
        $messages = array();
        
        foreach ($rows as $row) {
            $messages[] = self::newMessage($row, true);
        }
        
        return $messages;
    }
    
    public static function find($id, $logged_in) {
        $row = Util::dbQuery(
                'SELECT * FROM Messages WHERE id = :id '.
                $logged_in ? '' : 'AND public_message = \'t\' '.
                'ORDER BY sent DESC',
                array('id' => $id), false);

        if ($row) {
            $message = self::newMessage($row, true);
            return $message;
        }

        return null;
    }

    public static function findByUser($userid, $logged_in) {
        $rows = Util::dbQuery(
                'SELECT * FROM Messages WHERE userid = :userid '.
                ($logged_in ? '' : 'AND public_message = \'t\' ').
                'ORDER BY sent DESC',
                array('userid' => $userid), true);
        $messages = array();

        foreach ($rows as $row) {
            $messages[] = self::newMessage($row, true);
        }

        return $messages;
    }

    public static function findByTag($id, $logged_in) {
        $rows = Util::dbQuery(
                'SELECT * FROM Messages WHERE id IN ' .
                '(SELECT messageid FROM Tagged WHERE tagid = :tagid) '.
                $logged_in ? '' : ' AND public_profile = \'t\' '.
                'ORDER BY sent DESC',
                array('tagid' => $id), true);
        $messages = array();

        foreach ($rows as $row) {
            $messages[] = self::newMessage($row, true);
        }

        return $messages;
    }

    public function save() {
        $row = Util::dbQuery(
                'INSERT INTO Messages (userid, text, sent, public_message) ' .
                'VALUES (:userid, :text, :sent, :public_message) RETURNING id',
                array('userid' => $this->userid, 'text' => $this->text,
                'sent' => $this->sent, 
                'public_message' => $this->public_message ? 't' : 'f'),
                false);
        $this->id = $row['id'];
        $this->replyid = $row['id'];

        // Ei kovin kaunista mutta nyt on näin
        Util::dbQuery(
                'UPDATE Messages SET replyid = :replyid WHERE id = :id',
                array('id' => $this->id, 'replyid' => $this->replyid),
                false);

        Tag::parseAndSave($this);
    }
    
    public function remove($id) {
        Util::dbQuery(
                'DELETE FROM Tagged WHERE messageid = :messageid',
                array('messageid' => $id), false);
        Util::dbQuery(
                'DELETE FROM Messages WHERE id = :id',
                array('id' => $id), false);
        
        // poista tagit jotka jäivät orvoiksi?
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
            $errors['text'] = 'Viestissä ei ole sisältöä!';
        }
        
        return $errors;
    }

}
