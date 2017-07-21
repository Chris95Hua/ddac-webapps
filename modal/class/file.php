<?php
class File {

    // Reserve variables
    protected $_fileTypes = null;
    protected $_repository = null;
	protected $_allowedPaths = array();
    protected static $_role = null;

	
	public function init($role = null, $allowed) {
		$this->_fileTypes = require_once(implode(DS, array(MODAL, 'config', 'file_types.php')));

		// test if file-upload ajax call
		if (isset($_GET['upload']) && $_GET['upload'] == '1' && File::checkPermissions($role)){
			$this->ajaxUpload();
		}
		
		$this->_repository = Config::get('shared_folder');
		
		if (isset($allowed)) $this->_allowedPaths = $allowed;
		
		self::$_role = $role;
		
		$_SESSION['simple_auth']['cryptsalt'] = Config::get('encryption_salt');
				
		// handle actions (copy, paste, delete, rename etc.)
		$this->actions();
		
		$this->changeDirectory();
		
		// get current directory file-list
		return $this->getDirectoryList();
	}
	
	// TODO: flush link
	/*
	 * get and post actions (router)
	 */
	public function actions(){

		// no read permissions?
		if (!File::checkPermissions('r')){
			File::writeLog('auth bad - no read access');
			File::error("Access Forbidden");
			die;
		}

		// POST actions
		if (isset($_POST['action'])){

			$action = $_POST['action'];
			unset($_POST['action']);

			// actions with read & write permissions
			if (File::checkPermissions('rw')){
				switch ($action){

					case 'delete':

						foreach ($_POST as $post_file){

							if (in_array($post_file, Config::get('restricted_files'))) continue;

							$files[] = $this->filterInput($this->decrypt($post_file));
						}

						$this->deleteFiles($files, $_SESSION['cwd']);

						break;

					case 'rename':
						
						if (!isset($_POST['oldname']) || !isset($_POST['newname'])) break;

						$oldname = $this->filterInput($this->decrypt($_POST['oldname']));
						$newname = $this->filterInput($_POST['newname']);

						if (in_array($oldname, Config::get('restricted_files')) || in_array($newname, Config::get('restricted_files'))) break;

						$this->renameFile($oldname, $newname);
						
						break;

					case 'edit-save':

						if (!isset($_POST['filename'])) break;

						$filename = $this->filterInput($this->decrypt($_POST['filename']));
						$content = $_POST['content'];

						if (in_array($filename, Config::get('restricted_files'))) break;

						file_put_contents($_SESSION['cwd'].DS.$filename, $content);

						File::writeLog('edit file / save - '.$filename);

						break;

					case 'zip':

						if (!isset($_POST['archivename'])) break;
						$archive_name = $this->filterInput($_POST['archivename']);
						unset($_POST['archivename']);

						foreach ($_POST as $post_file){
							$files[] = $this->filterInput($this->decrypt($post_file));
						}

						$this->zipFiles($files, $archive_name);

						break;

					case 'unzip':

						if (!isset($_POST['filename'])) break;

						$filename = $this->filterInput($this->decrypt($_POST['filename']));

						$this->unzipFile($filename);

						break;

					case 'simple-copy':
					case 'simple-move':

						// link to home dir is blank
						if (!isset($_POST['destination'])) $_POST['destination'] = '';

						$destination = $this->filterInput($this->decrypt($_POST['destination']), false);
						$destination = rawurldecode($destination);
						unset($_POST['destination']);

						foreach ($_POST as $post_file){
							$files[] = $this->filterInput($this->decrypt($post_file));
						}

						if ($action == 'simple-copy'){
							$this->copyFiles($files, $_SESSION['cwd'], $this->_repository.DS.$destination);
						}

						if($action == 'simple-move'){
							$this->moveFiles($files, $_SESSION['cwd'], $this->_repository.DS.$destination);
						}

						break;

					default:
						break;
				}
					
			}

			// flush url
			// header('Location: /BC_Webportal/course');
			die;
		}


		// download file
		if (isset($_GET['download']) && !empty($_GET['download'])){

			$filename = $this->filterInput($this->decrypt($_GET['download']));

			if (in_array($filename, Config::get('restricted_files'))) die;

			if (!file_exists($_SESSION['cwd'].DS.$filename)) die;

			// Set headers
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/octet-stream");
			header("Content-Transfer-Encoding: binary");

			// output file
			set_time_limit(0);
			$file = @fopen($_SESSION['cwd'].DS.$filename,"rb");
			while(!feof($file))
			{
				print(@fread($file, 1024*8));
				ob_flush();
				flush();
			}

			File::writeLog('download - '.$filename);

			die;
		}

		// edit action - load file content via this ajax
		if (isset($_GET['edit-load']) && File::checkPermissions('rw')){

			$filename = $this->filterInput($this->decrypt($_GET['edit-load']));
			
			if (in_array($filename, Config::get('restricted_files'))) die;

			if (!file_exists($_SESSION['cwd'].DS.$filename)) die;
			
			echo file_get_contents($_SESSION['cwd'].DS.$filename);

			File::writeLog('edit file / load - '.$filename);

			die;
		}

		// new folder / new file
		if ((isset($_GET['newdir']) || isset($_GET['newfile'])) && self::$_role != 'Student'){

			$newdir = $newfile = '';
			
			if (isset($_GET['newdir']) && $_GET['newdir'] != ''){
				$newdir = $this->filterInput($_GET['newdir']);

				if (!in_array($newdir, Config::get('restricted_files')))
					$this->newFolder($_SESSION['cwd'].DS.$newdir);
				

			} elseif (isset($_GET['newfile']) && $_GET['newfile'] != ''){
				$newfile = $this->filterInput($_GET['newfile']);

				if (!in_array($newfile, Config::get('restricted_files')))
					touch($_SESSION['cwd'].DS.$newfile);
			}
			
			File::writeLog('create new - '.$newdir.$newfile);

			// flush url
			header('Location: '.Config::get('base_url').'/'.$_GET['page']);
			die;
		}

		// directory tree - ajax load
		if (isset($_GET['tree']) || !empty($_GET['tree'])){
			
			$tree_action = $this->filterInput($_GET['tree']);

			$dirs = '';

			if ($tree_action == 'cd'){
				$dirs = $this->getDirectoryTree($this->_repository, false, '/cd/');
			}

			if ($tree_action == 'copy' || $tree_action == 'move'){
				$dirs = $this->getDirectoryTree($this->_repository, true, '');
			}
			
			echo $dirs;

			File::writeLog('tree load');
			die;
		}

		return;
	}
	
	
	/*
	 * set current working directory
	 */
	public function changeDirectory(){
		
		// in no session or home - set defaults
		if (!isset($_SESSION['cwd']) || (isset($_GET['cd']) && $this->decrypt($_GET['cd']) == '')){		
			
			$_SESSION['cwd'] = $this->_repository;
			
			File::writeLog('change dir - home');

			return;
		}

		// get directory from url
		$input = (isset($_GET['cd']) ? $this->filterInput($this->decrypt($_GET['cd']), false) : false);
		
		if ($input && strpos($input, '..') === false){

			$childDir = $this->_repository.DS.$input;

			// do not allow chdir outside repository
			if (strstr($childDir, $this->_repository) == false){
				
				header('Location: '.Config::get('base_url').'/'.$_GET['page']);
				die;
			}

			if (is_dir($childDir)){
				// change to dir if exists
				$_SESSION['cwd'] = $childDir;
				
				File::writeLog('change dir - '.$input);
			}

		}
		
		return;
	}
	
	
	/*
	 * fix path to work with url
	 */
	public static function encodeurl($string){

		$string = rawurlencode($string);

		// do not encode /, :
		$string = str_replace("%2F", "/", $string);
		$string = str_replace("%5C", "/", $string);

		$string = str_replace("%3A", ":", $string);

		return $string;
	}


