<?php 
    /**
     * A class for core example reSlim project
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
    class Core {

        // Set title website
        var $title;

        // Set email address website
        var $email;
        
        // Set base path example project
        var $basepath;

        // Set base api reslim
        var $api;

        // Set api keys
        var $apikey;

        // Set language
        var $setlang = 'en';
        var $datalang;

        // Set Run Cache Files
        var $runcache = true;
        var $pathcache = 'cache-files';
        var $minifycache = true;

        var $version = '1.7.0';

        private static $instance;
        
        function __construct() {
            require 'config.php';
            $langs = glob(dirname(__FILE__) .'/language/*.'.$this->setlang.'.php');
            foreach ($langs as $langname) {
                require $langname;
            }
            $this->datalang = $lang;                // set language
            $this->title = $config['title'];
            $this->email = $config['email'];
            $this->basepath = $config['basepath'];
            $this->api = $config['api'];
            $this->apikey = $config['apikey'];
		}

        public static function getInstance()
        {
            if ( is_null( self::$instance ) )
            {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public static function lang($key){
            return self::getInstance()->datalang[$key];
        }
        
        // LIBRARY USER MANAGEMENT AND AUTHENTICATION======================================================================

        /**
		 * Get Message
         *
         * @param $type = the tpe of message in bootstrap. Example: success,warning,danger,info,primary,default
         * @param $primaryMessage = Message to show.
         * @param $secondaryMessage = Additional message to show. This is not required, so default is null.
		 * @return string with message data
		 */
        public static function getMessage($type,$primaryMessage,$secondaryMessage=null){
            return '<div class="alert alert-'.$type.'" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>'.$primaryMessage.'</strong> '.$secondaryMessage.'
                        </div>';
        }

        /**
		 * CURL Post Request
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function execPostRequest($url,$post_array){
        
            if(empty($url)){ return false;}
            //build query
            $fields_string =http_build_query($post_array);
        
            //open connection
            $ch = curl_init();
        
            ////curl parameter set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        
            //execute post
            $result = curl_exec($ch);
        
            //close connection
            curl_close($ch);
        
            return $result;
        }

        /**
		 * CURL Post Upload Request Multipart Data
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function execPostUploadRequest($url,$post_array){
        
            if(empty($url)){ return false;}
            
            //open connection
            $ch = curl_init();
        
            ////curl parameter set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_USERAGENT,'Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15');
            curl_setopt($ch, CURLOPT_HTTPHEADER,array('User-Agent: Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15','Referer: '.self::getInstance()->api,'Content-Type: multipart/form-data'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // stop verifying certificate
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$post_array);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
            //execute post
            $result = curl_exec($ch);
            if ($result === false){
                $result = curl_error($ch);
            };
        
            //close connection
            curl_close($ch);
        
            return $result;
        }

        /**
		 * CURL Get Request
         *
         * @param $url = The url api to get the request
		 * @return result json encoded data
		 */
        public static function execGetRequest($url){
            //open connection
	    	$ch = curl_init($url);
            
            //curl parameter
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
            
            //execute post
		    $data = curl_exec($ch);
            
            //close connection
    		curl_close($ch);

	    	return $data;
    	}

        /**
		 * Verify API Token
         *
         * @param $token = Your token that generated from api server after login
		 * @return boolean true / false
		 */
        public static function verifyToken($token){
            $result = false;
            $data = json_decode(self::execGetRequest(self::getInstance()->api.'/user/verify/'.$token));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    $result = true;
                }
            }
            return $result;
        }

        /**
		 * Get Role by API Token
         *
         * @param $token = Your token that generated from api server after login
		 * @return integer
		 */
        public static function getRole($token){
            $result = 0;
            $data = json_decode(self::execGetRequest(self::getInstance()->api.'/user/scope/'.$token));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    $result = $data->{'role'};
                }
            }
            return $result;
        }

        /**
		 * Revoke API Token
         *
         * @param $username = Your username
         * @param $token = Your token that generated from api server after login
		 * @return boolean true / false
		 */
        public static function revokeToken($username,$token){
            $result = false;
            $post_array = array(
                'Username' => urlencode($username),
                'Token' => urlencode($token)
            );
            $url = self::getInstance()->api.'/user/logout';
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    $result = true;
                }
            }
            return $result;
        }

        /**
		 * Process Register
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function register($url,$post_array){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo self::getMessage('success',self::lang('core_register_success'));
                } else {
                    echo self::getMessage('danger',self::lang('core_register_failed'),$data->{'message'});    
                }
            } else {
                echo self::getMessage('danger',self::lang('core_register_failed'),self::lang('core_not_connected'));
            }
	    }

        /**
		 * Process Update
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function update($url,$post_array){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo self::getMessage('success',self::lang('core_update_success'));
                } else {
                    echo self::getMessage('danger',self::lang('core_update_failed'),$data->{'message'});
                }
            } else {
                echo self::getMessage('danger',self::lang('core_update_failed'),self::lang('core_not_connected'));
            }
	    }

        /**
		 * Process Login
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function login($url,$post_array){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    if ($post_array['Rememberme'] == "on"){
						setcookie('username', $post_array['Username'], time() + (3600 * 168), "/", NULL); // expired = 7 days
				  		setcookie('token', $data->{'token'}, time() + (3600 * 168), "/", NULL); // expired = 7 days
					} else {
                        session_start();
                        $_SESSION['username'] = $post_array['Username'];
						$_SESSION['token'] = $data->{'token'};
					}
					header("Location: ".self::getInstance()->basepath."/index.php");
                } else {
                    echo self::getMessage('danger',self::lang('core_login_failed'),$data->{'message'});
                }
            } else {
                echo self::getMessage('danger',self::lang('core_login_failed'),self::lang('core_not_connected'));
            }
	    }

        /**
		 * Process Logout
         *
         * @return redirect to login page
		 */
        public static function logout()
        {
            //Unset SESSION
        	session_start();
            if(isset($_SESSION['username']) && !empty($_SESSION['username'])) {
                if (self::revokeToken($_SESSION['username'],$_SESSION['token'])){
                    unset($_SESSION['username']);
                    unset($_SESSION['token']);
                }
            }
        	// unset cookies
        	if (isset($_SERVER['HTTP_COOKIE'])) {
                if(isset($_COOKIE['username']) && !empty($_COOKIE['username'])) {
                    if (self::revokeToken($_COOKIE['username'],$_COOKIE['token'])){
                        setcookie('username', '', time()-1000, '/');
                        setcookie('token', '', time()-1000, '/');
                    }
                }
            	$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            	    foreach($cookies as $cookie) {
                	    $parts = explode('=', $cookie);
                    	$name = trim($parts[0]);
                    	setcookie($name, '', time()-1000);
                        setcookie($name, '', time()-1000, '/');
                	}
            }
        	header("Location: ".self::getInstance()->basepath."/modul-login.php");
        }

        /**
		 * Process Forgot Password
         *
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function forgotPassword($post_array){
            $data = json_decode(self::execPostRequest(self::getInstance()->api.'/user/forgotpassword',$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    $linkverify = self::getInstance()->basepath.'/modul-verify.php?passkey='.$data->{'passkey'};
                    $email_array = array(
                        'To' => $post_array['Email'],
                        'Subject' => self::lang('core_mail_reset_password1'),
                        'Message' => '<html><body><p>'.self::lang('core_mail_reset_password2').'<br /><br />
                        '.self::lang('core_mail_reset_password3').' <a href="'.$linkverify.'" target="_blank"><b>'.$linkverify.'</b></a>.<br /><br />
                        
                        '.self::lang('core_mail_reset_password4').'<br /><br /><br />
                        '.self::lang('core_mail_reset_password5').'<br />
                        '.self::getInstance()->title.'</p></body></html>',
                        'Html' => 'true',
                        'From' => '',
                        'FromName' => '',
                        'CC' => '',
                        'BCC' => '',
                        'Attachment' => ''
                    );
                    try {
                        $sendemail = json_decode(self::execPostRequest(self::getInstance()->api.'/mail/send',$email_array));
                        echo self::getMessage('success',self::lang('core_reset_password_success1'),self::lang('core_reset_password_success2'));
                    } catch (Exception $e) {
                        echo self::getMessage('danger',self::lang('core_reset_password_failed'),$e->getMessage());
                    }
                } else {
                    echo self::getMessage('danger',self::lang('core_reset_password_failed'),$data->{'message'});
                }
            } else {
                echo self::getMessage('danger',self::lang('core_reset_password_failed'),self::lang('core_not_connected'));
            }
	    }

        /**
		 * Process Verify Pass Key
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function verifyPassKey($url,$post_array){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo self::getMessage('success',self::lang('core_change_password_success'));
                } else {
                    echo self::getMessage('danger',self::lang('core_change_password_failed'),$data->{'message'});
                }
            } else {
                echo self::getMessage('danger',self::lang('core_change_password_failed'),self::lang('core_not_connected'));
            }
	    }

        /**
		 * Process Upload File
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function uploadFile($url,$post_array){
            $data = json_decode(self::execPostUploadRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo self::getMessage('success',self::lang('core_upload_success'));
                } else {
                    echo self::getMessage('danger',self::lang('core_upload_failed'),$data->{'message'});
                }
            } else {
                echo self::getMessage('danger',self::lang('core_upload_failed'),self::lang('core_not_connected'));
            }
        }
        
        /**
		 * Process Upload File avoid resubmit on refreshed page
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function safeUploadFile($url,$post_array){
            $data = json_decode(self::execPostUploadRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo self::getMessage('success',self::lang('core_upload_success'));
                    echo self::reloadPage(0);
                    exit;
                } else {
                    echo self::getMessage('danger',self::lang('core_upload_failed'),$data->{'message'});
                }
            } else {
                echo self::getMessage('danger',self::lang('core_upload_failed'),self::lang('core_not_connected'));
            }
	    }

        /**
		 * Process Update File
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function updateFile($url,$post_array){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('success',self::lang('core_update_success'));
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger',self::lang('core_update_failed'),$data->{'message'});
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger',self::lang('core_update_failed'),self::lang('core_not_connected'));
                echo '</div>';
            }
	    }

        /**
		 * Process Delete File
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function deleteFile($url,$post_array){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('success',self::lang('core_delete_success'));
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger',self::lang('core_delete_failed'),$data->{'message'});
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger',self::lang('core_delete_failed'),self::lang('core_not_connected'));
                echo '</div>';
            }
	    }

        /**
		 * Process Send Email
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function sendMail($url,$post_array){
            try{
                $data = json_decode(self::execPostRequest($url,$post_array));
                echo self::getMessage('success',self::lang('core_mail_send_success'));
            } catch (Exception $e) {
                echo self::getMessage('danger',self::lang('core_mail_send_failed'),self::lang('core_try_again'));
            }
	    }

        /**
		 * Process Create New API
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function createNewAPI($url,$post_array){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('success',self::lang('core_api_add_success'));
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger',self::lang('core_api_add_failed'),$data->{'message'});    
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger',self::lang('core_api_add_failed'),self::lang('core_not_connected'));
                echo '</div>';
            }
	    }

        /**
		 * Process Update API
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function updateAPI($url,$post_array){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('success',self::lang('core_update_success'));
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger',self::lang('core_update_failed'),$data->{'message'});
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger',self::lang('core_update_failed'),self::lang('core_not_connected'));
                echo '</div>';
            }
	    }

        /**
		 * Process Delete API
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
		 * @return result json encoded data
		 */
	    public static function deleteAPI($url,$post_array){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('success',self::lang('core_delete_success'));
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger',self::lang('core_delete_failed'),$data->{'message'});
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger',self::lang('core_delete_failed'),self::lang('core_not_connected'));
                echo '</div>';
            }
        }
        
        /**
		 * Process Create
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
         * @param $title = Name of the process itself
		 * @return result json encoded data
		 */
	    public static function processCreate($url,$post_array,$title){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('success',self::lang('core_process_add').' '.$title.' '.self::lang('status_success'));
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger',self::lang('core_process_add').' '.$title.' '.self::lang('status_failed'),$data->{'message'});    
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger',self::lang('core_process_add').' '.$title.' '.self::lang('status_failed'),self::lang('core_not_connected'));
                echo '</div>';
            }
	    }

        /**
		 * Process Update
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
         * @param $title = Name of the process itself
		 * @return result json encoded data
		 */
	    public static function processUpdate($url,$post_array,$title){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('success',self::lang('core_process_update').' '.$title.' '.self::lang('status_success'));
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger',self::lang('core_process_update').' '.$title.' '.self::lang('status_failed'),$data->{'message'});
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger',self::lang('core_process_update').' '.$title.' '.self::lang('status_failed'),self::lang('core_not_connected'));
                echo '</div>';
            }
	    }

        /**
		 * Process Delete
         *
         * @param $url = The url api to post the request
         * @param $post_array = Data array to post
         * @param $title = Name of the process itself
		 * @return result json encoded data
		 */
	    public static function processDelete($url,$post_array,$title){
            $data = json_decode(self::execPostRequest($url,$post_array));
            if (!empty($data)){
                if ($data->{'status'} == "success"){
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('success',self::lang('core_process_delete').' '.$title.' '.self::lang('status_success'));
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger',self::lang('core_process_delete').' '.$title.' '.self::lang('status_failed'),$data->{'message'});
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger',self::lang('core_process_delete').' '.$title.' '.self::lang('status_failed'),self::lang('core_not_connected'));
                echo '</div>';
            }
	    }

        /**
		 * Check SESSION, COOKIE and Verify Token
         *
         * @return data array, but if null will be redirect to login page
		 */
        public static function checkSessions()
        {
            // If cookie is not found then check session
            if (!isset($_COOKIE['username']) && !isset($_COOKIE['token'])) 
            {
                session_start();
                // if session is not found then redirect to login page
                if (!isset($_SESSION['username']) && !isset($_SESSION['token']))
                {
                    $out['username'] = null;
                    $out['token'] = null;
                    header("Location: ".self::getInstance()->basepath."/modul-login.php?m=1");
                }
                else
                {
                    if (self::verifyToken($_SESSION['token'])) {
                        $out['username'] = $_SESSION['username'];
            	    	$out['token'] = $_SESSION['token'];
                    } else {
                        $out['username'] = null;
                        $out['token'] = null;
                        header("Location: ".self::getInstance()->basepath."/modul-login.php?m=1");
                    }                     
                }
            }
            else // If there is a cookie then return array
            {
                if (self::verifyToken($_COOKIE['token'])) {
                    $out['username'] = $_COOKIE['username'];
            	    $out['token'] = $_COOKIE['token'];
                } else {
                    $out['username'] = null;
                    $out['token'] = null;
                    header("Location: ".self::getInstance()->basepath."/modul-login.php?m=1");
                }
    	    }
	        return $out;
        }

        /**
		 * Redirect Page Location Header
         *
         * @param $page = The page to redirect
         * @param $timeout = The page will be redirected when time is out. Default is zero 
         * @return redirect page
		 */
        public static function goToPage($page,$timeout=0)
        {
           return header("Refresh:".$timeout.";url= ".self::getInstance()->basepath."/".$page."");
        }

        /**
		 * Redirect Page Location by meta header
         *
         * @param $url = The url to redirect
         * @param $timeout = The page will be redirected when time is out. Default is zero 
         * @return redirect url
		 */
        public static function goToPageMeta($url,$timeout=0)
        {
            return '<meta http-equiv="refresh" content="'.$timeout.';url='.$url.'">';
        }

        /**
		 * Redirect Page Location use javascript
         *
         * @param $url = The url to redirect
         * @param $timeout = The page will be redirected when time is out. Default is 2 seconds. 
         * @return redirect page
		 */
        public static function redirectPage($url,$timeout=2)
        {
            if ($timeout>0){
                $timeout = $timeout * 1000;
                return '<script>setTimeout(function() {window.location.href="'.$url.'"}, '.$timeout.')</script>';
            } else{
                return '<script>window.location.href="'.$url.'";</script>';
            }
            
        }

        /**
		 * Reload Page
         *
         * @param $timeout = The page will be redirected when time is out. Default is 2 seconds. 
         * @return reload self page
		 */
        public static function reloadPage($timeout=2)
        {
            if ($timeout>0){
                $timeout = $timeout * 1000;
                return '<script>setTimeout(function() {window.location.href=window.location.href}, '.$timeout.')</script>';
            } else{
                return '<script>window.location.href=window.location.href;</script>';
            }
        }

        /**
		 * Save Settings
         *
         * @param $post_array = Data array to post
         * @return no return
		 */
        public static function saveSettings($post_array)
        {
            $newcontent = '<?php 
            //Configurations
            $config[\'title\'] = \''.$post_array['Title'].'\'; //Your title website
            $config[\'email\'] = \''.$post_array['Email'].'\'; //Your default email
            $config[\'basepath\'] = \''.$post_array['Basepath'].'\'; //Your folder website
            $config[\'api\'] = \''.$post_array['Api'].'\'; //Your folder rest api
            $config[\'apikey\'] = \''.$post_array['ApiKey'].'\'; //Your api key, you can leave this blank and fill this later';
            $handle = fopen('config.php','w+'); 
				fwrite($handle,$newcontent); 
				fclose($handle); 
            echo self::getMessage('success',self::lang('core_settings_changed'),self::lang('core_auto_refresh'));
            echo self::reloadPage();
        }

        /**
         * Auto cut off long text
         *
         * @param $string = Data text
         * @param $limitLength = Limit value to be auto cut. Default value is 50 chars
         * @param $replaceValue = Value to replacing the cutted text. Default value is ...
         * @return string cutted text
         */
        public static function cutLongText($string,$limitLength=50,$replaceValue='...'){
            return (strlen($string) > $limitLength) ? substr($string, 0, $limitLength) . $replaceValue : $string;
        }

        /**
         * Time to seconds converter
         *
         * @param $str_time = String value must time only. Example: 00:23:45
         * @return integer seconds
         */
        public static function convertTimeToSeconds($str_time){
            $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
            sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
            return $time_seconds;
        }

        /**
         * Slug converter
         *
         * @param $text = Text value
         * @return string
         */
        public static function convertToSlug($text){
            // replace non letter or digits by -
            $text = preg_replace('~[^\pL\d]+~u', '-', $text);

            // transliterate
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

            // remove unwanted characters
            $text = preg_replace('~[^-\w]+~', '', $text);

            // trim
            $text = trim($text, '-');

            // remove duplicate -
            $text = preg_replace('~-+~', '-', $text);

            // lowercase
            $text = strtolower($text);

            if (empty($text)) {
                return 'n-a';
            }

            return $text;
        }

        /**
         * Determine Server is use SSL or not. This support for full ssl and flexible ssl
         *
         * @return boolean
         */
        public static function isHttpsButtflare() {
            $whitelist = array(
                '127.0.0.1',
                '::1'
            );
            
            if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
                if (!empty($_SERVER['HTTP_CF_VISITOR'])){
                    return isset($_SERVER['HTTPS']) ||
                    ($visitor = json_decode($_SERVER['HTTP_CF_VISITOR'])) &&
                    $visitor->scheme == 'https';
                } else {
                    return isset($_SERVER['HTTPS']);
                }
            } else {
                return 0;
            }            
        }

        /**
         * This will minify output buffer, using this will minify about 80% of your page
         * Consider about speed for pregmatch in output buffer, it will require high memory in PHP and affect memory cpu in your server, 
         * so we make not high maximum minify for smallest risk failure in output buffering
         * 
         * Please note:
         * - Best for HTML, Inline CSS or JS maybe will broken (always check your console)
         * - Still not support to strip inline comment javascript like //this is comment string here...
         * - This is not silver bullet, don't use this if broke your inline css or javascript
         */
        public static function sanitize_output($buffer) {
    
            $search = array(
                // Minify contents from buffer before write to file.
                '/\>[^\S ]+/s',                                 // strip whitespaces after tags, except space [^1]
                '/[^\S ]+\</s',                                 // strip whitespaces before tags, except space [^2]
                '/<!--(.|\s)*?-->/',                            // Remove HTML comments [^3]
                '#\s*([!%&*\=+\)\{;\/])\s*#',                   // Remove white-space(s) around punctuation(s) [^4]
                '#[;,]([\]\}])#',                               // Remove the last semi-colon and comma [^5]
                '#\btrue\b#', '#false\b#', '#return\s+#',       // Replace `true` with `!0` and `false` with `!1` [^6]
                '/\}[^\S ]+/s',                                 // strip whitespaces after tags }, except space [^7]
                '/[^\S ]+\}/s',                                 // strip whitespaces before tags }, except space [^8]
                '/\/\*(.|\s)*?\*\//',                           // Remove Javascript comments only /* */ or /** */ [^9]
                '/[\n+\r+\t+]+\</s',                            // Strip inline break before tags < [^10]
                '/\>[\n+\r+\t+]+/s',                            // Strip inline break after tags > [^11]
                '/[\n+\r+\t+]+\}/s',                            // Strip inline break before tags } [^12]
                '/\}[\n+\r+\t+]+/s',                            // Strip inline break after tags } [^13]
                '/[\n+\r+\t+]+\{/s',                            // Strip inline break before tags { [^14]
                '/\{[\n+\r+\t+]+/s',                            // Strip inline break after tags { [^15]  
                '/[\n+\r+\t+]+\,/s',                            // Strip inline break before tags , [^16]
                '/\,[\n+\r+\t+]+/s'                             // strip inline break after tags , [^17]
            );
        
            $replace = array(
                '>',                    // [^1]
                '<',                    // [^2]
                '',                     // [^3]
                '$1',                   // [^4]
                '$1',                   // [^5]
                '!0', '!1', 'return ',  // [^6]
                '}',                    // [^7]
                '}',                    // [^8]
                '',                     // [^9]
                '<',                    // [^10]
                '>',                    // [^11]
                '}',                    // [^12]
                '}',                    // [^13]
                '{',                    // [^14]
                '{',                    // [^15]
                ',',                    // [^16]
                ','                     // [^17]
            );
        
            $buffer = preg_replace($search, $replace, $buffer);
        
            return $buffer;
        }

        /**
         * Start to writing cache as files (traditional way of caching)
         * Please note: 
         * - This script will not cache for url with parameter
         * - Do not use this for high size of page, better to use in maximum 50Kb of html page
         * - PHP Memory limit needed is minimum 1024M
         * - Better to use Harddisk with SSD feature to write file faster
         * - Sometimes ob_start will fail and you get blank page
         * 
         * @param $age = set how long file to be expired
         * @param $xmlformat = determine if page is xml formatted. Default is false
         * @param $match = set where spesific url should be cache in your php
         */
        public static function startCache($age=18000,$xmlformat=false,$match=null){
            if (self::getInstance()->runcache){
                //Create folder if it doesn't already exist
                $path = self::getInstance()->pathcache;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                // Detect url file
                $url = trim ( $_SERVER['REQUEST_URI'] ,'/' );
                // Build path file
                $link_array = explode('/',$url);
                $file = "";
                foreach ($link_array as $key){
                    $file .= $key.'-';
                }
                $file = substr($file, 0, -1);
                // Dont cache url using parameter           
                if(!empty($file) && strpos($file,'?') === false ){
                    // is using spesific url to cache
                    if (!empty($match)){
                        // if url match then do start
                        if (strpos($url,$match) !== false){
                            // define the path and name of cached file
        	                $cachefile = $path.'/'.$file.'.cache';
                            // define how long we want to keep the file in seconds. I set mine to 5 hours.
                            $cachetime = $age;
                            // Check if the cached file is still fresh. If it is, serve it up and exit.
                            if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
                                // we use file_get_contents instead readfile because readfile using output buffer which is not good in high simultaneous traffic
                                echo file_get_contents($cachefile);
                                exit;
                            }
                	        // if there is either no file OR the file to too old, render the page and capture the HTML.
                            ob_start();
                            if ($xmlformat == false){
                                echo '<!-- This page is cached version created at: '.date('Y-m-d H:i:s').' -->';
                            }
                        }
                    } else {
                        // define the path and name of cached file
    	                $cachefile = $path.'/'.$file.'.cache';
                        // define how long we want to keep the file in seconds. I set mine to 5 hours.
                        $cachetime = $age;
                        // Check if the cached file is still fresh. If it is, serve it up and exit.
                        if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
                            // we use file_get_contents instead readfile because readfile using output buffer which is not good in high simultaneous traffic
                            echo file_get_contents($cachefile);
                            exit;
                        }
            	        // if there is either no file OR the file to too old, render the page and capture the HTML.
                        ob_start();
                        if ($xmlformat == false){
                            echo '<!-- This page is cached version created at: '.date('Y-m-d H:i:s').' -->';
                        }
                    }
                }
            }
        }

        /**
         * This function will write the cache, so put this on very bottom on your script
         * 
         * @param $match = set where spesific url should be cache in your php
         */
        public static function endCache($match=null){
            if (self::getInstance()->runcache){
                //Determine path
                $path = self::getInstance()->pathcache;
                // Detect url file
                $url = trim ( $_SERVER['REQUEST_URI'] ,'/' );
                // Build path file
                $link_array = explode('/',$url);
                $file = "";
                foreach ($link_array as $key){
                    $file .= $key.'-';
                }
                $file = substr($file, 0, -1);
                // Dont cache url using parameter
                if(!empty($file) && strpos($file,'?') === false ){
                    // is using spesific url to cache
                    if (!empty($match)){
                        // if url match then do start
                        if (strpos($url,$match) !== false){
                            // define the path and name of cached file
        	                $cachefile = $path.'/'.$file.'.cache';
                            // We're done! Save the cached content to a file
	                        $fp = fopen($cachefile, 'w');
            	            if (self::getInstance()->minifycache){
                                fwrite($fp, self::sanitize_output(ob_get_contents()));
                            } else {
                                fwrite($fp, ob_get_contents());
                            }
                            fclose($fp);
                            // finally send browser output
                            ob_end_flush();
                        }
                    } else {
                        // define the path and name of cached file
    	                $cachefile = $path.'/'.$file.'.cache';
                        // We're done! Save the cached content to a file
	                    $fp = fopen($cachefile, 'w');
    	                if (self::getInstance()->minifycache){
                            fwrite($fp, self::sanitize_output(ob_get_contents()));
                        } else {
                            fwrite($fp, ob_get_contents());
                        }
                        fclose($fp);
                        // finally send browser output
                        ob_end_flush();
                    }
                }
            }
        }

}
