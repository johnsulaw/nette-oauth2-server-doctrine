<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;

class AuthorizationRequestSerializer implements IAuthorizationRequestSerializer
{

	/**
	 * @var \Doctrine\ORM\EntityManagerInterface
	 */
	private $em;

	public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	public function serialize(AuthorizationRequest $authorizationRequest): string
	{
		/** @var ClientEntity|null $client */
		$client = $authorizationRequest->getClient();
		if ($client !== null) {
			$this->em->detach($authorizationRequest->getClient());
		}
		foreach ($authorizationRequest->getScopes() as $scope) {
			$this->em->detach($scope);
		}
		return serialize($authorizationRequest);
	}

	public function unserialize(string $data): AuthorizationRequest
	{
		/** @var AuthorizationRequest $authorizationRequest */
		$authorizationRequest = unserialize($data);
		/** @var ClientEntity|null $client */
		$client = $authorizationRequest->getClient();
		if ($client !== null) {
			/** @var ClientEntity $client */
			$client = $this->em->merge($client);
			$authorizationRequest->setClient($client);
		}
		
		$scopes = [];
		foreach ($authorizationRequest->getScopes() as $scope) {
			$scopes[] = $this->em->merge($scope);
		}
		$authorizationRequest->setScopes($scopes);
		
		return $authorizationRequest;
	}

}
