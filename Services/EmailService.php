<?php

namespace JulienIts\EmailsQueueBundle\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class EmailService
{
    protected $em;
    protected $router;
    protected $twig;
    protected $tokenStorage;
    protected $user;
    protected $emailsQueueService;
    protected $param;

    public function __construct(
        EntityManagerInterface $em,
        RouterInterface $router,
        \Twig\Environment $twig,
        EmailsQueueService $emailsQueueService,
        ParameterBagInterface $param
    )
    {
        $this->em = $em;
		$this->router = $router;
		$this->twig = $twig;
        $this->emailsQueueService = $emailsQueueService;
        $this->param = $param;
    }

    public function createNewAndProcess($config)
    {
        $this->createNew($config);
        $this->emailsQueueService->processQueue(1);
    }

	public function createNew($config)
	{
        if(key_exists('emailHtml', $config)){
            $emailHtml = $config['emailHtml'];
        }else{
            $tpl = $this->twig->load($config['template']);
            $emailHtml = $tpl->render($config['templateVars']);
        }

		$emailQueue = new \JulienIts\EmailsQueueBundle\Entity\EmailQueue();

        try{
            dump($this->param->get('emails_queue.mode'));die;
        }catch(\Exception $e){
            die(' - -- ERROR------');
        }

		$emailQueue->setBody($emailHtml);
		$emailQueue->setContext($this->em->getRepository('EmailsQueueBundle:EmailContext')->findOneByName($config['contextName']));
		$emailQueue->setEmailFrom($config['emailFrom']);
		$emailQueue->setEmailFromName($config['emailFromName']);
		$emailQueue->setEmailTo($config['emailTo']);
        if(isset($config['emailsCc'])){
            $emailQueue->setEmailsCc($config['emailsCc']);
        }
        if(isset($config['emailsBcc'])){
            $emailQueue->setEmailsBcc($config['emailsBcc']);
        }
        if(isset($config['replyTo'])){
            $emailQueue->setReplyTo($config['replyTo']);
        }

		$emailQueue->setPriority($config['priority']);
		$emailQueue->setSubject($config['subject']);
        $emailQueue->setCreatedOn(new \DateTime());

        // Add body text
		if(isset($config['templateText'])){
			$tplText = $this->twig->load($config['templateText']);
			$emailText = $tplText->render($config['templateVars']);
			$emailQueue->setBodyText($emailText);
		}

		$this->em->persist($emailQueue);
		$this->em->flush();
	}
}
