<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthorizationRequestSerializer;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;
use Lookyman\NetteOAuth2Server\User\UserEntity;
use PHPUnit\Framework\TestCase;

class AuthorizationRequestSerializerTest extends TestCase
{

	public function testProcess(): void
	{
		$client = new ClientEntity();
		$client->setIdentifier('clientId');
		
		$scope = new ScopeEntity();
		$scope->setIdentifier('scopeId');
		
		$original = new AuthorizationRequest();
		$original->setGrantTypeId('grant');
		$original->setClient($client);
		$original->setUser(new UserEntity('user'));
		$original->setScopes([$scope]);
		$original->setAuthorizationApproved(true);
		$original->setRedirectUri('uri');
		$original->setState('state');
		$original->setCodeChallenge('cc');
		$original->setCodeChallengeMethod('ccm');

		$managedClient = new ClientEntity();
		$managedClient->setIdentifier('clientId');

		$managedScope = new ScopeEntity();
		$managedScope->setIdentifier('scopeId');

		$clientRepository = $this->createMock(\Doctrine\Persistence\ObjectRepository::class);
		$clientRepository
			->expects(self::once())
			->method('find')
			->with('clientId')
			->willReturn($managedClient);
		
		$scopeRepository = $this->createMock(\Doctrine\Persistence\ObjectRepository::class);
		$scopeRepository
			->expects(self::once())
			->method('find')
			->with('scopeId')
			->willReturn($managedScope);
		
		$em = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
		$em
			->expects(self::exactly(2))
			->method('getRepository')
			->willReturnMap([
				[ClientEntity::class, $clientRepository],
				[ScopeEntity::class, $scopeRepository],
			]);
		
		$serializer = new AuthorizationRequestSerializer($em);
		$processed = $serializer->unserialize($serializer->serialize($original));

		$original->setClient($managedClient);
		$original->setScopes([$managedScope]);
		
		self::assertEquals($original, $processed);
	}

}
