<?php
class Message {

    // Static REPOSITORY methods
    static public function GetAllMessageForUser (mysqli $conn, $reciever_id) {
        $sql = "SELECT * FROM Messages WHERE reciever_id = $reciever_id ORDER BY id DESC";
        $result = $conn->query($sql);
        $toReturn = [];
        if ($result != false) {
            foreach($result as $row) {
                $newMessage = new Message();
                $newMessage->id = $row['id'];
                $newMessage->massage_text = $row['massage_text'];
                $newMessage->reciever_id = $row['reciever_id'];
                $newMessage->sender_id = $row['sender_id'];
                $newMessage->is_read = $row['is_read'];
                $newMessage->creation_date = $row['creation_date'];

                $toReturn[] = $newMessage;
            }
        }
        return $toReturn;
    }

    static public function GetAllSendMessage (mysqli $conn, $sender_id) {
        $sql = "SELECT * FROM Messages WHERE sender_id = $sender_id ORDER BY id DESC";
        $result = $conn->query($sql);
        $toReturn = [];
        if ($result != false) {
            foreach($result as $row) {
                $newMessage = new Message();
                $newMessage->id = $row['id'];
                $newMessage->massage_text = $row['massage_text'];
                $newMessage->reciever_id = $row['reciever_id'];
                $newMessage->sender_id = $row['sender_id'];
                $newMessage->is_read = $row['is_read'];
                $newMessage->creation_date = $row['creation_date'];

                $toReturn[] = $newMessage;
            }
        }
        return $toReturn;
    }

    static public function ShowMyMessage (mysqli $conn, array $messageTable) {
        if (!empty($messageTable)) {
            echo "<table class='table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Nadawca</th><th>Wiadomość</th><th>Data</th><th>Czyt.</th>";
            echo "</tr>";
            echo "<tbody>";
            for ($i=0; $i<count($messageTable); $i++) {
                $senderUser = new User();
                $senderUser->loadFromDB($conn, $messageTable[$i]->getSenderId());
                $message = substr($messageTable[$i]->getMassageText(),0,30);
                echo "<tr>";
                echo "<td><a href='showOneUser.php?idToShow={$senderUser->getId()}'>{$senderUser->getLogin()}</a></td>";
                echo "<td><a href='showMessage.php?idToShow={$messageTable[$i]->getId()}'>$message...</a></td>";
                echo "<td>{$messageTable[$i]->getCreationDate()}</td>";
                if ($messageTable[$i]->getIsRead() == 0) {
                    echo "<td><b style='color: red'>NIE</b></td>";
                } else {
                    echo "<td style='color: green'>TAK</td>";
                }
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</thead>";
            echo "</table>";
        } else {
            echo "<p><div class='alert alert-info'>Nie masz wiadomości</div></p>";
        }
    }

    static public function ShowSendMessage (mysqli $conn, array $messageTable) {
        if (!empty($messageTable)) {
            echo "<table class='table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Odbiorca</th><th>Wiadomość</th><th>Data</th><th>Czyt.</th>";
            echo "</tr>";
            echo "<tbody>";
            for ($i=0; $i<count($messageTable); $i++) {
                $recieverUser = new User();
                $recieverUser->loadFromDB($conn, $messageTable[$i]->getRecieverId());
                $message = substr($messageTable[$i]->getMassageText(),0,30);
                echo "<tr>";
                echo "<td><a href='showOneUser.php?idToShow={$recieverUser->getId()}'>{$recieverUser->getLogin()}</a></td>";
                echo "<td>$message...</td>";
                echo "<td>{$messageTable[$i]->getCreationDate()}</td>";
                if ($messageTable[$i]->getIsRead() == 0) {
                    echo "<td><b style='color: red'>NIE</b></td>";
                } else {
                    echo "<td style='color: green'>TAK</td>";
                }
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</thead>";
            echo "</table>";
        } else {
            echo "<p><div class='alert alert-info'>Nie wysłałeś jeszcze żadnej wiadomości</div></p>";
        }
    }

    //Attributes
    private $id;
    private $massage_text;
    private $reciever_id;
    private $sender_id;
    private $is_read;
    private $creation_date;

    //Functions
    public function __construct() {
        $this->id = -1;
        $this->massage_text = '';
        $this->reciever_id = -1;
        $this->sender_id = -1;
        $this->is_read = null;
        $this->creation_date = null;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getMassageText() {
        return $this->massage_text;
    }

    public function setMassageText($newMassageText) {
        $this->massage_text = $newMassageText;
    }

    public function getRecieverId() {
        return $this->reciever_id;
    }

    public function setRecieverId($newRecieverId) {
        $this->reciever_id = $newRecieverId;
    }

    public function getSenderId() {
        return $this->sender_id;
    }

    public function setSenderId($newSenderId) {
        $this->sender_id = $newSenderId;
    }

    public function getIsRead() {
        return $this->is_read;
    }

    public function setIsRead($is_read) {
        $this->is_read = $is_read;
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
            $sql = "INSERT INTO Messages (sender_id, reciever_id, massage_text, creation_date, is_read)
                    VALUES ({$this->sender_id},{$this->reciever_id},'{$this->massage_text}','{$this->creation_date}','{$this->is_read}')";
            $result = $conn->query($sql);
            if ($result == TRUE) {
                $this->id = $conn->insert_id;
                return true;
            } else {
                return false;
            }
        } else {
            //update row in DB
            $sql = "UPDATE Messages SET is_read = {$this->is_read} WHERE id={$this->id}";
            $result = $conn->query($sql);
            return $result;
        }
    }

    public function loadOneMessageFromDB (mysqli $conn, $id) {
        $sql = "SELECT * FROM Messages WHERE id = $id";
        $result = $conn->query($sql);
        if ($result != FALSE) {
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $this->id = $row['id'];
                $this->sender_id = $row['sender_id'];
                $this->reciever_id = $row['reciever_id'];
                $this->massage_text = $row['massage_text'];
                $this->creation_date = $row['creation_date'];
                $this->is_read = $row['is_read'];
                return true;
            }
        }
        return false;
    }
}