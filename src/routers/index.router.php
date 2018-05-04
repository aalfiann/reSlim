<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\SimpleCache as SimpleCache;

    // GET example api to show all data role
    $app->get('/', function (Request $request, Response $response) {
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(3600)){
            $datajson = SimpleCache::load();
        } else {
            $data = [
                'status' => 'success',
			    'code' => '200',
    			'welcome' => 'Hello World, here is the default index reSlim',
                'author' => [
                    'name' => 'M ABD AZIZ ALFIAN (aalfiann@gmail.com)',
                    'github' => 'https://github.com/aalfian/reSlim',
                    'license' => 'https://github.com/aalfiann/reSlim/blob/master/license.md'
                ],
                'how_to_use' => 'reSlim is using authentication by token. So You have to register and login to get generated new token.',
                'generate_time' => date('Y-m-d h:i:s a', time())
            ];
            $blacklistparam = ["&_=","&query=","&search=","token","apikey","api_key","time","timestamp","time_stamp","etag","key","q","s","k","t"];
            $datajson = SimpleCache::save(json_encode($data, JSON_PRETTY_PRINT),null,$blacklistparam);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200);
    });