	/*
	 * filter user's input
	 */
	public function filterInput($string, $strict = true){

		// bad chars
		$strip = array("..", "*", "\n");

		// we need this sometimes
		if ($strict) array_push($strip, "/", "\\");

		$clean = trim(str_replace($strip, "_", strip_tags($string)));

		return $clean;
	}
	
	
	/*
	 * Delete file or dir
	 */
	public function deleteFiles($files, $directory){

		foreach ($files as $file){

			File::writeLog('delete - '.$file);

			if ($file == '.' || $file == '..') continue;

			if (is_dir($directory.DS.$file) == true){
				$this->recursiveRemoveDirectory($_SESSION['cwd'].DS.$file);
				$this->syncBufferFile($_SESSION['cwd'].DS.$file);
				continue;
			}

			unlink($directory.DS.$file);

			$this->syncBufferFile($directory.DS.$file);
		}
	}


	/*
	 * Copy files or dirs
	 */
	public function copyFiles($files, $source_dir, $destination_dir){

		foreach($files as $file){

			// destination is not a source's subfolder?
			if(strpos($destination_dir.DS.$file, $source_dir.DS.$file.DS) !== false){
				continue;
			}

			$this->copyRecursively($source_dir.DS.$file, $destination_dir.DS.$file);
		}

	}

	
	/*
	 * Copy files recursively
	 */
	public function copyRecursively($source, $dest){

		// Simple copy for a file
		if (is_file($source)) {
			File::writeLog('copy - '.$dest);
			return copy($source, $dest);
		}

		// Make destination directory
		if (!is_dir($dest)) {
			mkdir($dest);
		}

		// Loop through the folder
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep copy directories
			$this->copyRecursively($source.DS.$entry, $dest.DS.$entry);
			File::writeLog('copy - '.$source.' > '.$dest);
		}

