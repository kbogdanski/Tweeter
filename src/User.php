<?php

class User {
    // Static REPOSITORY methods
    static public function GetAllUsers(mysqli $conn) {
        $sql = "SELECT * FROM Users";
        $result = $conn->query($sql);
        $toReturn = [];
        if ($result != false) {
            foreach($result as $row) {
                $newUser = new User();
                $newUser->id = $row['id'];
                $newUser->login = $row['login'];
                $newUser->hassedPassword = $row['hassed_password'];
                $newUser->description = $row['user_description'];
                $newUser->isActive = $row['is_active'];
                $newUser->name = $row['name'];
                $newUser->surname = $row['surname'];
                $newUser->email = $row['email'];
                $newUser->avatar = $row['avatar'];

                $toReturn[] = $newUser;
            }
        }
        return $toReturn;
    }

    static public function LogIn(mysqli $conn, $login, $password) {
        $toReturn = null;
        $sql = "SELECT * FROM Users WHERE login='{$login}'";
        $result = $conn->query($sql);
        if ($result != false) {
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $loggedUser = new User();
                $loggedUser->id = $row['id'];
                $loggedUser->login = $row['login'];
                $loggedUser->hassedPassword = $row['hassed_password'];
                $loggedUser->description = $row['user_description'];
                $loggedUser->isActive = $row['is_active'];
                $loggedUser->name = $row['name'];
                $loggedUser->surname = $row['surname'];
                $loggedUser->email = $row['email'];
                $loggedUser->avatar = $row['avatar'];
                if ($loggedUser->verifyPassword($password)) {
                    $toReturn = $loggedUser;
                }
            }
        }
        return $toReturn;
    }

    //Attributes
    private $id;
    private $login;
    private $hassedPassword;
    private $description;
    private $isActive;
    private $name;
    private $surname;
    private $email;
    private $avatar;

    //Functions
    public function __construct() {
        $this->id = -1;
        $this->login = '';
        $this->hassedPassword = '';
        $this->description = '';
        $this->isActive = 1;
        $this->name = '';
        $this->surname = '';
        $this->email = '';
        $this->avatar = 'brak.jpg';
    }

    public function getId() {
        return $this->id;
    }

    public function setLogin($newLogin) {
        $this->login = $newLogin;
    }

    public function checkLogin (mysqli $conn, $newLogin) {
        $sql = "SELECT * FROM Users WHERE login='$newLogin'";
        $result = $conn->query($sql);
        if ($result != false) {
            if ($result->num_rows == 0) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function getLogin() {
        return $this->login;
    }

    public function setPassword($newPassword1, $newPassword2) {
        if ($newPassword1 != $newPassword2) {
            return false;
        }
        $hassedPassword = password_hash($newPassword1, PASSWORD_BCRYPT);
        $this->hassedPassword = $hassedPassword;
        return true;
    }

    public function setDescription($newDescription) {
        $this->description = $newDescription;
    }

    public function getDescription() {
        return $this->description;
    }

    public function deactivate() {
        $this->isActive = 0;
    }

    public function activate() {
        $this->isActive = 1;
    }

    public function isUserActive() {
        return $this->isActive === 1;
    }

    public function setName($newName) {
        $this->name = $newName;
    }

    public function getName() {
        return $this->name;
    }

    public function setSurname($newSurname) {
        $this->surname = $newSurname;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function setEmail($newEmail) {
        $this->email = $newEmail;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setAvatar($newAvatar) {
        $this->avatar = $newAvatar;
    }

    public function getAvatar() {
        return $this->avatar;
    }

    public function saveToDB(mysqli $conn) {
        if ($this->id == -1) {
            //insert new row to DB
            $sql = "INSERT INTO Users (login, hassed_password, user_description, is_active, name, surname, email, avatar)
                    VALUES ('{$this->login}','{$this->hassedPassword}','{$this->description}',{$this->isActive},
                    '{$this->name}','{$this->surname}','{$this->email}','{$this->avatar}')";
            $result = $conn->query($sql);
            if ($result == TRUE) {
                $this->id = $conn->insert_id;
                return true;
            } else {
                return false;
            }
        } else {
            //update row in DB
            $sql = "UPDATE Users SET login = '{$this->login}',
                                     hassed_password='{$this->hassedPassword}',
                                     user_description='{$this->description}',
                                     is_active={$this->isActive},
                                     name = '{$this->name}',
                                     surname = '{$this->surname}',
                                     email = '{$this->email}',
                                     avatar = '{$this->avatar}'
                                     WHERE id={$this->id}";
            $result = $conn->query($sql);
            return $result;
        }
    }

    public function loadFromDB(mysqli $conn, $id) {
        $sql = "SELECT * FROM Users WHERE id = $id";
        $result = $conn->query($sql);
        if ($result != FALSE) {
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $this->id = $row['id'];
                $this->login = $row['login'];
                $this->hassedPassword = $row['hassed_password'];
                $this->description = $row['user_description'];
                $this->isActive = $row['is_active'];
                $this->name = $row['name'];
                $this->surname = $row['surname'];
                $this->email = $row['email'];
                $this->avatar = $row['avatar'];
                return true;
            }
        }
        return false;
    }

    public function verifyPassword($password) {
        return password_verify($password, $this->hassedPassword);
    }

    public function loadUserPosts (mysqli $conn, $id) {
        $userPosts = [];
        $sql = "SELECT * FROM Posts WHERE user_id = $id ORDER BY id DESC ";
        $result = $conn->query($sql);
        if ($result != false) {
            if ($result->num_rows > 0) {
                foreach ($result as $row) {
                    $post = new Post();
                    $post->setId($row['id']);
                    $post->setUserId($row['user_id']);
                    $post->setPostText($row['post_text']);
                    $post->setCreationDate($row['creation_date']);
                    $userPosts[] = $post;
                }
            } else {
                return false;
            }
        }
        return $userPosts;
    }

    public function showUserPosts (mysqli $conn, array $tablePosts, $quantity = null) {
        if ($quantity > count($tablePosts) || $quantity == null) {
            $quantity = count($tablePosts);
        }
        for ($i=0; $i<$quantity; $i++) {
            $tableComment = $tablePosts[$i]->loadAllCommentsForPost($conn,$tablePosts[$i]->getId());
            $quantityComment = ($tableComment == 0) ? 0 : count($tableComment);
            echo "<div class='panel panel-primary'>";
                echo "<div class='panel-heading'>";
                echo "<a href='showPost.php?id={$tablePosts[$i]->getId()}'><img src='img/twitt.jpg' class='img-circle'> </a>";
                echo "{$tablePosts[$i]->getPostText()}";
                echo "</div>";
                echo "<div class='panel-body'>
                            <span style='float: left'>Data utworzenia: {$tablePosts[$i]->getCreationDate()}</span>
                            <a href='showPost.php?id={$tablePosts[$i]->getId()}'><span style='float: right'>Komentarze ({$quantityComment})</span><a>
                     </div>";
            echo "</div>";
        }
    }

    //Counting functions
    public function countUserPosts (mysqli $conn, $id) {
        $countUserPost = null;
        $sql = "SELECT count(user_id) FROM Posts WHERE user_id = $id";
        $result = $conn->query($sql);
        if ($result != false) {
            foreach ($result as $row) {
                $countUserPost = $row['count(user_id)'];
            }
        }
        return $countUserPost;
    }

    public function countUserComments (mysqli $conn, $id) {
        $countUserComments = null;
        $sql = "SELECT count(user_id) FROM Comments WHERE user_id = $id";
        $result = $conn->query($sql);
        if ($result != false) {
            foreach ($result as $row) {
                $countUserComments = $row['count(user_id)'];
            }
        }
        return $countUserComments;
    }

    public function countNoReadUserMessages (mysqli $conn, $id) {
        $countUserMessages = 0;
        $sql = "SELECT count(reciever_id) FROM Messages WHERE reciever_id = $id AND is_read = FALSE";
        $result = $conn->query($sql);
        if ($result != false) {
            foreach ($result as $row) {
                $countUserMessages = $row['count(reciever_id)'];
            }
        }
        return $countUserMessages;
    }

    public function countSendMessages (mysqli $conn, $id) {
        $countSendMessages = 0;
        $sql = "SELECT count(sender_id) FROM Messages WHERE sender_id = $id";
        $result = $conn->query($sql);
        if ($result != false) {
            foreach ($result as $row) {
                $countSendMessages = $row['count(sender_id)'];
            }
        }
        return $countSendMessages;
    }

    public function countRecieverMessages (mysqli $conn, $id) {
        $countRecieverMessages = 0;
        $sql = "SELECT count(reciever_id) FROM Messages WHERE reciever_id = $id";
        $result = $conn->query($sql);
        if ($result != false) {
            foreach ($result as $row) {
                $countRecieverMessages = $row['count(reciever_id)'];
            }
        }
        return $countRecieverMessages;
    }
}
?>