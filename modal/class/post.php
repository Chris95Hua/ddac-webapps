<?php

class Post {
	private $_db, $_data, $_authorID;
	
	//establish DB connection
	public function __construct() {
		$this->_db = DB::getInstance();
		$this->_authorID = $_SESSION['ID'];
	}
	
	//get posts based on author ID
	public function getAuthorPosts($role = null) {
		$qHead = "SELECT post.postID,
			post.title,
			post.post_date
			FROM post
			INNER JOIN user ON post.userID = user.userID ";
		
		$qTail = "ORDER BY post.post_date DESC";
		
		$q = ($role == "Admin") ? $qHead.$qTail : $qHead."WHERE post.userID = {$this->_authorID} ".$qTail;
		
		$posts = array();
		
		$stmt = $this->_db->execute($q);
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC !== false)) {
			$posts[] = array(
				'ID' => $row['postID'],
				'title' => $row['title'],
				'date' => $row['post_date']
			);
		}
		
		return $posts;
	}
	
	//get intakes
	public function getIntakes() {	
		$q = "SELECT intakeID, faculty FROM intake";
		
		$stmt = $this->_db->execute($q);
		
		$data = array();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC !== false)) {
			$data[] = array(
				'ID' => $row['intakeID'],
				'faculty' => $row['faculty']
			);
		}
		
		return $data;
	}
	
	//publish post
	public function create($title, $intake=array(), $content) {
		$title = escape(trim($title));
		$intake = escape($intake);
		$content = escape($content);
		
		if(strlen($title) > 0) {
			$q = "INSERT INTO post (title,intake,post_date,userID,content) VALUES ('{$title}','{$intake}',UNIX_TIMESTAMP(),{$this->_authorID},'{$content}')";
			
			if(!$this->_db->execute($q)) {
				throw new Exception("There was a problem publishing the post");
			}
		}
	}
	
	//update post
	public function update($postID, $title, $intake=array(), $content) {
		$title = escape(trim($title));
		$intake = escape($intake);
		$content = escape($content);
		
		if(strlen($title) > 0)  {
			$q = "UPDATE post SET last_edit_date = UNIX_TIMESTAMP(),
				title = '{$title}',
				intake = '{$intake}',
				content = '{$content}'
				WHERE postID = {$postID}";
				
			if(!$this->_db->execute($q)) {
				throw new Exception("There was a problem updating the post");
			}
		}
	}
	
	//delete post
	public function delete($postID = null) {
		if($postID) {
			$this->_db->delete("post", array("postID", "=", $postID));
		}
		
		return false;
	}
	
	//find post and return result
	public function find($postID = null, $role = null) {
		if($postID) {
			$q = ($role == 'Admin') ? "SELECT * FROM post WHERE postID = {$postID}" : "SELECT * FROM post WHERE postID = {$postID} AND userID = {$this->_authorID}" ;
			
			$data = $this->_db->execute($q);
			
			$result = $data->fetchAll(PDO::FETCH_OBJ);
			
			if ($data->rowCount()) {
				$this->_data = $result[0];
				return true;
			}
		}
		return false;
	}
	
	//return post information
	public function data() {
		return $this->_data;
	}

}