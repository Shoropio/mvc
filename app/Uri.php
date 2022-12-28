<?php

namespace App;

class Uri
{
	private $scheme;
	private $host;
	private $path;
	private $query;

	public function __construct() {
	  	$uri = parse_url($_SERVER['REQUEST_URI']);
	  	$this->scheme = $uri['scheme'];
	  	$this->host = $uri['host'];
	  	$this->path = $uri['path'];
	  	$this->query = $uri['query'];
	}

	public function getScheme() {
	  	return $this->scheme;
	}

	public function getHost() {
	  	return $this->host;
	}

	public function getPath() {
	  	return $this->path;
	}

	public function getQuery() {
	  	return $this->query;
	}
}