<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientRepository;

class ClientRepositoryMock extends ClientRepository
{

	/**
	 * @var ClientQuery
	 */
	private $query;

	public function __construct(\Doctrine\ORM\EntityManagerInterface $manager, ?callable $secretValidator = null)
	{
		parent::__construct($manager, $secretValidator);
	}

	protected function createQuery(): ClientQuery
	{
		return $this->query;
	}

	public function createQueryOriginal(): ClientQuery
	{
		return parent::createQuery();
	}

}
