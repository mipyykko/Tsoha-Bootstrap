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
        $messages = Message::all(self::check_logged_in());
        $messageinfo = Message::getMessageInfo($messages);
        View::make('message/index.html', array('messageinfo' => $messageinfo, 
                                               'user' => self::get_user_logged_in(),
                                               'admin' => self::admin_logged_in()));
    }
    
    public static function followed() {
        $user = self::get_user_logged_in();
        if (!$user) {
            Redirect::to("/");
        }
        $messages = Message::followed($user->id);
        $messageinfo = Message::getMessageInfo($messages);
        View::make('message/index.html', array('messageinfo' => $messageinfo,
                                               'user' => $user,
                                               'admin' => self::admin_logged_in()));
    }
    
    public static function userindex($userid) {
        $user = User::find($userid);
        $user_logged_in = self::get_user_logged_in();
        $userinfo = User::getUserinfo($user); // nää vois pakata myös samaan
        $messages = Message::findByUser($userid, $user_logged_in != null);
        $messageinfo = Message::getMessageInfo($messages); 
        $own_page = false;
        $followed = null;
        if ($user_logged_in) {
            $own_page = self::get_user_logged_in()->id == $user->id;
        }
        if (!$own_page && $user_logged_in) {
            $followed = $user_logged_in->follows($userid);
        }
        View::make('user/index.html', array('user' => $user, 'messageinfo' => $messageinfo,
                                            'userinfo' => $userinfo, 'logged_in' => $user_logged_in != null,
                                            'own_page' => $own_page, 'followed' => $followed, 
                                            'admin' => self::admin_logged_in()));
    }

    public function tagindex($text) {
        $tag = Tag::findByText($text);
        
        if ($tag) {
            $messages = Message::findByTag($tag->id, self::check_logged_in());
            $messageinfo = Message::getMessageInfo($messages);

            View::make('tag/index.html', array('messageinfo' => $messageinfo, 'tag' => $tag,
                                               'admin' => self::admin_logged_in()));
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
        $logged_in = false;
        if (self::check_logged_in()) {
            $logged_in = self::get_user_logged_in()->id == $message->userid;
        }
        if (count($errors) == 0 && $logged_in) {
            $message->save();
        }
        Redirect::to('/user/'.$message->userid);
    }
    
    public static function remove($id) {
        if (self::admin_logged_in()) {
            Message::remove($id);
        }
        Redirect::to("/");
    }
    
    public static function search() {
        // TODO
        Redirect::to('/');
    }

}
