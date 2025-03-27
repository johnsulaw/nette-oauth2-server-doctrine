<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\RefreshToken;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\RefreshTokenRepositoryMock;
use PHPUnit\Framework\TestCase;

class RefreshTokenRepositoryTest extends TestCase
{

	public function testGetNewRefreshToken(): void
	{
		$repository = new RefreshTokenRepository($this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock());
		self::assertInstanceOf(RefreshTokenEntity::class, $repository->getNewRefreshToken());
	}

	public function testPersistNewRefreshToken(): void
	{
		$token = new RefreshTokenEntity();

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('persist')->with($token);
		$manager->expects(self::once())->method('flush');

		$repository = new RefreshTokenRepository($manager);
		$repository->persistNewRefreshToken($token);
	}

	public function testRevokeRefreshToken(): void
	{
		$token = new RefreshTokenEntity();

		$entityRepo = $this->getMockBuilder(\Doctrine\ORM\EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('findOneBy')->with(['identifier' => 'id'])->willReturn($token);

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(RefreshTokenEntity::class)->willReturn($entityRepo);
		$manager->expects(self::once())->method('flush');

		$repository = new RefreshTokenRepositoryMock($manager);
		$repository->revokeRefreshToken('id');

		self::assertTrue($token->isRevoked());
	}

	public function testIsRefreshTokenRevoked(): void
	{
		$token = new RefreshTokenEntity();
		$token->setRevoked(true);

		$entityRepo = $this->getMockBuilder(\Doctrine\ORM\EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('findOneBy')->with(['identifier' => 'id'])->willReturn($token);

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(RefreshTokenEntity::class)->willReturn($entityRepo);

		$repository = new RefreshTokenRepositoryMock($manager);
		self::assertTrue($repository->isRefreshTokenRevoked('id'));
	}

}
