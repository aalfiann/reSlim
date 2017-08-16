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

        var $version = '1.3.0';

        private static $instance;
        
        function __construct() {
            require 'config.php';
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
                    echo self::getMessage('success','Process Register Successfully!');
                } else {
                    echo self::getMessage('danger','Process Register Failed!',$data->{'message'});    
                }
            } else {
                echo self::getMessage('danger','Process Register Failed!','Can not connected to the server!');
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
                    echo self::getMessage('success','Process Update Successfuly!');
                } else {
                    echo self::getMessage('danger','Process Update Failed!',$data->{'message'});
                }
            } else {
                echo self::getMessage('danger','Process Update Failed!','Can not connected to the server!');
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
						session_start();
                        $_SESSION['username'] = $post_array['Username'];
						$_SESSION['token'] = $data->{'token'};
					} else {
						setcookie('username', $post_array['Username'], time() + (3600 * 168), "/", NULL); // expired = 7 days
				  		setcookie('token', $data->{'token'}, time() + (3600 * 168), "/", NULL); // expired = 7 hari
					}
					header("Location: ".self::getInstance()->basepath."/index.php");
                } else {
                    echo self::getMessage('danger','Process Login Failed!',$data->{'message'});
                }
            } else {
                echo self::getMessage('danger','Process Login Failed!','Can not connected to the server!');
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
        	if (!isset($_SESSION['username'])) session_start();
                if (self::revokeToken($_SESSION['username'],$_SESSION['token'])){
                    unset($_SESSION['username']);
                	unset($_SESSION['token']);
                }
        	// unset cookies
        	if (isset($_SERVER['HTTP_COOKIE'])) {
                if (self::revokeToken($_COOKIE['username'],$_COOKIE['token'])){
                    setcookie('username', '', time()-1000, '/');
                    setcookie('token', '', time()-1000, '/');
                }
            	$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            	    foreach($cookies as $cookie) {
                	    $parts = explode('=', $cookie);
                    	$name = trim($parts[0]);
                    	setcookie($name, '', time()-1000);
                        setcookie($name, '', time()-1000, '/');
                	}
	        }
        	header("Location: ".self::getInstance()->basepath."/modul-login.php?m=1");
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
                        'Subject' => 'Request reset password',
                        'Message' => '<html><body><p>You have already requested to reset password.<br /><br />
                        Here is the link to reset: <a href="'.$linkverify.'" target="_blank"><b>'.$linkverify.'</b></a>.<br /><br />
                        
                        Just ignore this email if You don\'t want to reset password. Link will be expired 3days from now.<br /><br /><br />
                        Thank You<br />
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
                        echo self::getMessage('success','Request reset password hasbeen sent to your email!','If not, try to resend again later.');
                    } catch (Exception $e) {
                        echo self::getMessage('danger','Process Forgot Password Failed!',$e->getMessage());
                    }
                } else {
                    echo self::getMessage('danger','Process Forgot Password Failed!',$data->{'message'});
                }
            } else {
                echo self::getMessage('danger','Process Forgot Password Failed!','Can not connected to the server!');
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
                    echo self::getMessage('success','Process Change Password Successfully!');
                } else {
                    echo self::getMessage('danger','Process Change Password Failed!',$data->{'message'});
                }
            } else {
                echo self::getMessage('danger','Process Change Password Failed!','Can not connected to the server!');
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
                    echo self::getMessage('success','Process Upload Successfuly!');
                } else {
                    echo self::getMessage('danger','Process Upload Failed!',$data->{'message'});
                }
            } else {
                echo self::getMessage('danger','Process Upload Failed!','Can not connected to the server!');
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
                    echo self::getMessage('success','Process Update Successfuly!');
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger','Process Update Failed!',$data->{'message'});
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger','Process Update Failed!','Can not connected to the server!');
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
                    echo self::getMessage('success','Process Delete Successfuly!');
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger','Process Delete Failed!',$data->{'message'});
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger','Process Delete Failed!','Can not connected to the server!');
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
                echo self::getMessage('success','The message is successfully sent!');
            } catch (Exception $e) {
                echo self::getMessage('danger','The message is failed to sent!','Please try again later!');
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
                    echo self::getMessage('success','Process Add new API Keys Successfully!');
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger','Process Add new API Keys Failed!',$data->{'message'});    
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger','Process Add new API Keys Failed!','Can not connected to the server!');
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
                    echo self::getMessage('success','Process Update Successfuly!');
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger','Process Update Failed!',$data->{'message'});
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger','Process Update Failed!','Can not connected to the server!');
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
                    echo self::getMessage('success','Process Delete Successfuly!');
                    echo '</div>';
                } else {
                    echo '<div class="col-lg-12">';
                    echo self::getMessage('danger','Process Delete Failed!',$data->{'message'});
                    echo '</div>';
                }
            } else {
                echo '<div class="col-lg-12">';
                echo self::getMessage('danger','Process Delete Failed!','Can not connected to the server!');
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
		 * Reload Page
         *
         * @param $timeout = The page will be redirected when time is out. Default is 2000 miliseconds. 
         * @return reload self page
		 */
        public static function reloadPage($timeout=2000)
        {
            return '<script>setTimeout(function() {window.location.href=window.location.href}, '.$timeout.')</script>';
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
            echo self::getMessage('success','Settings hasbeen changed!','This page will automatically refresh at 2 seconds...');
            echo self::reloadPage();
        }

}
