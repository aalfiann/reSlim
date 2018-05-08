<?php
/**
 * This class is a part of reSlim project
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes;
use \classes\JSON as JSON;
use PDO;
    /**
     * A class for user sending email in reSlim
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
	class Mailer {
		
		protected $mailer,$db,$host,$smtpDebug,$smtpAutoTLS,$smtpAuth,$smtpSecure,$smtpPort,$username,$password,$defaultNameFrom;

        /**
         * @var $setFrom = The email address of sender. Example: youremail@gmail.com
         * @var $setFromName = The name of sender. Example: Your name here
         * @var $wordWrap = Auto wrap the email body. Default is 50.
         * @var $addAddress = The email address of destination. Example: someone1@gmail.com,someone2@gmail.com,someone3@gmail.com
         * @var $addCC = Add more cc email address. Example: someone1@gmail.com,someone2@gmail.com,someone3@gmail.com
         * @var $addBCC = Add more bcc email address. Example: someone1@gmail.com,someone2@gmail.com,someone3@gmail.com
         * @var $addAttachment = Add more attachment in email. Example: C:\book.doc,C:\image.jpg,C:\video.mp4
         * @var $subject = The subject of email
         * @var $body = The body of email
         * @var $isHtml = Email will using plain text or html. Default is true.
         */
        var $setFrom,$setFromName,$wordWrap=50,$addAddress,$addCC,$addBCC,$addAttachment,$subject,$body,$isHtml=true;

        function __construct($mailer,$db=null) {
            require '../config.php';
            $c = $config['smtp'];
            $this->host = $c['host'];
            $this->smtpAuth = $c['auth'];
            $this->smtpSecure = $c['secure'];
            $this->username = $c['username'];
            $this->password = $c['password'];
            $this->smtpPort = $c['port'];
            $this->smtpAutoTLS = $c['autotls'];
            $this->smtpDebug = $c['debug'];
            $this->defaultNameFrom = $c['defaultnamefrom'];
            $this->mailer = $mailer;
            if (!empty($db)) 
	        {
    	        $this->db = $db;
        	}
		}

        /** 
		 * Send Email
		 * @return result process in json encoded data
		 */
        public function send(){
            $this->mailer->isSMTP();                                      // Set mailer to use SMTP
            $this->mailer->Host = $this->host;                            // Specify main and backup SMTP servers
            $this->mailer->SMTPAuth = $this->smtpAuth;                    // Enable SMTP authentication
            $this->mailer->Username = $this->username;                    // SMTP username
            $this->mailer->Password = $this->password;                    // SMTP password
            $this->mailer->SMTPSecure = $this->smtpSecure;                // Enable TLS encryption, `ssl` also accepted
            $this->mailer->Port = $this->smtpPort;                        // TCP port to connect to
            $this->mailer->SMTPAutoTLS = $this->smtpAutoTLS;              // Set TLS encryption automatically
            $this->mailer->SMTPDebug = $this->smtpDebug;                  // Show debug information
            $this->mailer->SMTPOptions = array(
  								  	'ssl' => array(
									'verify_peer' => false,
									'verify_peer_name' => false,
									'allow_self_signed' => true
								    )
								);

            $this->mailer->WordWrap = $this->wordWrap;
            $this->mailer->setFrom(filter_var((empty($this->setFrom)?$this->username:$this->setFrom), FILTER_SANITIZE_EMAIL), filter_var((empty($this->setFromName)?$this->defaultNameFrom:$this->setFromName), FILTER_SANITIZE_STRING));
            $this->mailer->addReplyTo(filter_var((empty($this->setFrom)?$this->username:$this->setFrom), FILTER_SANITIZE_EMAIL), filter_var((empty($this->setFromName)?$this->defaultNameFrom:$this->setFromName), FILTER_SANITIZE_STRING));
            
            if (!empty($this->addAddress)){
                $address = preg_split( "/[;,#]/", preg_replace('/\s+/', '', $this->addAddress) );
                foreach ($address as $value) {
                    if(!empty($value)){
                        $this->mailer->addAddress(filter_var($value, FILTER_SANITIZE_EMAIL));
                    }
                }
            }

            if (!empty($this->addCC)){
                $cc = preg_split( "/[;,#]/", preg_replace('/\s+/', '', $this->addCC) );
                foreach ($cc as $value) {
                    if(!empty($value)){
                        $this->mailer->addCC(filter_var($value, FILTER_SANITIZE_EMAIL));
                    }
                }
            }
            
            if (!empty($this->addBCC)){
                $bcc = preg_split( "/[;,#]/", preg_replace('/\s+/', '', $this->addBCC) );
                foreach ($bcc as $value) {
                    if(!empty($value)){
                        $this->mailer->addBCC(filter_var($value, FILTER_SANITIZE_EMAIL));
                    }
                }
            }

            if (!empty($this->addAttachment)){
                $attachment = preg_split( "/[;,#]/", preg_replace('/\s+/', '', $this->addAttachment) );
                foreach ($attachment as $value) {
                    if(!empty($value)){
                        $this->mailer->addAttachment(filter_var($value, FILTER_SANITIZE_STRING));
                    }
                }
            }

            $this->mailer->Subject = filter_var($this->subject, FILTER_SANITIZE_STRING);
            $this->mailer->Body = $this->body;
            $this->mailer->AltBody = filter_var($this->body, FILTER_SANITIZE_STRING);
            $this->mailer->isHTML($this->isHtml);
            
            if(!$this->mailer->send()) {
                $data = [
                    'status' => 'error',
            		'code' => '0',
            		'message' => $this->mailer->ErrorInfo
                ];
            } else {
                $data = [
                    'status' => 'success',
            		'code' => 'RS105',
            		'message' => CustomHandlers::getreSlimMessage('RS105')
                ];
            }
            return JSON::encode($data,true);
        }
    }