<?php

namespace Cm;


class CouchDBNotFoundException extends \Exception {};


class CouchDB
{
	public $_hostName = '';
	public $_port = 5984;
	public $_userName = '';
	public $_password = '';
	public $_debug = false;
	public $_options=['cast'=>'object'];

	public function __construct()
	{
		$this->_schema = 'http';
		$this->_hostName = 'localhost';
		$this->_databaseName = '';
		$this->_port = 5984;
		$this->_userName = '';
		$this->_password = '';
	}

	public function setHostName($hostName)
	{
		$this->_hostName = $hostName;
	}

	public function setPort($port)
	{
		$this->_port = $port;
	}

	public function setDatabaseName($databaseName)
	{
		$this->_databaseName = $databaseName;
	}

	public function getDatabaseName(){
		return $this->_databaseName;
	}

	public function setUserName($userName)
	{
		$this->_userName = $userName;
	}

	public function setPassword($password)
	{
		$this->_password = $password;
	}

	public function setOptions($options){
		$this->_options = array_merge($this->_options,$options);
	}

	public function setDebug($debug)
	{
		$this->_debug = $debug;
	}

	public function connect()
	{
		return;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:5984/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-type: application/json',
			'Accept: */*',
		));

		$response = curl_exec($ch);
		curl_close($ch);
	}

	public function databaseList()
	{
		return;
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:5984/_all_dbs');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-type: application/json',
			'Accept: */*',
		));

		$response = curl_exec($ch);

		curl_close($ch);
	}

	public function __request($method, $options = [])
	{
		$options = (object) array_merge([
			'database' => $this->_databaseName,
			'path' => '',
		], $options);

		//print_r($options);


		$ch = curl_init();

		$url = "{$this->_schema}://{$this->_hostName}:{$this->_port}";
		if (!empty($options->database)) {
			$url .= "/{$options->database}";
		}

		if ($options->path) {
			$url .= "{$options->path}";
		}

		if( $this->_debug ){
			echo $url."\n";
		}



		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if (!empty($options->data)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $options->data);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-type: application/json',
			'Accept: */*',
		));

		curl_setopt($ch, CURLOPT_USERPWD, "{$this->_userName}:{$this->_password}");

		$tmp = curl_exec($ch);
		//$tmp = json_decode($tmp, false);
		$tmp = json_decode($tmp, $this->_options['cast']=='array');

		curl_close($ch);

		return $tmp;
	}

	public function uuid()
	{
		$tmp = $this->__request('GET', [
			'database' => '',
			'path' => '/_uuids',
		]);

		//print_r($tmp);
		return $tmp->uuids[0];
	}

	public function createDatabase()
	{
		$tmp = $this->__request('PUT', []);

		return $tmp;
	}

	public function get($docId)
	{
		if (empty($docId)) {
			throw new \Exception('Invalid docId');
		}

		$tmp = $this->__request('GET', [
			'path' => "/{$docId}",
		]);

		/*
		if( !empty($tmp["error"]) && $tmp->error =="not_found" ){
			throw new CouchDBNotFoundException("Not Found, {$tmp->reason}",404);
		}
		*/

		return $tmp;
	}

	public function put($doc)
	{
		$tmp = $this->__request('PUT', [
			'path' => "/{$doc->_id}",
			'data' => json_encode($doc),
		]);

		return $tmp;
	}

	public function post($doc)
	{
		$tmp = $this->__request('POST', [
			'data' => json_encode($doc),
		]);

		return $tmp;
	}

	public function remove($docId, $rev)
	{
		if (empty($docId)) {
			throw new Exception("Can't remove, empty docId");
		}

		$tmp = $this->__request('DELETE', [
			'path' => "/{$docId}?rev={$rev}",
		]);

		return $tmp;
	}

	public function query($fn, $options = [])
	{
		/*
		{
			"map" : "function(doc) { if (doc.foo=='bar') { emit(null, doc.foo); } }"
		}
		*/

		/*
		startkey
		startkey_docid
		endkey
		endkey_docid
		limit
		descending
		skip
		include_docs
		*/

		$isTempView = strrpos($fn,"function") !== false;

		$options = array_merge([
			'inclusive_end'=>true,
			'descending' => false,
			'include_docs' => true
		], $options);


		$options['inclusive_end'] = $options['inclusive_end']===true ? 'true' : 'false';
		$options['include_docs'] = $options['include_docs']===true ? 'true' : 'false';
		$options['descending'] = $options['descending']===true ? 'true' : 'false';

		if( !empty($options["startkey"]) && !is_string($options["startkey"]) ){
			$options["startkey"] = json_encode($options["startkey"]);
		}

		if( !empty($options["endkey"]) && !is_string($options["endkey"]) ){
			$options["endkey"] = json_encode($options["endkey"]);
		}

		$urlParams = http_build_query($options);

		if( $isTempView ){
			$path = '/_temp_view?'.$urlParams;
		}
		else {
			$path = "/_design/{$fn}/_view/{$fn}?".$urlParams;
		}


		//echo "{$path}\n";

		$tmp = $this->__request('POST', [
			'path' => $path,
			'data' => json_encode([
				'map' => $fn,
			]),
		]);

		return $tmp;
	}

	public function page($fn, $options = [])
	{

	}

	public function executeJs($filename)
	{
		$v8 = new \v8js();

		$raw = file_get_contents($filename);
		$tmp = $v8->executeString($raw, $filename);
		$tmp = json_decode(json_encode($tmp));

		return $tmp;
	}
}
