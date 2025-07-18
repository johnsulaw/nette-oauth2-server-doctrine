<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\AuthCode;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\AuthCodeRepositoryMock;
use PHPUnit\Framework\TestCase;

class AuthCodeRepositoryTest extends TestCase
{

	public function testGetNewAuthCode(): void
	{
		$repository = new AuthCodeRepository($this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock());
		self::assertInstanceOf(AuthCodeEntity::class, $repository->getNewAuthCode());
	}

	public function testPersistNewAuthCode(): void
	{
		$code = new AuthCodeEntity();

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('persist')->with($code);
		$manager->expects(self::once())->method('flush');

		$repository = new AuthCodeRepository($manager);
		$repository->persistNewAuthCode($code);
	}

	public function testRevokeAuthCode(): void
	{
		$code = new AuthCodeEntity();

		$entityRepo = $this->getMockBuilder(\Doctrine\ORM\EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('findOneBy')->with(['identifier' => 'id'])->willReturn($code);

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(AuthCodeEntity::class)->willReturn($entityRepo);
		$manager->expects(self::once())->method('flush');

		$repository = new AuthCodeRepositoryMock($manager);
		$repository->revokeAuthCode('id');

		self::assertTrue($code->isRevoked());
	}

	public function testIsAuthCodeRevoked(): void
	{
		$code = new AuthCodeEntity();
		$code->setRevoked(true);

		$entityRepo = $this->getMockBuilder(\Doctrine\ORM\EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('findOneBy')->with(['identifier' => 'id'])->willReturn($code);

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(AuthCodeEntity::class)->willReturn($entityRepo);

		$repository = new AuthCodeRepositoryMock($manager);
		self::assertTrue($repository->isAuthCodeRevoked('id'));
	}

}
