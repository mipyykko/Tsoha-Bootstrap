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
    }

    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Messages');
        $query->execute();
        $rows = $query->fetchAll();
        $messages = array();

        foreach ($rows as $row) {
            $messages[] = self::getMessage($row, true);
        }

        return $messages;
    }

    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Messages WHERE id = :id');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $message = self::getMessage($row, true);
            return $message;
        }

        return null;
    }

    public static function userMessages($userid) {
        $query = DB::connection()->prepare('SELECT * FROM Messages WHERE userid = :userid');
        $query->execute(array('userid' => $userid));
        $rows = $query->fetchAll();
        $messages = array();
        
        foreach ($rows as $row) {
            $messages[] = self::getMessage($row, true);
        }
        
        return $messages;
    }
    
    private function getMessage($row, $parse) {
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
        if ($message) {
            $tags = array();
            \preg_match_all("/(#[\p{Pc}\p{N}\p{L}\p{Mn}]+)/u", $message, $tags);
            if ($tags) {
                foreach ($tags[1] as $tag) {
                    $message = \str_replace($tag, "<a href=\"tags/" . \substr($tag, 1) . "\">".$tag."</a>", $message);
                }
            }
        }
        return $message;
    }

}
