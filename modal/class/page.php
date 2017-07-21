<?php

class Page {
	// table field name
	const PAGE_TABLE = 'page';
	const COL_ID = 'page_id';
	const COL_TITLE = 'page_title';
	const COL_SLUG = 'page_slug';

	private $_db, $_cache = false, $_pages = array();


	/**
	* Establish database connection and get available pages
	*/
	public function __construct($page = null) {
		$this->_db = MySQLConn::getInstance();
/*

page cache true = skip check, set cache to true
page cache false = skip check, set cache to false
page cache auto = check table size, cache if less than 64mb


Ways to reduce database transaction/calls
1. Use MemcacheD (hard to/requires setup)
2. Store everything in a 2D array (uses more memory, duplicated for all users)
3. S

*/


		// need a way to load all the data again if necessary
/*		$this->_db->selectAll(self::PAGE_TABLE);

		$this->_pages = $this->_db->fetchAll();*/
		// $this->_pages[index][col]
	}


	/**
	* Get slug of current page from query string
	*
	* @return string                     Slug of current page
	*/
	public static function getPageParam() {
		if($page = Input::get('page')) {
			// TODO: if its numeric, attempt to get it from database

			return $page;
		}

		return Config::get('home');
	}


	/**
	* Check if current page is accessible and user has permission to view it
	*
	* @return string                     Slug of current page
	*
	* NOTE: Will redirect to 404 or 403 page if page not found or
	* 		permission denied
	*/
	public function pageCheck($role = null) {
		if(!$role) {
			$role = $_SESSION['role'];
		}

		if ($page = self::getPageParam()) {
			$this->find($page, true, $role);

			switch ($this->_data[Page::COL_ID]) {
			    case -1:
			        header('Location: error403');
			        break;
			    case -2:
			        header('Location: error404');
			        break;
			}

			return $page;
		}
	}


	/**
	* Get pages based on user permission
	*
	* @param string         $role        User role
	* @return array(string)              Pages
	*/
	public function getAllPages($role = null) {
		$data = $this->_db->execute("SELECT * FROM ".self::PAGE_TABLE);

		$result = $data->fetchAll();

		if($data->rowCount()) {
			foreach($result as $page) {
				$permission = json_decode($page->permission, true);

				if(isset($permission[$role]) == true) {
					$_pages[] = $page->pageID;
				}
			}
			return $_pages;
		}
		return false;
	}


	//find page and return result
	public function find($page, $get, $role) {
		// TODO: check in cached page table
		$field = is_numeric($page) ? self::COL_ID : self::COL_SLUG;
		$proc = is_numeric($page) ? "get_page_by_id" : "get_page_by_slug";

		$this->_db->callSproc($proc, array($field => $page, "role" => $role));

		if($this->_db->count()) {
			if($get) {
				$this->_data = $this->_db->fetch();
			}

			return true;
		}

		return false;
	}


	//get page information
	public function data() {
		return $this->_data;
	}
}
