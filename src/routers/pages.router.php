<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

    // POST api to create new page
    $app->post('/page/data/new', function (Request $request, Response $response) {
        $pages = new classes\modules\Pages($this->db);
        $datapost = $request->getParsedBody();
        $pages->username = $datapost['Username'];
        $pages->token = $datapost['Token'];
        $pages->title = $datapost['Title'];
        $pages->image = $datapost['Image'];
        $pages->description = $datapost['Description'];
        $pages->content = $datapost['Content'];
        $pages->tags = $datapost['Tags'];
        $body = $response->getBody();
        $body->write($pages->addPage());
        return classes\Cors::modify($response,$body,200);
    });

    // POST api to update page
    $app->post('/page/data/update', function (Request $request, Response $response) {
        $pages = new classes\modules\Pages($this->db);
        $datapost = $request->getParsedBody();    
        $pages->username = $datapost['Username'];
        $pages->token = $datapost['Token'];
        $pages->title = $datapost['Title'];
        $pages->image = $datapost['Image'];
        $pages->description = $datapost['Description'];
        $pages->content = $datapost['Content'];
        $pages->tags = $datapost['Tags'];
        $pages->pageid = $datapost['PageID'];
        $pages->statusid = $datapost['StatusID'];
        $body = $response->getBody();
        $body->write($pages->updatePage());
        return classes\Cors::modify($response,$body,200);
    });

    // POST api to delete page
    $app->post('/page/data/delete', function (Request $request, Response $response) {
        $pages = new classes\modules\Pages($this->db);
        $datapost = $request->getParsedBody();    
        $pages->pageid = $datapost['PageID'];
        $pages->username = $datapost['Username'];
        $pages->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($pages->deletePage());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show all data page pagination registered user
    $app->get('/page/data/search/{username}/{token}/{page}/{itemsperpage}/', function (Request $request, Response $response) {
        $pages = new classes\modules\Pages($this->db);
        $pages->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $pages->username = $request->getAttribute('username');
        $pages->token = $request->getAttribute('token');
        $pages->page = $request->getAttribute('page');
        $pages->itemsPerPage = $request->getAttribute('itemsperpage');
        $body = $response->getBody();
        $body->write($pages->searchPageAsPagination());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show all data status page
    $app->get('/page/data/status/{token}', function (Request $request, Response $response) {
        $pages = new classes\modules\Pages($this->db);
        $pages->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($pages->showOptionRelease());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show single data page registered user
    $app->get('/page/data/read/{pageid}/{username}/{token}', function (Request $request, Response $response) {
        $pages = new classes\modules\Pages($this->db);
        $pages->username = $request->getAttribute('username');
        $pages->token = $request->getAttribute('token');
        $pages->pageid = $request->getAttribute('pageid');
        $body = $response->getBody();
        $body->write($pages->showSinglePage());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show single data page public
    $app->get('/page/data/public/read/{pageid}/', function (Request $request, Response $response) {
        $pages = new classes\modules\Pages($this->db);
        $pages->pageid = $request->getAttribute('pageid');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($pages->showSinglePagePublic());
        return classes\Cors::modify($response,$body,200);
    })->add(new \classes\middleware\ApiKey(filter_var((empty($_GET['apikey'])?'':$_GET['apikey']),FILTER_SANITIZE_STRING)));

    // GET api to show all data page pagination public
    $app->get('/page/data/public/search/{page}/{itemsperpage}/', function (Request $request, Response $response) {
        $pages = new classes\modules\Pages($this->db);
        $pages->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $pages->page = $request->getAttribute('page');
        $pages->itemsPerPage = $request->getAttribute('itemsperpage');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($pages->searchPageAsPaginationPublic());
        return classes\Cors::modify($response,$body,200);
    })->add(new \classes\middleware\ApiKey(filter_var((empty($_GET['apikey'])?'':$_GET['apikey']),FILTER_SANITIZE_STRING)));

    // GET api to show all data published page pagination public
    $app->get('/page/data/public/published/{page}/{itemsperpage}/', function (Request $request, Response $response) {
        $pages = new classes\modules\Pages($this->db);
        $pages->page = $request->getAttribute('page');
        $pages->itemsPerPage = $request->getAttribute('itemsperpage');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($pages->showPublishPageAsPaginationPublic());
        return classes\Cors::modify($response,$body,200);
    })->add(new \classes\middleware\ApiKey(filter_var((empty($_GET['apikey'])?'':$_GET['apikey']),FILTER_SANITIZE_STRING)));

    // GET api to show all data published page ascending pagination public
    $app->get('/page/data/public/published/{page}/{itemsperpage}/{sort}/', function (Request $request, Response $response) {
        $pages = new classes\modules\Pages($this->db);
        $pages->page = $request->getAttribute('page');
        $pages->itemsPerPage = $request->getAttribute('itemsperpage');
        $pages->sort = $request->getAttribute('sort');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($pages->showPublishPageAsPaginationPublic());
        return classes\Cors::modify($response,$body,200);
    })->add(new \classes\middleware\ApiKey(filter_var((empty($_GET['apikey'])?'':$_GET['apikey']),FILTER_SANITIZE_STRING)));

    // GET api to update data view page
    $app->get('/page/data/view/{pageid}/', function (Request $request, Response $response) {
        $pages = new classes\modules\Pages($this->db);
        $pages->pageid = $request->getAttribute('pageid');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($pages->updateViewPage());
        return classes\Cors::modify($response,$body,200);
    })->add(new \classes\middleware\ApiKey(filter_var((empty($_GET['apikey'])?'':$_GET['apikey']),FILTER_SANITIZE_STRING)));