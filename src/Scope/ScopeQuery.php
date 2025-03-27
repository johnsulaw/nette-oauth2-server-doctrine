<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope;

use Doctrine\ORM\QueryBuilder;

class ScopeQuery
{

	/**
	 * @var callable[]
	 */
	private $filters = [];
	
	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	private $qb;


	public function __construct(
		\Doctrine\ORM\EntityManagerInterface $em
	)
	{
		$this->qb = $em
			->getRepository(\Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity::class)
			->createQueryBuilder('s')
			->select('s');
	}

	public function byIdentifier(string $identifier): ScopeQuery
	{
		$this->filters[] = function (QueryBuilder $queryBuilder) use ($identifier): void {
			$queryBuilder->andWhere('s.identifier = :identifier')->setParameter('identifier', $identifier);
		};
		return $this;
	}


	public function createQuery(): \Doctrine\ORM\Query
	{
		foreach ($this->filters as $filter) {
			$filter($this->qb);
		}
		
		return $this->qb->getQuery();
	}

}
