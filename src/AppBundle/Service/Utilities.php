<?php

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Google_Service_Calendar;


define('SCOPES', implode(' ', array(
        Google_Service_Calendar::CALENDAR_READONLY)
));
define('STDIN',fopen("php://stdin","r"));

class Utilities
{

    /** @var EntityManager $em */
    protected $em;

    /** @var string $objectName */
    protected $objectName;

    /** @var array $criteria */
    protected $criteria = [];

    /** @var array $orderBy */
    protected $orderBy = [];

    /** @var  integer | null $limit */
    protected $limit;

    /** @var  integer | null $offset */
    protected $offset;
    
    /** @var string */
    protected $googleCalendarAppName;
    
    /** @var string */
    protected $googleCalendarCredentialsPath;
    
    /** @var string */
    protected $googleCalendarClientSecretPath;
    
    /** @var ContainerInterface $container */
    protected $container;

    /** @var  string $googleCalendarId */
    protected $googleCalendarId;

    /** @var  array $googleCalendarOptParameters */
    protected $googleCalendarOptParameters;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->limit = null;
        $this->offset = null;
        $this->container = $container;
        $this->googleCalendarAppName = 'DKJ Google Calendar API';
        $this->googleCalendarCredentialsPath = $this->container->getParameter('google_calendar_credentials_path');
        $this->googleCalendarClientSecretPath = $this->container->getParameter('google_calendar_client_secret');
    }

    /**
     * @return string
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * @param string $objectName
     * @return $this
     */
    public function setObjectName(string $objectName)
    {
        $this->objectName = $objectName;

        return $this;
    }

    /**
     * @return array
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param array $criteria
     * @return $this
     */
    public function setCriteria(array $criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param array $orderBy
     * @return $this
     */
    public function setOrderBy(array $orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     * @return Utilities
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int|null $offset
     * @return Utilities
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleCalendarId()
    {
        return $this->googleCalendarId;
    }

    /**
     * @param string $googleCalendarId
     * @return Utilities
     */
    public function setGoogleCalendarId($googleCalendarId)
    {
        $this->googleCalendarId = $googleCalendarId;
        return $this;
    }

    /**
     * @return array
     */
    public function getGoogleCalendarOptParameters()
    {
        return $this->googleCalendarOptParameters;
    }

    /**
     * @param array $googleCalendarOptParameters
     * @return Utilities
     */
    public function setGoogleCalendarOptParameters($googleCalendarOptParameters)
    {
        $this->googleCalendarOptParameters = $googleCalendarOptParameters;
        return $this;
    }



    public function getPages() {

        $pages = 1;

        $allRecords = $this->getAllRecords();

        $recordsToShow = $this->getRepository()->findBy($this->getCriteria(), $this->getOrderBy(), $this->getLimit(), $this->getOffset());

        $countAllRecords = count($allRecords);

        $countRecordsToShow = count($recordsToShow);

        if ($countAllRecords > $countRecordsToShow) {

            $pages = $countAllRecords / $countRecordsToShow;

            $pages = round($pages);

            return $pages;
        }

        return $pages;

    }

    /**
     * @return array
     */
    public function paginationAction() {

        return $this->getRepository()->findBy($this->getCriteria(), $this->getOrderBy(), $this->getLimit(), $this->getOffset());

    }

    /**
     * @return string
     */
    public function getGoogleClient() {
		  $client = new \Google_Client();
		  $client->setApplicationName($this->googleCalendarAppName);
		  $client->setScopes([Google_Service_Calendar::CALENDAR_READONLY]);
		  $client->setAuthConfig($this->googleCalendarClientSecretPath);
		  $client->setAccessType('offline');

		  // Load previously authorized credentials from a file.
		  $credentialsPath = $this->expandHomeDirectory($this->googleCalendarCredentialsPath);
		  dump($credentialsPath);
		  die;
		  if (file_exists($credentialsPath)) {
			$accessToken = json_decode(file_get_contents($credentialsPath), true);
		  } else {
			// Request authorization from the user.
			$authUrl = $client->createAuthUrl();
			$authCode = trim(fgets(STDIN));
			
			dump($authUrl);
			die;
			
			// Exchange authorization code for an access token.
			$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

			// Store the credentials to disk.
			if(!file_exists(dirname($credentialsPath))) {
			  mkdir(dirname($credentialsPath), 0700, true);
			}
			file_put_contents($credentialsPath, json_encode($accessToken));
			printf("Credentials saved to %s\n", $credentialsPath);
		  }
		  $client->setAccessToken($accessToken);

		  // Refresh the token if it's expired.
		  if ($client->isAccessTokenExpired()) {
			$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
		  }
		  return $client;
	}

	public function getListEvents() {

        /** @var \Google_Client $client */
        $client = $this->getGoogleClient();
        $service = new Google_Service_Calendar($client);
        $result = $service->events->listEvents($this->getGoogleCalendarId(), $this->getGoogleCalendarOptParameters());

        return $result;
    }

    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    public function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }
    
    
    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository() {

        $repository = $this->em->getRepository($this->getObjectName());

        return $repository;
    }

    /**
     * @return array
     */
    private function getAllRecords() {

        $records = $this->getRepository()->findAll();

        return $records;

    }

}
