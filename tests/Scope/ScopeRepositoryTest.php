<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Scope;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\ScopeRepositoryMock;
use PHPUnit\Framework\TestCase;

class ScopeRepositoryTest extends TestCase
{

	public function testGetScopeEntityByIdentifier(): void
	{
		$scope = new ScopeEntity();

		$entityRepo = $this->getMockBuilder(\Doctrine\ORM\EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('findOneBy')->with(['identifier' => 'id'])->willReturn($scope);

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(ScopeEntity::class)->willReturn($entityRepo);

		$repository = new ScopeRepositoryMock($manager);
		self::assertSame($scope, $repository->getScopeEntityByIdentifier('id'));
	}

	public function testFinalizeScopes(): void
	{
		$repository = new ScopeRepository($this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock());
		$scopes = $repository->finalizeScopes([$scope = new ScopeEntity()], 'grant', new ClientEntity(), 'uid');

		self::assertIsArray($scopes);
		self::assertCount(1, $scopes);
		self::assertSame($scope, array_pop($scopes));
	}

}
