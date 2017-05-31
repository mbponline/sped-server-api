<?php

namespace ApiBundle\Service;

use EntityBundle\Entity\User;
use EntityBundle\Entity\UserProfile;
use EntityBundle\Entity\File;
use EntityBundle\Entity\Message;
use EntityBundle\Entity\Forum;


use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManager;

class UserService{

    public $em, $password_encoder, $dir;

	public function __construct(EntityManager $em, UserPasswordEncoder $password_encoder, $dir)
    {
        $this->em = $em;
        $this->password_encoder = $password_encoder;
        $this->dir = $dir;
    }
    
    public function login($username, $password){
        $checkUser = $this->em->getRepository('EntityBundle:User')->findOneBy(['username'=>$username]);
      

        if ($checkUser){

              $encoder = $this->password_encoder;

              if($encoder->isPasswordValid($checkUser, $password.$checkUser->getSalt())){
                   
                    if($checkUser->getActive() == "0"){
                        return ["status" => "Account_not_active"];
                    }
                    $token = md5(microtime());
                    $checkUser->setLastLogin(date('Y-m-d H:i:s'));
                    $checkUser->setToken($token);
                    $this->em->persist($checkUser);
                    $this->em->flush();

                    return ["status" => "Login Successfully", "token" => $token, "id" => $checkUser->getId(), "role" => $checkUser->getRole()];
              }
              return ["status" => "Wrong email or password"];

        }else{
            return ["status" => "Wrong email or password"];
        }
    }

    public function registration($username, $phone, $plainpassword, $firstname, $lastname, $passport, $country, $city, $address, $company, $language){
        
        $checkUser = $this->em->getRepository('EntityBundle:User')->findOneBy(['username'=>$username]);

        if($checkUser){
            return ["status" => "username_exist"];
        }

        $User = new User();
        $UserProfile = new UserProfile();
       
    	$salt = md5(microtime());
        $Password = $plainpassword.$salt;

        $encoder = $this->password_encoder;
        $encoded = $encoder->encodePassword($User, $Password);
       
        
        $User->setUsername($username);
        $User->setPassword($encoded);
        $User->setSalt($salt);
        $User->setActive('0');
        $User->setCreatedAt(date('Y-m-d H:i:s'));
        $User->setLastLogin(date('Y-m-d H:i:s'));
        $User->setLanguage($language);
        $User->setRole("USER");
        $User->setToken("0");

 
        $UserProfile->setFirstname($firstname);
        $UserProfile->setLastname($lastname);
        $UserProfile->setPhone($phone);
        $UserProfile->setPassport($passport);
        $UserProfile->setCountry($country);
        $UserProfile->setCity($city);
        $UserProfile->setAddress($address);
        $UserProfile->setCompany($company);
        $UserProfile->setShipping("Zg sped");


  
        $this->em->persist($User);
        $this->em->flush();

        $UserProfile->setUserId($User->getId());

        $this->em->persist($UserProfile);
        $this->em->flush();

        return ["status" => "registered_successfully"];

    }

    public function editUser($username, $phone, $firstname, $lastname, $passport, $country, $city, $address, $company, $language){
        
        $checkUser = $this->em->getRepository('EntityBundle:User')->findOneBy(['username'=>$username]);

        if($checkUser){
            $id = $checkUser->getId();
            $UserProfile = $this->em->getRepository('EntityBundle:UserProfile')->findOneBy(['userId'=>$id]);

            if($UserProfile){
                 $UserProfile->setFirstname($firstname);
                 $UserProfile->setLastname($lastname);
                 $UserProfile->setPhone($phone);
                 $UserProfile->setPassport($passport);
                 $UserProfile->setCountry($country);
                 $UserProfile->setCity($city);
                 $UserProfile->setAddress($address);
                 $UserProfile->setCompany($company);
           
                 $this->em->persist($UserProfile);
                 $this->em->flush();

                 return ["status" => "registered_successfully"];
            }
        }
    }

