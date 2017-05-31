<?php

namespace ApiBundle\Controller;

use EntityBundle\Entity\UserProfile;
use EntityBundle\Entity\User;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;



class UserController extends Controller
{   
    public function loginAction($username, $password){

        $status = $this->get('UserService')->login($username, $password);

        $response = new Response();
        
        $serializer = $this->get('serializer');
        $json = $serializer->serialize(
            $status,
            'json', array('groups' => array('group1'))
        );
        
        $response->setContent($json);

        return $response;
    }

    public function registrationAction($username, $phone, $plainpassword, $firstname, $lastname, $passport, $country, $city, $address, $company, $language){

        $status = $this->get('UserService')->registration($username, $phone, $plainpassword, $firstname, $lastname, $passport, $country, $city, $address, $company, $language);

        $serializer = $this->get('serializer');

        $json = $serializer->serialize(
            $status,
            'json', array('groups' => array('group1'))
        );

        $response = new Response();

		$response->setContent($json);    	

        return $response;
    }

    public function editUserAction($username, $phone, $firstname, $lastname, $passport, $country, $city, $address, $company, $language){

        $status = $this->get('UserService')->editUser($username, $phone, $firstname, $lastname, $passport, $country, $city, $address, $company, $language);

        $serializer = $this->get('serializer');

        $json = $serializer->serialize(
            $status,
            'json', array('groups' => array('group1'))
        );

        $response = new Response();

        $response->setContent($json);       

        return $response;
    }

    public function listOfUsersAction(Request $request){

        $users = $this->get('doctrine.orm.entity_manager')->getRepository('EntityBundle:User')->Users();
       
        $serializer = $this->get('serializer');

        $json = $serializer->serialize(
            $users,
            'json', array('groups' => array('group1'))
        );

        $response = new Response();
        $response->setContent($json);

        return $response;
    }

    public function sendAction(Request $request){


        $json = $request->request->get("id");

        $array = json_decode($json, true);

        $status = $this->get('UserService')->saveFile($array);
        
        $response = new Response();

        $response->setContent("Message Successfully Sent");  
        
        return $response;
    }

    public function messagesAction(Request $request, $token){

        $user = $this->get('UserService')->getUserByToken($token);

        $messages = $this->get('doctrine.orm.entity_manager')->getRepository('EntityBundle:Message')->userMessages($user->getId());

        $serializer = $this->get('serializer');

        $json = $serializer->serialize(
            $messages,
            'json', array('groups' => array('group1'))
        );

        $response = new Response();
        $response->setContent($json);
        return $response;
    }

    public function sentMessagesAction($token){
        $user = $this->get('UserService')->getUserByToken($token);

        $messages = $this->get('doctrine.orm.entity_manager')->getRepository('EntityBundle:Message')->sentMessages($user->getId());

        $serializer = $this->get('serializer');

        $json = $serializer->serialize(
            $messages,
            'json', array('groups' => array('group1'))
        );

        $response = new Response();
        $response->setContent($json);
        return $response;
    }

    public function filesAction($message_id, $token){

        $user = $this->get('UserService')->getUserByToken($token);

        if($user){
            $files = $this->get('UserService')->getFileById($message_id);

            $serializer = $this->get('serializer');

            $json = $serializer->serialize(
                $files,
                'json', array('groups' => array('group1'))
            );

            $response = new Response();
            $response->setContent($json);
            return $response;
        }else{
            $response = new Response();
            $response->setContent("empty");
            return $response;
        }
    }

    public function getUserAction($token){

        $user = $this->get('UserService')->getUserByToken($token);

        $serializer = $this->get('serializer');

        $json = $serializer->serialize(
            $user,
            'json', array('groups' => array('group1'))
        );

        $response = new Response();
        $response->setContent($json);
        return $response;

    }

    public function deleteUserAction($user_id, $token){

        $user = $this->get('UserService')->getUserByToken($token);

        if($user->getRole() == "ADMIN"){
              $status = $this->get('UserService')->deleteUser($user_id);
        }
        
        $serializer = $this->get('serializer');

        $json = $serializer->serialize(
            $status,
            'json', array('groups' => array('group1'))
        );

        $response = new Response();
        $response->setContent($json);
        return $response;
    }

    public function deleteMessageAction($message_id, $token){

       $user = $this->get('UserService')->getUserByToken($token);
      
        if($user){
            $status = $this->get('UserService')->deleteMessage($message_id);
        }

        $serializer = $this->get('serializer');

        $json = $serializer->serialize(
            $status,
            'json', array('groups' => array('group1'))
        );
        
        $response = new Response();
        $response->setContent($json);
        return $response;
    }

    public function setStatusAction($message_id, $token, $from_to){
        $user = $this->get('UserService')->getUserByToken($token);
      
        if($user){
            $status = $this->get('UserService')->setStatus($message_id, $from_to, $token);
        }
        
        $serializer = $this->get('serializer');
        
        $json = $serializer->serialize(
            $status,
            'json', array('groups' => array('group1'))
        );
        
        $response = new Response();
        $response->setContent($json);
        return $response;
    }

    public function getForumAction($token){
        $user = $this->get('UserService')->getUserByToken($token);
      
        if($user){
            $data = $this->get('doctrine.orm.entity_manager')->getRepository('EntityBundle:Forum')->forum();
        }

        $serializer = $this->get('serializer');
        
        $json = $serializer->serialize(
            $data,
            'json', array('groups' => array('group1'))
        );
        
        $response = new Response();
        $response->setContent($json);
        return $response;
    }

    public function postForumAction(Request $request, $token){
        $user = $this->get('UserService')->getUserByToken($token);
      
        if($user){
            $message = $request->request->get("message");

            $status = $this->get('UserService')->postForum($user->getId(), $request->request->get("message"));

        }

        $serializer = $this->get('serializer');
        
        $json = $serializer->serialize(
            $status,
            'json', array('groups' => array('group1'))
        );
        
        $response = new Response();
        $response->setContent($json);
        return $response;
    }

    public function firebaseAction(Request $request, $token1){
        $token = $request->request->get("token");
        $status = $this->get('UserService')->setToken($token1); 
    }
}
