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
        $row = Util::dbQuery(
                'SELECT * FROM Tags WHERE id = :id',
                array('id' => $id), false);

        if ($row) {
            $tag = self::newTag($row);
            return $tag;
        }

        return null;
    }

    public function findByText($text) {
        $row = Util::dbQuery(
                'SELECT * FROM Tags WHERE text = :text',
                array('text' => $text), false);

        if ($row) {
            $tag = self::newTag($row);
            return $tag;
        }

        return null;
    }

    public function findActive() {
        $rows = Util::dbQuery(
                'SELECT * FROM Tags WHERE id IN '.
                '(SELECT tagid FROM Tagged GROUP BY tagid ORDER BY COUNT(*) DESC LIMIT 10) LIMIT 5',
                array(), true);
        $tags = array();
        
        if ($rows) {
            foreach ($rows as $row) {
                $tag = $row['text'];
                $tags[] = '<a href="/pitterpatter/tag/'.$tag.'">#' . $tag . '</a>';
            }
        }
        
        return $tags;
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
                $row = Util::dbQuery(
                        'INSERT INTO Tags (text, last_active) VALUES (:text, :last_active) RETURNING id',
                        array('text' => $tag, 'last_active' => date('Y-m-d H:i:s')), false);
                $foundtag = new Tag(array('text' => $tag, 'id' => $row['id']));
            }
            Util::dbQuery(
                    'INSERT INTO Tagged (tagid, messageid) VALUES (:tagid, :messageid)',
                    array('tagid' => $foundtag->id, 'messageid' => $message->id), false);
        }
    }

    private function newTag($row) {
        $tag = new Tag(array(
            'text' => $row['text'],
            'last_active' => date('Y-m-d H:i:s'),
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
