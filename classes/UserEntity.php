<?php
class UserEntity{
    protected $id;
    protected $firstname;
    protected $lastname;
    protected $password;
    protected $email;

    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['id'])) {
            $this->id = $data['id'];
        }
        $this->firstname = $data['firstname'];
        $this->lastname = $data['lastname'];
        $this->password = $data['password'];
        $this->email = $data['email'];
    }
    public function __toString()
    {
        return $this->email." ".$this->firstname;
    }
    public function getId() {
        return $this->id;
    }
    public function getFirstName() {
        return $this->firstname;
    }
    public function getLastName() {
        return $this->lastname;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getPassword() {
        return $this->password;
    }

}