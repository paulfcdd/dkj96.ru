<?php

namespace AppBundle\Service;
define('STDIN',fopen("php://stdin","r"));

use Symfony\Component\DependencyInjection\ContainerInterface;

class GoogleCalendar
{
	private $container;

	private $clientSecretPath;

	private $credentialsPath;

	private $applicationName;

	private $scopes;

	/** @var  string $calendarId */
	private $calendarId;

	/** @var  array $optParams */
	private $optParams;


	/**
	 * @return mixed
	 */
	public function getClientSecretPath()
	{
		return $this->clientSecretPath;
	}

	/**
	 * @param mixed $clientSecretPath
	 *
	 * @return $this
	 */
	private function setClientSecretPath($clientSecretPath)
	{
		$this->clientSecretPath = $clientSecretPath;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCredentialsPath()
	{
		return $this->credentialsPath;
	}

	/**
	 * @param mixed $credentialsPath
	 *
	 * @return $this
	 */
	private function setCredentialsPath($credentialsPath)
	{
		$this->credentialsPath = $credentialsPath;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getApplicationName()
	{
		return $this->applicationName;
	}

	/**
	 * @param mixed $applicationName
	 *
	 * @return $this
	 */
	public function setApplicationName($applicationName)
	{
		$this->applicationName = $applicationName;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getScopes() {

		return $this->scopes;

	}

	/**
	 * @param $scopes
	 * @return $this
	 */
	private function setScopes($scopes) {

		$this->scopes = $scopes;

		return $this;

	}

	/**
	 * @return string
	 */
	public function getCalendarId()
	{
		return $this->calendarId;
	}

	/**
	 * @param string $calendarId
	 * @return GoogleCalendar
	 */
	public function setCalendarId(string $calendarId)
	{
		$this->calendarId = $calendarId;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getOptParams()
	{
		return $this->optParams;
	}

	/**
	 * @param array $optParams
	 * @return GoogleCalendar
	 */
	public function setOptParams(array $optParams)
	{
		$this->optParams = $optParams;
		return $this;
	}



	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
		$this
			->setClientSecretPath($this->container->getParameter('google_calendar_client_secret'))
			->setCredentialsPath($this->container->getParameter('google_calendar_credentials'))
			->setScopes( implode(' ', [\Google_Service_Calendar::CALENDAR_READONLY]));
	}

	public function run() {
		$service = new \Google_Service_Calendar($this->getClient());

		$results = $service->events->listEvents($this->getCalendarId(), $this->getOptParams());

		if (count($results->getItems()) == 0) {
			print "No upcoming events found.\n";
		} else {
			print "Upcoming events:\n";
			foreach ($results->getItems() as $event) {
				$start = $event->start->dateTime;
				if (empty($start)) {
					$start = $event->start->date;
				}
				printf("%s (%s)\n", $event->getSummary(), $start);
			}
		}
	}

	public function getClient() {
		$client = new \Google_Client();

		$client->setAuthConfig($this->getClientSecretPath());
		$client->setScopes($this->getScopes());
		$client->setApplicationName($this->getApplicationName());
		$client->setAccessType('offline');
		$client->setRedirectUri('http://dkj96.ru');

		$credentialsPath = $this->expandHomeDirectory($this->getCredentialsPath());

		if (file_exists($credentialsPath)) {
			$accessToken = json_decode(file_get_contents($credentialsPath), true);
		} else {
			// Request authorization from the user.
			$authUrl = $client->createAuthUrl();
			printf("Open the following link in your browser:\n%s\n", $authUrl);
			print 'Enter verification code: ';
			$authCode = trim(fgets(STDIN));

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

	private function expandHomeDirectory($path) {
		$homeDirectory = getenv('HOME');
		if (empty($homeDirectory)) {
			$homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
		}
		return str_replace('~', realpath($homeDirectory), $path);
	}
}