<?php

class Post {
    // Static REPOSITORY methods
    static public function GetAllPosts (mysqli $conn) {
        $sql = "SELECT * FROM Posts ORDER BY id DESC";
        $result = $conn->query($sql);
        $toReturn = [];
        if ($result != false) {
            foreach($result as $row) {
                $newPost = new Post();
                $newPost->id = $row['id'];
                $newPost->user_id = $row['user_id'];
                $newPost->post_text = $row['post_text'];
                $newPost->creation_date = $row['creation_date'];

                $toReturn[] = $newPost;
            }
        }
        return $toReturn;
    }

    static public function GetPostByID (mysqli $conn, $id) {
        $toReturn = null;
        $sql = "SELECT * FROM Posts WHERE id = $id";
        $result = $conn->query($sql);
        if ($result != false) {
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $post = new Post();
                $post->id = $row['id'];
                $post->user_id = $row['user_id'];
                $post->post_text = $row['post_text'];
                $post->creation_date = $row['creation_date'];

                $toReturn = $post;
            }
        }
        return $toReturn;
    }

    //Attributes
    private $id;
    private $user_id;
    private $post_text;
    private $creation_date;

    //Functions
    public function __construct() {
        $this->id = -1;
        $this->user_id = -1;
        $this->post_text = '';
        $this->creation_date = null;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($newUserId) {
        $this->user_id = $newUserId;
    }

    public function getPostText() {
        return $this->post_text;
    }

    public function setPostText($newPostText) {
        $this->post_text = $newPostText;
    }

    public function getCreationDate() {
        return $this->creation_date;
    }

    public function setCreationDate($creation_date) {
        $this->creation_date = $creation_date;
    }

    public function saveToDB(mysqli $conn) {
        $this->creation_date = date("Y-m-d H:i:s");
        if ($this->id == -1) {
            //insert new row to DB
            $sql = "INSERT INTO Posts (user_id, post_text, creation_date)
                    VALUES ({$this->user_id},'{$this->post_text}','{$this->creation_date}')";
            $result = $conn->query($sql);
            if ($result == TRUE) {
                $this->id = $conn->insert_id;
                return true;
            } else {
                return false;
            }
        } else {
            //update row in DB
            $sql = "UPDATE Posts SET post_text = '{$this->post_text}',
                                     creation_date = '{$this->creation_date}'
                                     WHERE id={$this->id}";
            $result = $conn->query($sql);
            return $result;
        }
    }

    public function loadAllCommentsForPost (mysqli $conn, $id) {
        $postComments = [];
        $sql = "SELECT * FROM Comments WHERE post_id = $id AND is_active = true ORDER BY id ASC";
        $result = $conn->query($sql);
        if ($result != false) {
            if ($result->num_rows > 0) {
                foreach ($result as $row) {
                    $comment = new Comment();
                    $comment->setId($row['id']);
                    $comment->setPostId($row['post_id']);
                    $comment->setUserId($row['user_id']);
                    $comment->setCommentText($row['comment_text']);
                    $comment->setCreationDate($row['creation_date']);
                    $comment->setIsActive($row['is_active']);
                    $postComments[] = $comment;
                }
            } else {
                return 0;
            }
        }
        return $postComments;
    }

    public function showCommentsForPost (mysqli $conn, Post $post, User $loggerUser) {
        $user = new User();
        $commentsTable = $post->loadAllCommentsForPost($conn,$post->getId());
        $count = ($commentsTable != 0) ? count($commentsTable) : 0;
        if ($count != 0) {
            for ($i=0; $i<$count; $i++) {
                echo "<div class='panel-group' id='accordion'>";
                echo "<div class='panel panel-default'>";
                echo "<div class='panel-heading'>";
                echo "<h4 class='panel-title'>";
                echo "<a data-toggle='collapse' data-parent='#accordion' href='#collapse_$i'>{$commentsTable[$i]->getCommentText()}</a>";
                echo "</h4>";
                echo "</div>";
                echo "<div id='collapse_$i' class='panel-collapse collapse'>";
                echo "<div class='panel-body'>";
                $user->loadFromDB($conn,$commentsTable[$i]->getUserId());
                echo "Autor: {$user->getLogin()}<br>";
                echo "Data utworzenia: {$commentsTable[$i]->getCreationDate()}<br>";
                if ($loggerUser->getId() == $commentsTable[$i]->getUserId()) {
                    echo "<a href='?id={$post->getId()}&delete={$commentsTable[$i]->getId()}'><button class='btn btn-danger btn-xs'>Usuń</button></a>";
                }
                echo "</div>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p><div class='alert alert-warning'>Ten twitt nie ma jeszcze komentarzy</div></p>";
        }
    }
}
?>