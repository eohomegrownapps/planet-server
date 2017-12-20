<?php

class DB_Functions {
 
    private $db;
    private $link;
 
    function __construct() {
        require_once 'DB_Connect.php';
        $this->db = new DB_Connect();
        $this->link = $this->db->connect();
    }

    function __destruct() {
         
    }

    //'Front-End' functions
    public function addProject($ProjectJSON, $UserID){
        $ProjectObj = json_decode($ProjectJSON,true);
        if ($ProjectObj==NULL){
            return false;
        }
        $ProjectID = $ProjectObj["ProjectID"];
        if (!is_int($ProjectID)){
            return false;
        }
        $ProjectName = $ProjectObj["ProjectName"];
        if (!$this->validateStringRange($ProjectName,1,50)){
            return false;
        }
        $ProjectDescription = $ProjectObj["ProjectDescription"];
        if (!$this->validateStringRange($ProjectDescription,1,1000)){
            return false;
        }
        $ProjectSearchKeywords = $ProjectObj["ProjectSearchKeywords"];
        //TODO: Should we validate on *long* fields like this, ProjectData and ProjectImage?
        if (!is_string($ProjectSearchKeywords)){
            return false;
        }
        $ProjectData = $ProjectObj["ProjectData"];
        if (!$this->validateStringNonNull($ProjectData)){
            return false;
        }
        $ProjectImage = $ProjectObj["ProjectImage"];
        if (!$this->validateStringNonNull($ProjectImage)){
            return false;
        }
        $ProjectIsMusicBlocks = $ProjectObj["ProjectIsMusicBlocks"];
        if (!($ProjectIsMusicBlocks==0||$ProjectIsMusicBlocks==1)){
            return false;
        }
        $ProjectCreatorName = $ProjectObj["ProjectCreatorName"];
        if (!$this->validateStringRange($ProjectCreatorName,1,50)){
            return false;
        }
        $ProjectTags = $ProjectObj["ProjectTags"];
        if (!$this->validateArray($ProjectTags,5)){
            return false;
        }
        $this->addProjectToDB($ProjectID, $UserID, $ProjectName, $ProjectDescription, $ProjectSearchKeywords, $ProjectData, $ProjectImage, $ProjectIsMusicBlocks, $ProjectCreatorName);
        $this->addTagsToProject($ProjectID, $ProjectTags);
    }


    //Database-adding functions
    public function addProjectToDB($ProjectID, $UserID, $ProjectName, $ProjectDescription, $ProjectSearchKeywords, $ProjectData, $ProjectImage, $ProjectIsMusicBlocks, $ProjectCreatorName){
        $stmt = mysqli_prepare($this->link, "INSERT INTO `Projects` (`ProjectID`, `UserID`, `ProjectName`, `ProjectDescription`, `ProjectSearchKeywords`, `ProjectData`, `ProjectImage`, `ProjectIsMusicBlocks`, `ProjectCreatorName`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);");
        mysqli_stmt_bind_param($stmt, 'iisssssis', $ProjectID, $UserID, $ProjectName, $ProjectDescription, $ProjectSearchKeywords, $ProjectData, $ProjectImage, $ProjectIsMusicBlocks, $ProjectCreatorName);
        // execute prepared statement
        mysqli_stmt_execute($stmt);
    }

    public function addTagProjectPair($TagID, $ProjectID){
        $stmt = mysqli_prepare($this->link, "INSERT INTO `TagsToProjects` (`TagID`, `ProjectID`) VALUES (?, ?);");
        mysqli_stmt_bind_param($stmt, 'ii', $TagID, $ProjectID);
        // execute prepared statement
        mysqli_stmt_execute($stmt);
    }

    public function addTagsToProject($ProjectID, $ProjectTags){
        foreach ($ProjectTags as $tag) {
            if ($this->canUserAddTag($tag)){
                $this->addTagProjectPair($tag, $ProjectID);
            }
        }
    }
    

    //Validation/checking functions
    public function canUserAddTag($tag){
        $stmt = mysqli_prepare($this->link, "SELECT * FROM `Tags` WHERE `TagID` = ? AND `isTagUserAddable` = 0;");
        mysqli_stmt_bind_param($stmt, 'i', $tag);
        // execute prepared statement
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result){
            if (mysqli_num_rows($result)==0){
                return true;
            }
        }
        return false;
    }

    public function validateStringRange($string,$start,$end){
        //inclusive
        if (is_string($string)){
            if (strlen($string)>=$start&&strlen($string)<=$end){
                return true;
            }
        }
        return false;
    }

    public function validateStringNonNull($string){
        //inclusive
        if (is_string($string)){
            if (strlen($string)>=0){
                return true;
            }
        }
        return false;
    }

    public function validateArray($array,$length){
        if (is_array($array)){
            if (count($array)<=$length){
                return true;
            }
        }
        return false;
    }
}
?>
