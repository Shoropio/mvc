<?php

namespace App\Helpers;

class Redirect {
  	public static function to($path)
  	{
    	header("Location: $path");
    	exit;
  	}

  	public static function with($type, $message)
	{
	    $_SESSION[$type] = $message;
	    header("Location: " . $_SERVER["HTTP_REFERER"]);
	    exit;
	}

	public function back()
	{
	    header("Location: " . $_SERVER["HTTP_REFERER"]);
	    exit;
	}


}

// Redirect::to('/login');
