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
        $userinfo = User::getUserinfo($user);
        $messages = Message::userMessages($userid);
        View::make('user/index.html', array('user' => $user, 'messages' => $messages,
                                            'userinfo' => $userinfo));
    }
}