		// Clean up
		$dir->close();

		return true;
	}


	/*
	 * Move files or dirs
	 */
	public function moveFiles($files, $source_dir, $destination_dir){

		// batch move
		foreach($files as $file){

			if ($file == '.' || $file == '..' || in_array($file, Config::get('restricted_files'))) return false;

			if (!file_exists($destination_dir.DS.$file)){

				// destination is not a source's subfolder?
				if(strpos($destination_dir.DS.$file, $source_dir.DS.$file.DS) !== false){
					continue;
				}

				rename($source_dir.DS.$file, $destination_dir.DS.$file);

				File::writeLog('move - '.$file);

				$this->syncBufferFile($source_dir.DS.$file);
			}
		}
		return;
	}


	/*
	 * Reneme file or dir
	 */
	public function renameFile($old_name, $new_name){

		if (!file_exists($_SESSION['cwd'].DS.$new_name)){
			rename($_SESSION['cwd'].DS.$old_name, $_SESSION['cwd'].DS.$new_name);

			File::writeLog('rename - '.$old_name.' > '.$new_name);

			$this->syncBufferFile($_SESSION['cwd'].DS.$old_name);
		}

		return;
	}


	/*
	 * Zip selected files
	 */
	public function zipFiles($input_files, $destination){

		if (!extension_loaded('zip')) {
			File::error('Zip PHP module is not installed on this server');
			die;
		}

		$destination = $_SESSION['cwd'].DS.$destination;
		if (substr($destination, -4, 4) != '.zip'){
			$destination = $destination.'.zip';
		}

		$zip = new ZipArchive();
		if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			File::error('Archive could not be created');
		}

		$startdir = str_replace('\\', '/', $_SESSION['cwd']);

		foreach ($input_files as $source){

			File::writeLog('zip - '.$source);

			$source = $_SESSION['cwd'].DS.$source;

			$source = str_replace('\\', '/', $source);

			if (is_dir($source) === true)
			{
				$subdir = str_replace($startdir.'/', '', $source) . '/';
				$zip->addEmptyDir($subdir);
					

				$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

				foreach ($files as $file)
				{

					$file = str_replace('\\', '/', $file);

					// Ignore "." and ".." folders
					if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
						continue;

					if (is_dir($file) === true)
					{
						$zip->addEmptyDir($subdir . str_replace($source . '/', '', $file . '/'));
					}
					else if (is_file($file) === true)
					{
						$zip->addFromString($subdir . str_replace($source . '/', '', $file), file_get_contents($file));
					}
				}
			}
			else if (is_file($source) === true)
			{
				$zip->addFromString(basename($source), file_get_contents($source));

			}
		}

		$zip->close();

		return;
	}


	/*
	 * UnZip archive
	 */
	public function unzipFile($file){

		if (!extension_loaded('zip')) {
			File::error('Zip PHP module is not installed on this server');
			die;
		}

		$file = $_SESSION['cwd'].DS.$file;

		$zip = zip_open($file);
		if(!$zip) {
			File::error("Unable to proccess file '{$file}'");
		}

		$e='';

		while($zip_entry=zip_read($zip)) {
			$zdir=dirname(zip_entry_name($zip_entry));
			$zname=zip_entry_name($zip_entry);

			// test if restricted file
			if (strpos($zname, implode("", Config::get('restricted_files'))) !== false) continue;

			if(!zip_entry_open($zip,$zip_entry,"r")) {
				$e.="Unable to proccess file '{$zname}' <br />";
				continue;
			}

			File::writeLog('unzip - '.$zname);

			// create dir if not exist
			if(!is_dir($_SESSION['cwd'].DS.$zdir)){
				mkdir($_SESSION['cwd'].DS.$zdir, Config::get('new_dir_mode'));
			}

			// do create empty dirs
			if(!is_dir($_SESSION['cwd'].DS.$zname) && substr($zname, -1, 1) == "/"){
				mkdir($_SESSION['cwd'].DS.$zname, Config::get('new_dir_mode'));
				continue;
			}

			$zip_fs=zip_entry_filesize($zip_entry);

			// do create empty files
			if(empty($zip_fs)){
				@touch($_SESSION['cwd'].DS.$zname);
				continue;
			}

			$zz=zip_entry_read($zip_entry,$zip_fs);

			$z=fopen($_SESSION['cwd'].DS.$zname,"w");
			fwrite($z,$zz);
			fclose($z);
			zip_entry_close($zip_entry);

		}
		zip_close($zip);

		if ($e != ''){
			File::error($e);
		}

		return;
	}


	/*
	 * unset buffer if moving/deleting/renaming something inside buffer
	 */
	public function syncBufferFile($file){

		if (isset($_SESSION['buffer']['files'])){
			foreach($_SESSION['buffer']['files'] as $buffer_file){

				if(strpos($_SESSION['buffer']['directory'].DS.$buffer_file, $file) !== false){
					unset($_SESSION['buffer']);
					return;
				}
			}
		}
	}


	/**
	 *
	 * Mark if file is inside buffer, if not return false
	 */
	public function isBuffered($file){

		if (isset($_SESSION['buffer']['files'])){
			foreach($_SESSION['buffer']['files'] as $buffer_file){

				if($_SESSION['buffer']['directory'].DS.$buffer_file == $file){
					return ' buffer-'.$_SESSION['buffer']['type'];
				}
			}
		}

		return false;
	}

	
	/*
	 * Create new folder
	 */
	public function newFolder($newdir) {
		mkdir($newdir, Config::get('new_dir_mode'));
	}
	
	
	/*
	 * Remove directory and all sub-content
	 */
	public function recursiveRemoveDirectory($directory, $empty=FALSE)
	{
		if(substr($directory,-1) == DS)
		{
			$directory = substr($directory,0,-1);
		}
		if(!file_exists($directory) || !is_dir($directory))
		{
			return FALSE;
		}elseif(is_readable($directory))
		{
			$handle = opendir($directory);
			while (FALSE !== ($item = readdir($handle)))
			{
				if($item != '.' && $item != '..')
				{
					$path = $directory.DS.$item;
					if(is_dir($path))
					{
						$this->recursiveRemoveDirectory($path);
					}else{
						unlink($path);
					}
				}
			}
			closedir($handle);
			if($empty == FALSE)
			{
				if(!rmdir($directory))
				{
					return FALSE;
				}
			}
		}
		return TRUE;
	}


	// TODO: call upload class modularly
	/*
	 * upload files with ajax
	 */
	public function ajaxUpload(){
		
		if ($_SERVER['REQUEST_METHOD'] != 'POST') die;
			
		require_once ('/modal/class/upload.php');

		$upload_handler = new Upload();

		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Content-Disposition: inline; filename="files.json"');
		header('X-Content-Type-Options: nosniff');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
		header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');
		
		$filename = '?';
		if (isset($_FILES['files']['name'])) {
			$filename = $this->filterInput(implode(" ",$_FILES['files']['name']));

			if (in_array($filename, Config::get('restricted_files'))) die;
		}

		File::writeLog('upload file '.$filename);
		
		$upload_handler->post();

		die;
	}
	
	
	// TODO: update role
	/*
	 * get dir listing
	 */
	public function getDirectoryList()
	{
		$directory = $_SESSION['cwd'];
		$parent_directory = dirname($directory);
		
		// check if home dir
		if ($directory == $this->_repository) $home = true;
		
		// create an array to hold directory list
		$dirs = $files = array();

		// open dir and create a handler
		$handler = opendir($directory);

		// if cannot open, reset current dir to home
		if ($handler == false){
			$directory = $_SESSION['cwd'] = $this->_repository;
			$handler = opendir($directory);
		}

		// open directory and walk through the filenames
		while (false !== ($file = readdir($handler))) {
			
			$chkDir = str_replace($this->_repository, '', $directory).DS.$file;
			$chkDir = str_replace('\\','/',$chkDir);
			$_SESSION['dirLevel'] = substr_count($chkDir,'/');
			$chkDir = ltrim($chkDir, '/\\');
			$accessLevel = $_SESSION['role'] == 'Lecturer' ? 2 : 0;

			// if file isn't this directory or its parent or resticted, add it to the results
			if ($file != "." && $file != ".." && !in_array($file, Config::get('restricted_files'))) {
				
				if($_SESSION['dirLevel'] > $accessLevel){
					if (filetype($directory.DS.$file) == 'dir'){
						$link = str_replace($this->_repository, '', $directory).DS.$file;
						$link = ltrim($link, '/\\');
						
						$dirs[] = array(
								'name' => $file,
								'crypt' => $this->encrypt($file),
								'link' => $this->encrypt($link),
								'size' => 0,
								'type' => 'dir',
								'time' => filemtime ($directory.DS.$file),
								'icon_class' => 'fa-folder',
								'buffer' => $this->isBuffered($directory.DS.$file),
						);

					}else{

						$link = Config::get('base_url').str_replace(Config::get('base_path'), '', $directory).DS.$file;
						
						$fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));

						if (isset($this->_fileTypes[$fileExt])) {
							$iconClass = $this->_fileTypes[$fileExt];
						} else {
							$iconClass = $this->_fileTypes['blank'];
						}

						$files[] = array(
								'name' => $file,
								'crypt' => $this->encrypt($file),
								'size' => $this->formatBytes(filesize($directory.DS.$file)),
								'sizeb' => filesize($directory.DS.$file),
								'type' => $this->getFileType($file),
								'time' => filemtime ($directory.DS.$file),
								'icon_class' => $iconClass,
								// create url from path
								'link' => $link,
								'buffer' => $this->isBuffered($directory.DS.$file),
						);
					}
				} else {
					if (filetype($directory.DS.$file) == 'dir'  && in_array($chkDir, $this->_allowedPaths)){
						$link = str_replace($this->_repository, '', $directory).DS.$file;
						$link = ltrim($link, '/\\');
						
						$dirs[] = array(
								'name' => $file,
								'crypt' => $this->encrypt($file),
								'link' => $this->encrypt($link),
								'size' => 0,
								'type' => 'dir',
								'time' => filemtime ($directory.DS.$file),
								'icon_class' => 'fa-folder',
								'buffer' => $this->isBuffered($directory.DS.$file),
						);

					}
				}
			}
		}

		// tidy up: close the handler
		closedir($handler);

		// $dirs = $this->sortByKey($dirs, 'name', true);

		// add back link
		if (!isset($home)){
						
			$link = str_replace($this->_repository, '', $parent_directory);
			$link = ltrim($link, '/\\');
			
			array_unshift($dirs, array(
			'name' => "Go Back",
			'crypt' => $this->encrypt('..'),
			'link' => $this->encrypt($link),
			'size' => 0,
			'type' => 'back',
			'time' => 0,
			'icon_class' => 'fa-level-up',
			'buffer' => false,
			));
		}
				
		// build breadcrumbs
		$breadcrumb = array();
		$next = '';
		$bc = 'Home' . str_replace('\\', '/', str_replace($this->_repository, '', $directory));		
		$bc = explode('/', $bc);
		foreach ($bc as $bclink){
			if ($bclink != 'Home'){
				$next .= '/'.$bclink;
				$next = ltrim($next, '/');
			}
			$breadcrumb[$bclink] = 'course/cd/'.$this->encrypt($next);
		}



		// $files = $this->sortByKey($files, 'name', true);

		return array('dirs' => $dirs, 'files' => $files, 'breadcrumb' => $breadcrumb);
	}


	/*
	 * Sort array by key
	 */
	// public function sortByKey($array, $key, $natural = false) {

		// $order = $_SESSION['sort']['order'];

		// $result = array();

		// $values = array();
		// foreach ($array as $id => $value) {
			// $values[$id] = isset($value[$key]) ? $value[$key] : '';
			// $values[$id] = current(explode(".", $values[$id]));
		// }
			
		// if ($natural){
			// natcasesort($values);
			// if ($order != 1) $values = array_reverse($values, true);
		// }else{
			// if ($order == 1)
				// asort($values);
			// else
				// arsort($values);
		// }

		// foreach ($values as $key => $value) {
			// $result[$key] = $array[$key];
		// }

		// return $result;
	// }


	/*
	 * Bytes formatter
	 */
	public function formatBytes($bytes, $precision = 0) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);

		return round($bytes, $precision) . ' ' . $units[$pow];
	}


	/*
	 * encrypt string
	 */
	public function encrypt($string)
	{
		// test if encryption is off or blank string
		if (Config::get('encrypt_url_actions') != true || !isset($_SESSION['simple_auth']['cryptsalt']) || $string == '') return $string;
			
		$key = $_SESSION['simple_auth']['cryptsalt'];
			
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));

		// url safe
		$ret = strtr($encrypted, '+/=', '-_~');

		return $ret;
	}

	/*
	 * decrypt
	 */
	public function decrypt($string)
	{
		// test if encryption is off or blank string
		if (Config::get('encrypt_url_actions') != true || !isset($_SESSION['simple_auth']['cryptsalt']) || $string == '') return $string;
			
		$key = $_SESSION['simple_auth']['cryptsalt'];
			
		// clean url safe
		$encrypted = strtr($string, '-_~', '+/=');
			
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))), "\0");

		return $decrypted;
	}

	// TODO: access level need to be updated
	/*
	 * create directory tree html
	 */
	public function getDirectoryTree($path, $skip_files = false, $link_prefix = '') {
		
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);

		$dom = new DomDocument("1.0");

		$li = $dom;
		$ul = $dom->createElement('ul');
		$li->appendChild($ul);
		$el = $dom->createElement('li');
		$a = $dom->createElement('a','Home');
		$at = $dom->createAttribute('clink');
		$at->value = $link_prefix;
		$el->appendChild($a);
		$el->appendChild($at);
		$ul->appendChild($el);

		$node = $ul;
		$depth = -1;
		$skip_dir = '';

		foreach($objects as $object){
			$name = $object->getFilename();
			
			$path = $object->getPathname();
			$isDir = is_dir($path);
			
			$link = str_replace($this->_repository, '', $path);
			$level = substr_count($link,'\\');
			$link = File::encodeurl(ltrim($link, '/\\'));
			$accessLevel = $_SESSION['role'] == 'Lecturer' ? 2 : 0;
			
			//skip unwanted folders to 2 levels
			if(!in_array($link, $this->_allowedPaths) && $level<=$accessLevel && $isDir) {
				$skip_dir = str_replace('..','',$link);
				continue;
			}
			
			if($skip_dir != '' && !in_array($link, $this->_allowedPaths) && strstr($link,$skip_dir)){
				continue;
			}
			
			// skip unwanted files
			if ($name == '.' || $name == '..' || (in_array($name, Config::get('restricted_files')) && !$isDir)) continue;
			
			$skip_dir = '';
			$skip = false;


			if (($isDir == false && $skip_files == true )){
				// skip unwanted files
				$skip = true;
			}elseif($isDir == false){
				// this is regural file, no links here
				$link = '';
			}else{
				// this is dir
				$link = $link;
			}

			if ($objects->getDepth() == $depth){
				//the depth hasnt changed so just add another li
				if (!$skip){
					$el = $dom->createElement('li');
					$a = $dom->createElement('a',$name);
					$at = $dom->createAttribute('clink');
					$at->value = $link_prefix.$this->encrypt($link);
					$el->appendChild($a);
					$el->appendChild($at);
					if (!$isDir) $el->appendChild($dom->createAttribute('isfile'));

					$node->appendChild($el);

				}
			}
			elseif ($objects->getDepth() > $depth){
				//the depth increased, the last li is a non-empty folder
				$li = $node->lastChild;
				$ul = $dom->createElement('ul');
				$li->appendChild($ul);
				if (!$skip){
					$el = $dom->createElement('li');
					$a = $dom->createElement('a',$name);
					$at = $dom->createAttribute('clink');
					$at->value = $link_prefix.$this->encrypt($link);
					$el->appendChild($a);
					$el->appendChild($at);
					if (!$isDir) $el->appendChild($dom->createAttribute('isfile'));

					$ul->appendChild($el);
				}
				$node = $ul;
			}
			else{
				//the depth decreased, going up $difference directories
				$difference = $depth - $objects->getDepth();
				for ($i = 0; $i < $difference; $difference--){
					$node = $node->parentNode->parentNode;
				}
				if (!$skip){
					$el = $dom->createElement('li');
					$a = $dom->createElement('a',$name);
					$at = $dom->createAttribute('clink');
					$at->value = $link_prefix.$this->encrypt($link);
					$el->appendChild($a);
					$el->appendChild($at);
					if (!$isDir) $el->appendChild($dom->createAttribute('isfile'));

					$node->appendChild($el);
				}
			}
			$depth = $objects->getDepth();
		}

		return $dom->saveHtml();
	}
	
	/*
	 * Check users permissions
	 */
	public static function checkPermissions($account){

		//CHECK ACCOUNT TYPE
		for($i=0; $i<strlen($mode); $i++) {

			if (strstr($_SESSION['simple_auth']['permissions'], substr($mode, $i, 1)) == false){
				return false;
			}
		}

		return true;
	}
	
	
	/*
	 * Helper for reading php.ini settings
	 */
	public static function returnBytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}
	
	
	/*
	 * do some config validation
	 */
	public static function validateConf($config, $param){
		// do some extra work on repository config query
		if ($param == 'shared_folder'){
			// error if repository does not exist
			if (!is_dir($config['shared_folder'])){
				File::error('Directory does not exist: '.$config['shared_folder']);
			}
		}

		// max_filesize check server's conflict
		if ($param == 'max_filesize'){

			$php_post_max_size = File::returnBytes(ini_get('post_max_size'));
			$php_upload_max_filesize =  File::returnBytes(ini_get('upload_max_filesize'));

			if ($config['max_filesize'] > $php_post_max_size || $config['max_filesize'] > $php_upload_max_filesize){
				File::error('Config param max_filesize is bigger than php server setting: post_max_size = '.$php_post_max_size.', upload_max_filesize = '.$php_upload_max_filesize);
			}
		}

		return $config;
	}
	

	/*
	 * Return file type based on extension
	 */
	public static function getFileType($filename){
		if (preg_match("/^.*\.(jpg|jpeg|png|gif|bmp)$/i", $filename) != 0){
			return 'image';
		}
		elseif (preg_match("/^.*\.(zip)$/i", $filename) != 0) {
			return 'zip';
		}

		return 'generic';
	}


	/*
	 * Display fatal errors
	 */
	public static function error($message){
		echo "</script><script>document.write('')</script>";
		echo "<div class=\"warning\"><strong>{$message}</strong></div>";

		File::writeLog('error - '.$message);

		die;
	}
	
	
	/*
	 * write usage to log
	 */
	public static function writeLog($action){

		if (Config::get('write_log') != true) return;

		if (isset($_SESSION['simple_auth']['username'])) {
			$user = $_SESSION['simple_auth']['username'];
		}
		else{
			$user = 'unknown';
		}

		$ip = $_SERVER["REMOTE_ADDR"];

		$log = date('Y-m-d H:i:s')." | IP $ip | $user | $action \n";

		file_put_contents(Config::get('base_path').DS.'usage.log', $log, FILE_APPEND);
	}

}
