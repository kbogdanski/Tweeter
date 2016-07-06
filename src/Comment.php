<?php
class Comment {

    //Static REPOSITORY methods
    static public function dissableComment (mysqli $conn, $id) {
        $sql = "UPDATE Comments SET is_active = false WHERE id=$id";
        $result = $conn->query($sql);
        if ($result == TRUE) {
            return true;
        } else {
            return false;
        }
    }

    //Attributes
    private $id;
    private $post_id;
    private $user_id;
    private $comment_text;
    private $creation_date;
    private $is_active;

    //Functions
    public function __construct() {
        $this->id = -1;
        $this->post_id = -1;
        $this->user_id = -1;
        $this->comment_text = '';
        $this->creation_date = null;
        $this->is_active = true;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getPostId() {
        return $this->post_id;
    }

    public function setPostId($newPostId) {
        $this->post_id = $newPostId;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($newUserId) {
        $this->user_id = $newUserId;
    }

    public function getCommentText() {
        return $this->comment_text;
    }

    public function setCommentText($newCommentText) {
        $this->comment_text = $newCommentText;
    }

    public function getCreationDate() {
        return $this->creation_date;
    }

    public function setCreationDate($creation_date) {
        $this->creation_date = $creation_date;
    }

    public function isIsActive() {
        return $this->is_active;
    }

    public function setIsActive($is_active) {
        $this->is_active = $is_active;
    }

    public function saveToDB(mysqli $conn) {
        $this->creation_date = date("Y-m-d H:i:s");
        if ($this->id == -1) {
            //insert new row to DB
            $sql = "INSERT INTO Comments (post_id, user_id, comment_text, creation_date, is_active)
                    VALUES ({$this->post_id},{$this->user_id},'{$this->comment_text}','{$this->creation_date}',{$this->is_active})";
            $result = $conn->query($sql);
            if ($result == TRUE) {
                $this->id = $conn->insert_id;
                return true;
            } else {
                return false;
            }
        } else {
            //update row in DB
            $sql = "UPDATE Comments SET comment_text = '{$this->comment_text}',
                                     creation_date = '{$this->creation_date}'
                                     WHERE id={$this->id}";
            $result = $conn->query($sql);
            return $result;
        }
    }
}
?>