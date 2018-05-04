<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\middleware\ValidateParam as ValidateParam;

    // POST example api to send mail
    $app->post('/mail/send', function (Request $request, Response $response) {      
        $mail = new classes\Mailer($this->mail);
        $datapost = $request->getParsedBody();

        $mail->addAddress = $datapost['To'];
        $mail->subject = $datapost['Subject'];
        $mail->body = $datapost['Message'];
        $mail->isHtml = $datapost['Html'];
        $mail->setFrom = $datapost['From'];
        $mail->setFromName = $datapost['FromName'];
        $mail->addCC = $datapost['CC'];
        $mail->addBCC = $datapost['BCC'];
        $mail->addAttachment = $datapost['Attachment'];
        
        $body = $response->getBody();
        $body->write($mail->send());
        return classes\Cors::modify($response,$body,200);
    })->add(new ValidateParam(['FromName','CC','BCC','Attachment']))
        ->add(new ValidateParam(['To','Subject','Message','Html'],'','required'))
        ->add(new ValidateParam('From','6-50','email'));