<?php
namespace App\Utility;

class HttpClient implements IHttpClient{
    protected $response;
    protected $responseHeaders;
    protected $statusCode;
    
    public function __construct(){
        $this->responseHeaders = [];
    }

    public function request(string $method, string $URL, array $params = null, array $headers = null, \Closure $closure = null)
    {
        // FOR NOW JUST ONLY DO GET REQUEST, EVEN IF METHOD IS OTHERWISE.
        $queryString="";
        if(!is_null($params)){
            $queryString = "?";
            foreach($params as $param=>$value){
                $queryString .= "$param=$value&";
            }
            $queryString = substr($queryString, 0, -1);
        }

        $requestObject = curl_init($URL.$queryString);

        curl_setopt($requestObject, CURLOPT_RETURNTRANSFER, TRUE);
        

        if(!is_null($headers)){
            if(!curl_setopt($requestObject, CURLOPT_HTTPHEADER, $headers)){
                throw new \Exception("Headers not accepted.");
            }
        }

        curl_setopt($requestObject, CURLOPT_HEADERFUNCTION, function($curl, $header){
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) // ignore invalid headers
            return $len;

            $name = strtolower(trim($header[0]));
            if (!array_key_exists($name, $this->responseHeaders))
            $this->responseHeaders[$name] = [trim($header[1])];
            else
            $this->responseHeaders[$name][] = trim($header[1]);

            return $len;
        });

        $this->statusCode = curl_getinfo($requestObject, CURLINFO_HTTP_CODE);

        $responseString = curl_exec($requestObject);

        // if(is_null($this->responseHeaders['content-type'][0])||$this->responseHeaders['content-type'][0] != 'application/json'){
        //     throw new \ParseError("Response not of json content type.");
        // }
        $this->response = json_decode($responseString,true);
    }

    public function getStatusCode()
    {
        return curl_getinfo($requestObject, CURLINFO_HTTP_CODE);;
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    public function getResponseBody()
    {
        return $this->response;
    }
}