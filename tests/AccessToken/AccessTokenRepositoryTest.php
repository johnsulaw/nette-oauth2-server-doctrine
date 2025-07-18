<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\AccessToken;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\AccessTokenRepositoryMock;
use PHPUnit\Framework\TestCase;

class AccessTokenRepositoryTest extends TestCase
{

	public function testGetNewToken(): void
	{
		$repository = new AccessTokenRepository($this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock());
		$token = $repository->getNewToken($client = new ClientEntity(), [$scope = new ScopeEntity()], 'uid');

		self::assertInstanceOf(AccessTokenEntity::class, $token);
		self::assertSame($client, $token->getClient());
		self::assertIsArray($scopes = $token->getScopes());
		self::assertCount(1, $scopes);
		self::assertSame($scope, array_pop($scopes));
		self::assertEquals('uid', $token->getUserIdentifier());
	}

	public function testPersistNewAccessToken(): void
	{
		$token = new AccessTokenEntity();

		$manager = self::createMock(\Doctrine\ORM\EntityManagerInterface::class);
		$manager->expects(self::once())->method('persist')->with($token);
		$manager->expects(self::once())->method('flush');

		$repository = new AccessTokenRepository($manager);
		$repository->persistNewAccessToken($token);
	}

	public function testRevokeAccessToken(): void
	{
		$token = new AccessTokenEntity();

		$entityRepo = self::createMock(\Doctrine\ORM\EntityRepository::class);
		$entityRepo->expects(self::once())->method('findOneBy')->with(['identifier' => 'id'])->willReturn($token);

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(AccessTokenEntity::class)->willReturn($entityRepo);
		$manager->expects(self::once())->method('flush');

		$repository = new AccessTokenRepositoryMock($manager);
		$repository->revokeAccessToken('id');

		self::assertTrue($token->isRevoked());
	}

	public function testIsAccessTokenRevoked(): void
	{
		$token = new AccessTokenEntity();
		$token->setRevoked(true);

		$entityRepo = $this->getMockBuilder(\Doctrine\ORM\EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('findOneBy')->with(['identifier' => 'id'])->willReturn($token);

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(AccessTokenEntity::class)->willReturn($entityRepo);

		$repository = new AccessTokenRepositoryMock($manager);
		self::assertTrue($repository->isAccessTokenRevoked('id'));
	}

}
