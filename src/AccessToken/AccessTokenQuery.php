<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken;

use Doctrine\ORM\QueryBuilder;

class AccessTokenQuery
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
			->getRepository(\Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity::class)
			->createQueryBuilder('at')
			->select('at');
	}
	
	public function createQuery(): \Doctrine\ORM\Query
	{
		foreach ($this->filters as $filter) {
			$filter($this->qb);
		}
		
		return $this->qb->getQuery();
	}

}