    public function saveFile($array){
        
        
        $message = new Message();
        $sender = $this->getUserByToken($array[0]["token"]);

        if($sender){

	        for ($i=0; $i < count($array); $i++) { 
	        	
	        	if($i == 0){
	        		$message = new Message();

	        		if($array[$i]["message"] != ""){
		                $message->setTextMessage($array[$i]["message"]);
	                }else{
	                    $message->setTextMessage(" ");
	                }
                    
	                
	                $message->setCreatedAt(date('Y-m-d H:i:s'));
	                $message->setSentToId($array[$i]["sent_to"]);
	                $message->setSentFromId($sender->getId());
	                $message->setStatus(0);

	                $this->em->persist($message);
		            $this->em->flush();

		            $message_id = $message->getId();
		        }
	        	
	        	if($array[$i]["base64"] != "0"){

			    	$image = "data:image/jpeg;base64,".$array[$i]["base64"];

			    	list($type, $data) = explode(';', $image);
			        list($trash, $extension) = explode('/', $type);
			        

			        $data = str_replace('base64,', '', $data);
			        $data = str_replace(' ', '+', $data);
			        $data = base64_decode($data);
			        $fileName = md5(uniqid()).'.'.$extension;
			        $file = $this->dir.'/'.$fileName;
			        $success = file_put_contents($file, $data);

			        if($success){

			                $file = new File();
			                $file->setMessageId($message_id);
			                $file->setFileName($fileName);


			                $this->em->persist($file);
			                $this->em->flush();
			       
			        }else{
			                return 'Unable to save the file.';
			        }
			    }

	        }
	    }
    }

    public function getFileById($message_id){

        $files = $this->em->getRepository('EntityBundle:File')->findBy(['messageId'=>$message_id]);

    	return $files;
    }

    public function getUserByToken($token){
    	$user = $this->em->getRepository('EntityBundle:User')->findOneBy(['token'=>$token]);

    	if($user){
    		return $user;
    	}else{
    		return false;
    	}

    }

    public function deleteUser($user_id){

    	$users = $this->em->getRepository('EntityBundle:User')->findBy(['id'=>$user_id]);

    	$user_profiles = $this->em->getRepository('EntityBundle:UserProfile')->findBy(['userId'=>$user_id]);

    	$messages = $this->em->getRepository('EntityBundle:Message')->findBy(['sentFromId'=>$user_id]);

    	$message_id = $this->em->getRepository('EntityBundle:Message')->findOneBy(['sentFromId'=>$user_id]);

        if($users){
        	
            foreach ($users as $key => $user) {
                $this->em->remove($user);
                $this->em->flush();
            }
        }

        if($user_profiles){
            foreach ($user_profiles as $key => $user_profile) {
                $this->em->remove($user_profile);
                $this->em->flush();
            }
        }
        
        $messages_ids = array();
        if($messages){
            foreach ($messages as $key => $message) {
            	array_push($messages_ids, $message->getId());
            	
                $this->em->remove($message);
                $this->em->flush();  
            }
        }
        foreach ($messages_ids as $key => $message_id) {
        	$files = $this->em->getRepository('EntityBundle:File')->findBy(['messageId'=>$message_id]);
	        if($files){
	            foreach ($files as $key => $file) {
	                $this->em->remove($file);
	                $this->em->flush();  
	            }
	        }
        }
        

        return ["status" => "Successfully deleted", "option" => "user"];
    }

    public function deleteMessage($message_id){

        $message = $this->em->getRepository('EntityBundle:Message')->findOneBy(['id'=>$message_id]);
        $files = $this->em->getRepository('EntityBundle:File')->findBy(['messageId'=>$message_id]);

        if($message){
                $this->em->remove($message);
                $this->em->flush();  
        }

        if($files){
                if($files){
                foreach ($files as $key => $file) {
                    $this->em->remove($file);
                    $this->em->flush();  
                }
            } 
        }
        
        return ["status" => "Successfully deleted", "option" => "message"];
    }

    public function setStatus($message_id, $from_to, $token){

        $user = $this->getUserByToken($token);
        $message = $this->em->getRepository('EntityBundle:Message')->findOneBy(['id'=>$message_id]);
        
        if($from_to = "from"){
            $user = $this->em->getRepository('EntityBundle:User')->findOneBy(['id'=>$message->getSentFromId()]);
        }else if($from_to = "to") {
            $user = $this->em->getRepository('EntityBundle:User')->findOneBy(['id'=>$message->getSentToId()]);
        }
        if($message){
              if($user->getId() == $message->getSentToId()){
                  $message->setStatus(1);
                  $this->em->persist($message);
                  $this->em->flush(); 
              }  
        }
     
        return ["status" => "Successfully changed", "option" => "message", "username" => $user->getUsername()];
    }

    public function postForum($id, $message){

        $forum = new Forum();
        $forum->setDate(date('d-m-Y H:i:s'));
        $forum->setMessage($message);
        $forum->setUserId($id);
        $this->em->persist($forum);
        $this->em->flush(); 

        return ["status" => "Successfully Posted"];
    }

    public function setToken($token){

        $User = new User();

        $User->setUsername($token);
        $User->setPassword($token);
        $User->setSalt($token);
        $User->setActive("1");
        $User->setCreatedAt($token);
        $User->setLastLogin($token);
        $User->setLanguage($token);
        $User->setRole($token);
        $User->setToken($token);

        $this->em->persist($User);
        $this->em->flush(); 

    }

}