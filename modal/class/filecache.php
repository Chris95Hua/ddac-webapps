<?php

class FileCache {
	const FILE_WRITE = 'w';
	const FILE_READ = 'r';

// file caching for single server (slower than ram)
// memcached for distributed setup
// varnished

	public function store($key, $data, $ttl) {
		$handle = fopen($this->getFileName($key), self::FILE_WRITE);

		if(!$handle) {
			throw new Exception('Could not write to cache');
		}

		// lock the file for writing
		flock($handle, LOCK_EX);
		fseek($handle, 0);
		ftruncate($handle, 0);

		$data = serialize(array(time()+$ttl, $data));

		if(fwrite($handle, $data)===false) {
			throw new Exception('Could not write to cache');
		}

		fclose($handle);
	}


	private function getFileName($key) {
		return 'path' . md5($key);
	}


	public function fetch($key) {
		$filename = $this->getFileName($key);

		if(!file_exists($filename) || !is_readable($filename)) {
			return false;
		}

		$handle = fopen($filename, self::FILE_READ);
		if(!$handle) {
			return false;
		}

		flock($handle, LOCK_SH);

		$data = file_get_contents($filename);
		fclose($handle);

		$data = @unserialize($data);
		if(!$data) {
			unlink($filename);
			return false;
		}

		if(time() > $data[0]) {
			unlink($filename);
			return false;
		}

		return $data[1];
	}


	public function delete($key) {
		$filename = $this->getFileName($key);

		if(file_exists($filename)) {
			return unlink($filename);
		}
		else {
			return false;
		}
	}
}
