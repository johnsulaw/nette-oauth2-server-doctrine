<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Client;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\ClientRepositoryMock;
use PHPUnit\Framework\TestCase;

class ClientRepositoryTest extends TestCase
{

	public function testGetClientEntityPublic(): void
	{
		$client = new ClientEntity();
		$client->setSecret('secret');;

		$entityRepo = $this->getMockBuilder(\Doctrine\ORM\EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('findOneBy')->with(['identifier' => 'id'])->willReturn($client);

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(ClientEntity::class)->willReturn($entityRepo);

		$called = false;
		$repository = new ClientRepositoryMock($manager, function () use (&$called): void {
			$called = true;
		});
		self::assertSame($client, $repository->getClientEntity('id', 'grant', 'secret', false));
		self::assertFalse($called);
	}

	public function testGetClientEntityPrivateSuccess(): void
	{
		$client = new ClientEntity();
		$client->setSecret('secret');

		$entityRepo = $this->getMockBuilder(\Doctrine\ORM\EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('findOneBy')->with(['identifier' => 'id'])->willReturn($client);

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(ClientEntity::class)->willReturn($entityRepo);
		
		$repository = new ClientRepositoryMock($manager);
		self::assertSame($client, $repository->getClientEntity('id', 'grant', 'secret', true));
	}

	public function testGetClientEntityPrivateFail(): void
	{
		$client = new ClientEntity();
		$client->setSecret('secret');

		$entityRepo = $this->getMockBuilder(\Doctrine\ORM\EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('findOneBy')->with(['identifier' => 'id'])->willReturn($client);

		$manager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(ClientEntity::class)->willReturn($entityRepo);

		$repository = new ClientRepositoryMock($manager, function (): bool {
			return false;
		});
		self::assertNull($repository->getClientEntity('id', 'grant', 'secret', true));
	}


}
