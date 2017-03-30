<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tag
 *
 * @author pyykkomi
 */
class Tag extends BaseModel {

    public $text, $id;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Tags WHERE id = :id');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $tag = self::newTag($row);
            return $tag;
        }

        return null;
    }

    public function findByText($text) {
        $query = DB::connection()->prepare('SELECT * FROM Tags WHERE text = :text');
        $query->execute(array('text' => $text));
        $row = $query->fetch();

        if ($row) {
            $tag = self::newTag($row);
            return $tag;
        }

        return null;
    }

    public function parseAndSave($message) {
        $tags = self::parsetags($message);

        if (!$tags) {
            return;
        }

        foreach ($tags as $tag) {
            $tag = substr($tag, 1);
            $foundtag = self::findByText($tag);
            if (!$foundtag) {
                $query = DB::connection()->prepare(
                        'INSERT INTO Tags (text) VALUES (:text) RETURNING id'
                );
                $query->execute(array('text' => $tag));
                $row = $query->fetch();
                $foundtag = new Tag(array('text' => $tag, 'id' => $row['id']));
            }
            $query = DB::connection()->prepare(
                    'INSERT INTO Tagged (tagid, messageid) VALUES (:tagid, :messageid)'
            );
            $query->execute(array('tagid' => $foundtag->id, 'messageid' => $message->id));
        }
    }

    private function newTag($row) {
        $tag = new Tag(array(
            'text' => $row['text'],
            'id' => $row['id']
        ));
        return $tag;
    }

    public function parsetags($message) {
        $tags = array();
        \preg_match_all("/(#[\p{Pc}\p{N}\p{L}\p{Mn}]+)/u", $message->text, $tags);

        return $tags[1];
    }

}
