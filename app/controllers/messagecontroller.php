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
        View::make('message/index.html', array('messages' => $messages));
    }
    
    public static function userindex($userid) {
        $user = User::find($userid);
        $userinfo = User::getUserinfo($user); // nää vois pakata myös samaan
        $messages = Message::userMessages($userid);
        $messageinfo = Message::getMessageInfo($messages); 
        View::make('user/index.html', array('user' => $user, 'messageinfo' => $messageinfo,
                                            'userinfo' => $userinfo));
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
        $message->save();
        Redirect::to('/user/'.$message->userid);
    }
}
