<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeRepository;

class ScopeRepositoryMock extends ScopeRepository
{

	/**
	 * @var ScopeQuery
	 */
	private $query;

	public function __construct(\Doctrine\ORM\EntityManagerInterface $registry)
	{
		parent::__construct($registry);
	}

	protected function createQuery(): ScopeQuery
	{
		return $this->query;
	}

	public function createQueryOriginal(): ScopeQuery
	{
		return parent::createQuery();
	}

}
