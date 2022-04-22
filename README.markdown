# Julien-ITS

## emails-queue-s5

### Features

Service you can use to send your emails to a queue system. All your emails will be stored in your database to keep logs of them.
Send your emails directly or with a cron using the queue.
Define how many emails you want to send each time you call the process queue action.

### Installation


Install with composer

Before installation :

Add in your .env file

```sh
EMAILS_QUEUE_MODE: "prod"
EMAILS_QUEUE_DEBUG_TO: ""
EMAILS_QUEUE_DEBUG_CC: ""
```

Add emails_queue.yaml in /config/packages

```sh
emails_queue:
  mode: '%env(EMAILS_QUEUE_MODE)%'
  debug_to: '%env(EMAILS_QUEUE_DEBUG_TO)%'
  debug_cc: '%env(EMAILS_QUEUE_DEBUG_CC)%'
```

You can now install it via composer

```sh
$ composer require julien-its/emails-queue-s5
```

### Instructions

Once installed,

**Generate new tables in your database with doctrine**

```sh
$ php bin/console doctrine:migration:diff
$ php bin/console doctrine:migration:migrate
```

**Create a new email service** where you will define all your emails methods. We only add one exemple of a contact form email

    <?php
    namespace App\Services;
    use \JulienIts\EmailsQueueBundle\Entity\EmailQueue;
    class EmailService
    {
    	const DEFAULT_SUBJECT = "My App";
        protected $jitsEmailService;

        public function __construct(\JulienIts\EmailsQueueBundle\Services\EmailService $jitsEmailService)
        {
            $this->jitsEmailService = $jitsEmailService;
        }

    	public function contact($message)
    	{
            $config = array(
                'template' => 'EmailsQueueBundle:mail:contact.html.twig',
                'templateVars' => array('message' => $message),
                'emailFrom' => 'from@from.com',
                'emailFromName' => 'My app',
                'contextName' => 'contact',
                'priority' => EmailQueue::HIGH_PRIORITY,
                'subject' => self::DEFAULT_SUBJECT.' : Contact',
                'emailTo' => 'toemail@to.com',
                'emailsBcc' => 'contact@from.com;email2@email.com'
            );
    		$this->jitsEmailService->createNewAndProcess($config);
    	}
    }

Note that you can copy the contact.html and email layout on your own appBundle to personalize them

**Two possibilities when creating an emailQueue :**

    $this->jitsEmailService->createNew($config);
    $this->jitsEmailService->createNewAndProcess($config);

createNewAndProcess Will directly process the email queue and send it to your mail service.

### Send an email

To send your email, call your service in a controller :

```sh
$message = array(
    'name' => 'Julien Gustin',
    'phone' => '+320484010203',
    'message' => 'gustin.julien@gmail.com'
);
$emailService->contact($message);
```
### Define the cron action

If you went to send emails by packets, you can use the command


    php bin/console jits:queue:process --limit 30

