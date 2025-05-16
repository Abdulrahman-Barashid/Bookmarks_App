<?php
class Bookmark
{
    private $id;
    private $title;
    private $link;
    private $dateAdded;
    private $done = false;
    private $dbConnection;
    private $dbTable = "bookmarks";

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    public function getDone()
    {
        return $this->done;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }

    public function setDone($done)
    {
        $this->done = $done;
    }

    // Create a new bookmark
    public function create()
    {
        $query = "INSERT INTO " . $this->dbTable . " (title, link, date_added) VALUES (:title, :link, NOW())";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":link", $this->link);
        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s", $stmt->error);
        return false;
    }

    public function readOne()
    {
        $query = "SELECT * FROM " . $this->dbTable . " WHERE id=:id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute() && $stmt->rowCount() == 1) {
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $this->id = $result->id;
            $this->title = $result->title;
            $this->link = $result->link;
            $this->dateAdded = $result->date_added;
            $this->done = 0;
            return true;
        }
        return false;
    }

    public function readAll()
    {
        $query = "SELECT * FROM bookmarks ORDER BY id ASC";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute();
        $bookmarks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookmarks[] = $row;
        }
        return $bookmarks;
    }

    public function update()
    {
        $query = "UPDATE bookmarks SET done = :done WHERE id = :id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':done', $this->done);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->dbTable . " WHERE id=:id";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(":id", $this->id);
        if ($stmt->execute() && $stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }
}
