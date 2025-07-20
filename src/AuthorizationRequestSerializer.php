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
			$clientId = $client->getIdentifier();
			$managedClient = $this->em->getRepository(ClientEntity::class)->findBy(['identifier' => $clientId]);
			
			if ($managedClient === null) {
				throw new \RuntimeException("Client with ID '$clientId' not found.");
			}

			$authorizationRequest->setClient($client);
		}

		$managedScopes = [];
		foreach ($authorizationRequest->getScopes() as $scope) {
			$scopeId = $scope->getIdentifier();
			$managedScope = $this->em->getRepository(\Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity::class)->findBy(['identifier' => $scopeId]);
			
			if ($managedScope === null) {
				throw new \RuntimeException("Scope with ID '$scopeId' not found.");
			}

			$managedScopes[] = $managedScope;
		}

		$authorizationRequest->setScopes($managedScopes);

		return $authorizationRequest;
	}

}
