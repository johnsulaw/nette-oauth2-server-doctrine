<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeRepository;

class AuthCodeRepositoryMock extends AuthCodeRepository
{

	/**
	 * @var AuthCodeQuery
	 */
	private $query;

	public function __construct(\Doctrine\ORM\EntityManagerInterface $registry)
	{
		parent::__construct($registry);
	}

	protected function createQuery(): AuthCodeQuery
	{
		return $this->query;
	}

	public function createQueryOriginal(): AuthCodeQuery
	{
		return parent::createQuery();
	}

}
