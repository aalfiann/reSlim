<?php

namespace classes;

class reSlimHandlers {
   
   var $code = null; 

    var $responseMessages = [
        //Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        //Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        //Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        //Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        //Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    var $reSlimMessages = [
        // User process success 1xx    
        'RS101' => 'Request process is successfully created!',
        'RS102' => 'Request process is successfully get!',
        'RS103' => 'Request process is successfully updated!',
        'RS104' => 'Request process is successfully deleted!',
        // User process error 2xx
        'RS201' => 'Request process is failed to create!',
        'RS202' => 'Request process is failed to get!',
        'RS203' => 'Request process is failed to update!',
        'RS204' => 'Request process is failed to delete!',
        // User authority success 3xx
        'RS301' => 'Your token is actived!',
        'RS302' => 'Your token is match!',
        'RS303' => 'Your token is pass!',
        'RS304' => 'Your token is authorized',
        // User authority error 4xx
        'RS401' => 'Your token is expired, so You have to generate new token!',
        'RS402' => 'Your token is not match!',
        'RS403' => 'Your Token is wrong!',
        'RS404' => 'Your token is not authorized',
        'RS405' => 'You don\'t have any token!',
        // User data success 5xx
        'RS501' => 'Data records is found!',
        // User data error 6xx
        'RS601' => 'There is no any data records!',
        'RS602' => 'Data records is exceed the limit!',
        // Parameter success 7xx
        'RS701' => 'The parameter is valid!',
        'RS702' => 'The parameter is authorized!',
        // Parameter error 8xx
        'RS801' => 'The parameter is not valid!',
        'RS802' => 'The parameter is not authorized!',
        'RS803' => 'The parameter is deprecated!'
    ];

    public function getreSlimMessage($code){
        return $this->reSlimMessages[$code];
    }

    public function getResponseMessage($code){
        return $this->responseMessages[$code];
    }
}