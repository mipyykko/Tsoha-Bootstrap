<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of messagecontroller
 *
 * @author pyykkomi
 */
class MessageController extends BaseController {

    public static function index() {
        $messages = Message::all();
        $messageinfo = Message::getMessageInfo($messages);
        View::make('message/index.html', array('messageinfo' => $messageinfo));
    }
    
    public static function userindex($userid) {
        $user = User::find($userid);
        $userinfo = User::getUserinfo($user); // nää vois pakata myös samaan
        $messages = Message::findByUser($userid);
        $messageinfo = Message::getMessageInfo($messages); 
        View::make('user/index.html', array('user' => $user, 'messageinfo' => $messageinfo,
                                            'userinfo' => $userinfo));
    }

    public function tagindex($text) {
        $tag = Tag::findByText($text);
        
        if ($tag) {
            $messages = Message::findByTag($tag->id);
            $messageinfo = Message::getMessageInfo($messages);
            View::make('tag/index.html', array('messageinfo' => $messageinfo, 'tag' => $tag));
        } else {
            $tag = array('text' => $text);
            View::make('tag/index.html', array('tag' => $tag));
        }
    }

    public static function store() {
        $params = $_POST;
        $message = new Message(array(
                'userid' => $params['userid'],
                'replyid' => $params['replyid'],
                'text' => $params['text'],
                'sent' => date('Y-m-d H:i:s'),
                'public_message' => $params['public_message']
        ));
        $errors = $message->errors();
        
        if (count($errors) == 0) {
            $message->save();
        }
        Redirect::to('/user/'.$message->userid);
    }
    
    public static function search() {
        // TODO
        Redirect::to('/');
    }

}
