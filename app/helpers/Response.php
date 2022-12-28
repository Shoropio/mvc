<?php

namespace App\Helpers;

class Response
{
	private $headers = [];
	private $statusCode = 200;
	private $body;

	public function setHeader($name, $value) {
	    $this->headers[$name] = $value;
	}

	public function setStatusCode($statusCode) {
	    $this->statusCode = $statusCode;
	}

	public function setBody($body) {
	    $this->body = $body;
	}

	public function redirect($url) {
	    $this->headers['Location'] = $url;
	    $this->statusCode = 302;
	}

	public function render() {
	    // Enviar los encabezados
	    foreach ($this->headers as $name => $value) {
	      	header("$name: $value");
	    }

	    // Enviar el código de estado
	    http_response_code($this->statusCode);

	    // Mostrar el cuerpo de la respuesta
	    echo $this->body;
	 }

    // Método para redirigir a una ruta específica
    public function redirectt($path)
    {
        header("Location: $path");
        exit;
    }

    // Método para obtener la URL de la aplicación
    public function getUrl($path)
    {
        // Obtener la URL base de la aplicación
        $baseUrl = $this->getBaseUrl();

        // Concatenar la ruta especificada con la URL base
        return $baseUrl . $path;
    }

    // Método para obtener la URL base de la aplicación
    public function getBaseUrl()
    {
        // Obtener el protocolo utilizado (http o https)
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';

        // Obtener el nombre del host
        $host = $_SERVER['HTTP_HOST'];

        // Obtener la carpeta donde se encuentra la aplicación
        //$folder = dirname($_SERVER['SCRIPT_NAME']);

        //$folder = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        // Generar y retornar la URL base
        //return "$protocol://$host$folder/";

        $root = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
		$root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

		return $root;
    }
}





/*
namespace App;

class Response {
  private $data;
  private $status;
  private $message;

  // Constructor de la clase
  public function __construct($data = null, $status = 200, $message = '') {
    $this->data = $data;
    $this->status = $status;
    $this->message = $message;
  }

    public function setData($data) {
	    $this->data = $data;
	}

	public function getData() {
	    return $this->data;
	}

	public function setStatus($status) {
	    $this->status = $status;
	}

	public function getStatus() {
	    return $this->status;
	}

	public function setMessage($message) {
	    $this->message = $message;
	}

	public function getMessage() {
	    return $this->message;
	}

	public function send() {
	    http_response_code($this->status);
	    echo json_encode([
	      	'data' => $this->data,
	      	'message' => $this->message,
	    ]);
	}
}
*/
/*



$response = new Response([ 'id' => 1, 'name' => 'John' ]);
$response->send();


*/