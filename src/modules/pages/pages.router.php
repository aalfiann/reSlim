<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\middleware\ValidateParam as ValidateParam;
use \classes\middleware\ValidateParamURL as ValidateParamURL;
use \classes\middleware\ApiKey as ApiKey;
use \classes\SimpleCache as SimpleCache;
use \modules\pages\Pages as Pages;


    // Get module information
    $app->map(['GET','OPTIONS'],'/page/get/info/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($pages->viewInfo());
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ApiKey);

    // POST api to create new page
    $app->post('/page/data/new', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
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
    })->add(new ValidateParam('Content'))
        ->add(new ValidateParam('Tags','0-500'))
        ->add(new ValidateParam(['Image','Description'],'0-250'))
        ->add(new ValidateParam(['Title','Token'],'1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));

    // POST api to update page
    $app->post('/page/data/update', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
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
    })->add(new ValidateParam('Content'))
        ->add(new ValidateParam('Tags','0-500'))
        ->add(new ValidateParam(['Image','Description'],'0-250'))
        ->add(new ValidateParam(['PageID','StatusID'],'1-11','numeric'))
        ->add(new ValidateParam(['Token','Title'],'1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));

    // POST api to update draft page
    $app->post('/page/data/update/draft', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $datapost = $request->getParsedBody();    
        $pages->username = $datapost['Username'];
        $pages->token = $datapost['Token'];
        $pages->title = $datapost['Title'];
        $pages->image = $datapost['Image'];
        $pages->description = $datapost['Description'];
        $pages->content = $datapost['Content'];
        $pages->tags = $datapost['Tags'];
        $pages->pageid = $datapost['PageID'];
        $body = $response->getBody();
        $body->write($pages->updateDraftPage());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('Content'))
        ->add(new ValidateParam('Tags','0-500'))
        ->add(new ValidateParam(['Image','Description'],'0-250'))
        ->add(new ValidateParam(['PageID'],'1-11','numeric'))
        ->add(new ValidateParam(['Token','Title'],'1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));

    // POST api to delete page
    $app->post('/page/data/delete', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $datapost = $request->getParsedBody();    
        $pages->pageid = $datapost['PageID'];
        $pages->username = $datapost['Username'];
        $pages->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($pages->deletePage());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam('PageID','1-11','numeric'))
        ->add(new ValidateParam('Token','1-250','required'))
        ->add(new ValidateParam('Username','1-50','required'));

    // GET api to show all data page pagination registered user
    $app->get('/page/data/search/{username}/{token}/{page}/{itemsperpage}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $pages->username = $request->getAttribute('username');
        $pages->token = $request->getAttribute('token');
        $pages->page = $request->getAttribute('page');
        $pages->itemsPerPage = $request->getAttribute('itemsperpage');
        $body = $response->getBody();
        $body->write($pages->searchPageAsPagination());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParamURL('query'));

    // GET api to show all data status page
    $app->get('/page/data/status/{token}', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($pages->showOptionRelease());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show single data page registered user
    $app->get('/page/data/read/{pageid}/{username}/{token}', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->username = $request->getAttribute('username');
        $pages->token = $request->getAttribute('token');
        $pages->pageid = $request->getAttribute('pageid');
        $body = $response->getBody();
        $body->write($pages->showSinglePage());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show single data page public
    $app->map(['GET','OPTIONS'],'/page/data/public/read/{pageid}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->pageid = $request->getAttribute('pageid');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(3600,["apikey","lang"])){
            $datajson = SimpleCache::load(["apikey","lang"]);
        } else {
            $datajson = SimpleCache::save($pages->showSinglePagePublic(),["apikey","lang"],null,3600);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ValidateParamURL('lang','0-2'))
        ->add(new ApiKey);

    // GET api to show all data page pagination public
    $app->map(['GET','OPTIONS'],'/page/data/public/search/{page}/{itemsperpage}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $pages->page = $request->getAttribute('page');
        $pages->itemsPerPage = $request->getAttribute('itemsperpage');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(3600,["apikey","query","lang"])){
            $datajson = SimpleCache::load(["apikey","query","lang"]);
        } else {
            $datajson = SimpleCache::save($pages->searchPageAsPaginationPublic(),["apikey","query","lang"],null,3600);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ValidateParamURL('lang','0-2'))
        ->add(new ValidateParamURL('query'))
        ->add(new ApiKey);

    // GET api to show all data published page pagination public
    $app->map(['GET','OPTIONS'],'/page/data/public/published/{page}/{itemsperpage}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->page = $request->getAttribute('page');
        $pages->itemsPerPage = $request->getAttribute('itemsperpage');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(3600,["apikey","lang"])){
            $datajson = SimpleCache::load(["apikey","lang"]);
        } else {
            $datajson = SimpleCache::save($pages->showPublishPageAsPaginationPublic(),["apikey","lang"],null,3600);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ValidateParamURL('lang','0-2'))->add(new ApiKey);

    // GET api to show all data published page asc or desc pagination public
    $app->map(['GET','OPTIONS'],'/page/data/public/published/{page}/{itemsperpage}/{sort}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->page = $request->getAttribute('page');
        $pages->itemsPerPage = $request->getAttribute('itemsperpage');
        $pages->sort = $request->getAttribute('sort');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(3600,["apikey","lang"])){
            $datajson = SimpleCache::load(["apikey","lang"]);
        } else {
            $datajson = SimpleCache::save($pages->showPublishPageAsPaginationPublic(),["apikey","lang"],null,3600);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ValidateParamURL('lang','0-2'))->add(new ApiKey);

    // GET api to update data view page
    $app->map(['GET','OPTIONS'],'/page/data/view/{pageid}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->pageid = $request->getAttribute('pageid');
        $body = $response->getBody();
        $body->write($pages->updateViewPage());
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ApiKey);

    // GET api to get all data page for statistic purpose
    $app->get('/page/stats/data/summary/{username}/{token}', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->token = $request->getAttribute('token');
        $pages->username = $request->getAttribute('username');
        $body = $response->getBody();
        $body->write($pages->statPageSummary());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to get all data page for statistic chart purpose
    $app->get('/page/stats/data/chart/{year}/{username}/{token}', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->token = $request->getAttribute('token');
        $pages->username = $request->getAttribute('username');
        $pages->year = $request->getAttribute('year');
        $body = $response->getBody();
        $body->write($pages->statPageYear());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to show all data published page written by asc or desc pagination
    $app->get('/page/data/written/{username}/{token}/{user}/{page}/{itemsperpage}/{sort}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $pages->username = $request->getAttribute('username');
        $pages->token = $request->getAttribute('token');
        $pages->user = $request->getAttribute('user');
        $pages->page = $request->getAttribute('page');
        $pages->itemsPerPage = $request->getAttribute('itemsperpage');
        $pages->sort = $request->getAttribute('sort');
        $body = $response->getBody();
        $body->write($pages->showPageWrittenByAsPagination());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParamURL('query'));
    
    // GET api to show all data published page written by asc or desc pagination public
    $app->map(['GET','OPTIONS'],'/page/data/written/public/{user}/{page}/{itemsperpage}/{sort}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->search = filter_var((empty($_GET['query'])?'':$_GET['query']),FILTER_SANITIZE_STRING);
        $pages->user = $request->getAttribute('user');
        $pages->page = $request->getAttribute('page');
        $pages->itemsPerPage = $request->getAttribute('itemsperpage');
        $pages->sort = $request->getAttribute('sort');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(3600,["apikey","lang","query"])){
            $datajson = SimpleCache::load(["apikey","lang","query"]);
        } else {
            $datajson = SimpleCache::save($pages->showPageWrittenByAsPaginationPublic(),["apikey","lang","query"],null,3600);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ValidateParamURL('query'))
        ->add(new ValidateParamURL('lang','0-2'))
        ->add(new ApiKey);

    // GET api to get all data trending page
    $app->get('/page/taxonomy/page/all/{limit}/{username}/{token}', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->token = $request->getAttribute('token');
        $pages->username = $request->getAttribute('username');
        $pages->limit = $request->getAttribute('limit');
        $body = $response->getBody();
        $body->write($pages->showAllTrendingPage());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to get seasonal data trending page
    $app->get('/page/taxonomy/page/seasonal/{limit}/{username}/{token}', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->token = $request->getAttribute('token');
        $pages->username = $request->getAttribute('username');
        $pages->limit = $request->getAttribute('limit');
        $body = $response->getBody();
        $body->write($pages->showSeasonalTrendingPage());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to get all data trending page for public
    $app->get('/page/taxonomy/page/all/{limit}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->token = $request->getAttribute('token');
        $pages->username = $request->getAttribute('username');
        $pages->limit = $request->getAttribute('limit');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(21600,["apikey","lang"])){
            $datajson = SimpleCache::load(["apikey","lang"]);
        } else {
            $datajson = SimpleCache::save($pages->showAllTrendingPagePublic(),["apikey","lang"],null,21600);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ValidateParamURL('lang','0-2'))
        ->add(new ApiKey);

    // GET api to get seasonal data trending page for public
    $app->get('/page/taxonomy/page/seasonal/{limit}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->token = $request->getAttribute('token');
        $pages->username = $request->getAttribute('username');
        $pages->limit = $request->getAttribute('limit');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(3600,["apikey","lang"])){
            $datajson = SimpleCache::load(["apikey","lang"]);
        } else {
            $datajson = SimpleCache::save($pages->showSeasonalTrendingPagePublic(),["apikey","lang"],null,3600);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ValidateParamURL('lang','0-2'))
        ->add(new ApiKey);

    // GET api to get all data trending tags page
    $app->get('/page/taxonomy/tags/all/{limit}/{username}/{token}', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->token = $request->getAttribute('token');
        $pages->username = $request->getAttribute('username');
        $pages->limit = $request->getAttribute('limit');
        $body = $response->getBody();
        $body->write($pages->showAllTrendingTags());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to get seasonal data trending tags page
    $app->get('/page/taxonomy/tags/seasonal/{limit}/{username}/{token}', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->token = $request->getAttribute('token');
        $pages->username = $request->getAttribute('username');
        $pages->limit = $request->getAttribute('limit');
        $body = $response->getBody();
        $body->write($pages->showSeasonalTrendingTags());
        return classes\Cors::modify($response,$body,200);
    });

    // GET api to get all data trending tags page for public
    $app->get('/page/taxonomy/tags/all/{limit}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->token = $request->getAttribute('token');
        $pages->username = $request->getAttribute('username');
        $pages->limit = $request->getAttribute('limit');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(21600,["apikey","lang"])){
            $datajson = SimpleCache::load(["apikey","lang"]);
        } else {
            $datajson = SimpleCache::save($pages->showAllTrendingTagsPublic(),["apikey","lang"],null,21600);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ValidateParamURL('lang','0-2'))
        ->add(new ApiKey);

    // GET api to get seasonal data trending tags page for public
    $app->get('/page/taxonomy/tags/seasonal/{limit}/', function (Request $request, Response $response) {
        $pages = new Pages($this->db);
        $pages->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $pages->token = $request->getAttribute('token');
        $pages->username = $request->getAttribute('username');
        $pages->limit = $request->getAttribute('limit');
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        if (SimpleCache::isCached(3600,["apikey","lang"])){
            $datajson = SimpleCache::load(["apikey","lang"]);
        } else {
            $datajson = SimpleCache::save($pages->showSeasonalTrendingTagsPublic(),["apikey","lang"],null,3600);
        }
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ValidateParamURL('lang','0-2'))
        ->add(new ApiKey);