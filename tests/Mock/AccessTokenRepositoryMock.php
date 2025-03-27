<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenRepository;

class AccessTokenRepositoryMock extends AccessTokenRepository
{

	public function __construct(\Doctrine\ORM\EntityManagerInterface $registry)
	{
		parent::__construct($registry);
	}

}
