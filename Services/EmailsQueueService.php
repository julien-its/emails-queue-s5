<?php
namespace JulienIts\EmailsQueueBundle\Services;

use JulienIts\EmailsQueueBundle\Entity\EmailQueue;
use JulienIts\EmailsQueueBundle\Entity\EmailSent;
use Doctrine\ORM\EntityManagerInterface;


class EmailsQueueService
{
    //const WHITE_LIST_ENABLE = false;
	protected $em;
	protected $mailer;
	protected $appMode;
    
    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function processQueue($limit=15)
    {
        $queueRepo = $this->em->getRepository(EmailQueue::class);
        $emailsQueue = $queueRepo->findBy(array(), array('priority'=>'desc', 'id'=>'desc'), $limit);
        foreach($emailsQueue as $emailQueue){
            $this->_sendEmailQueue($emailQueue);
            $this->_setEmailQueueToSent($emailQueue);
        }
    }
    
    private function _sendEmailQueue(EmailQueue $emailQueue)
    {
        $message = new \Swift_Message();
		 
        $to = $emailQueue->getEmailTo();
        
        if($emailQueue->getReplyTo() != null && $emailQueue->getReplyTo() != ''){
            $message->addReplyTo($emailQueue->getReplyTo());
        }
        
        $message->setSubject($emailQueue->getSubject())
				->setFrom([$emailQueue->getEmailFrom() => $emailQueue->getEmailFromName()])
				->setTo($to)
				->setBody($emailQueue->getBody(),'text/html');
        
        if($emailQueue->getBodyText() != null){
            $message->addPart($emailQueue->getBodyText(),'text/plain');
        }


        foreach($emailQueue->getBccArray() as $bcc){
            if($bcc == $to)
                continue;
            $message->addBcc($bcc);
        }
        
        // Add CC from the emailQueue entity
        if($emailQueue->getEmailsCc() != null){
            $arrEmails = explode(';', $emailQueue->getEmailsCc());
            foreach($arrEmails as $email){
                $email = trim($email);
                if($email == $to){
                    continue;
                }
                if(in_array($email, $emailQueue->getBccArray())){
                    continue;
                }
                $message->addCc($email);
            }
        }
        
        // Add BCC from the emailQueue entity
        if($emailQueue->getEmailsBcc() != null){
            $arrEmails = explode(';', $emailQueue->getEmailsBcc());
            foreach($arrEmails as $email){
                $email = trim($email);
                if($email == $to){
                    continue;
                }
                if(in_array($email, $emailQueue->getBccArray())){
                    continue;
                }
                $message->addBcc($email);
            }
        }
        
        $this->mailer->send($message);
    }
    
    private function _setEmailQueueToSent(EmailQueue $emailQueue)
    {
        $emailSent = new EmailSent();
        
        $emailSent->setPriority($emailQueue->getPriority());
        $emailSent->setEmailFrom($emailQueue->getEmailFrom());
        $emailSent->setEmailFromName($emailQueue->getEmailFromName());
        $emailSent->setEmailTo($emailQueue->getEmailTo());
        $emailSent->setSubject($emailQueue->getSubject());
        $emailSent->setBody($emailQueue->getBody());
        $emailSent->setBodyText($emailQueue->getBodyText());
        $emailSent->setCreatedOn($emailQueue->getCreatedOn());
        $emailSent->setContext($emailQueue->getContext());
		$emailSent->setEmailsBcc($emailQueue->getEmailsBcc());
		$emailSent->setEmailsCc($emailQueue->getEmailsCc());
		$emailSent->setReplyTo($emailQueue->getReplyTo());
        
        $this->em->persist($emailSent);
        $this->em->remove($emailQueue);
		$this->em->flush();
    }
}
