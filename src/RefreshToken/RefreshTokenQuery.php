<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken;

use Doctrine\ORM\QueryBuilder;

class RefreshTokenQuery
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
			->getRepository(\Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenEntity::class)
			->createQueryBuilder('rt')
			->select('rt');
	}

	public function byIdentifier(string $identifier): RefreshTokenQuery
	{
		$this->filters[] = function (QueryBuilder $queryBuilder) use ($identifier): void {
			$queryBuilder->andWhere('rt.identifier = :identifier')->setParameter('identifier', $identifier);
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
