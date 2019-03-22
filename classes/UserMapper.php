<?php

class UserMapper extends Mapper{
    public function getUsers(){
        $sql="select * from user";
        $stmt=$this->db->query($sql);
        $results = [];
        while($row=$stmt->fetch()){
            $results[]=new UserEntity($row);
        }
        return $results;
    }
    public function getUserByEmail($email){
        $sql="select * from user where email=:email";
        $stmt=$this->db->prepare($sql);

        $result = $stmt->execute(["email"=>$email]);
        
        if($result){
            $row = $stmt->fetch();
            if(is_array($row)){
                return new UserEntity($row);
            }
            else return null ;
            
        }
    }
    public function save(UserEntity $user)
    {
        $sql="insert into user(firstname,lastname,password,email)
        values(:firstname,:lastname,:password,:email";

        $stmt=$this->db->prepare($sql);

        $result = $stmt->execute([[
            "firstname"=>$user->getFirstName(),
            "lastname"=>$user->getLastName(),
            "password"=>password_hash($user->getPassword(),PASSWORD_DEFAULT),
            "email"=>$user->getEmail()]]);
        if(!$result){
            throw new Exception("Impossible de sauvegarder cet utilisateur");
        }else{
            return $this->db->lastInsertId();
        }
    }
}