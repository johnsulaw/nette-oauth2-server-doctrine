<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenRepository;

class RefreshTokenRepositoryMock extends RefreshTokenRepository
{

	/**
	 * @var RefreshTokenQuery
	 */
	private $query;

	public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
	{
		parent::__construct($em);
	}

	protected function createQuery(): RefreshTokenQuery
	{
		return $this->query;
	}

	public function createQueryOriginal(): RefreshTokenQuery
	{
		return parent::createQuery();
	}

}
