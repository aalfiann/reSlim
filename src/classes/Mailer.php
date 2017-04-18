<?php
/**
 * This class is a part of reSlim project
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes;
use \classes\Mailer as Mailer;
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
		
		protected $mailer,$db,$host,$smtpDebug,$smtpAutoTLS,$smtpAuth,$smtpSecure,$smtpPort,$username,$password;

        var $setFrom,$setFromName,$addReplyTo,$addReplyToName,$addAddress,$addCC,$addBCC,$addAttachment,$subject,$body,$isHtml;

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
            $this->mailer->isHTML($this->isHtml);                         // Set email format to HTML
            $this->mailer->SMTPAutoTLS = $this->smtpAutoTLS;              // Set TLS encryption automatically
            $this->mailer->SMTPDebug = $this->smtpDebug;                  // Show debug information

            if (!empty($this->setFrom)){
                $this->mailer->setFrom($this->setFrom, $this->setFromName);
            }

            if (!empty($this->addReplyTo)){
                $this->mailer->addReplyTo($this->addReplyTo, $this->addReplyToName);
            }

            if (!empty($this->addAddress)){
                $address = preg_split( "/[;,#]/", preg_replace('/\s+/', '', $this->addAddress) );
                foreach ($address as $value) {
                    if(!empty($value)){
                        $this->mailer->addAddress($value);
                    }
                }
            }

            if (!empty($this->addCC)){
                $cc = preg_split( "/[;,#]/", preg_replace('/\s+/', '', $this->addCC) );
                foreach ($cc as $value) {
                    if(!empty($value)){
                        $this->mailer->addCC($value);
                    }
                }
            }
            
            if (!empty($this->addBCC)){
                $bcc = preg_split( "/[;,#]/", preg_replace('/\s+/', '', $this->addBCC) );
                foreach ($bcc as $value) {
                    if(!empty($value)){
                        $this->mailer->addBCC($value);
                    }
                }
            }

            if (!empty($this->addAttachment)){
                $attachment = preg_split( "/[;,#]/", preg_replace('/\s+/', '', $this->addAttachment) );
                foreach ($attachment as $value) {
                    if(!empty($value)){
                        $this->mailer->addAttachment($value);
                    }
                }
            }

            $this->mailer->Subject = $this->subject;

            if ($this->isHtml){
                $this->mailer->Body = $this->body;
            } else {
                $this->mailer->Body = $this->body;
                $this->mailer->AltBody = $this->body;
            }

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
            return json_encode($data, JSON_PRETTY_PRINT);
        }
    }