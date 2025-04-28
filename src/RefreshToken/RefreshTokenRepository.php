<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{

	/**
	 * @var \Doctrine\ORM\EntityManagerInterface
	 */
	private $em;

	public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	public function getNewRefreshToken(): RefreshTokenEntity
	{
		return new RefreshTokenEntity();
	}

	public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
	{
		if ($refreshTokenEntity instanceof RefreshTokenEntity) {
			$this->em->persist($refreshTokenEntity);
			$this->em->flush();
		}
	}

	/**
	 * @param string $tokenId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function revokeRefreshToken($tokenId): void
	{
		/** @var RefreshTokenEntity|null $refreshTokenEntity */
		$refreshTokenEntity = $this->em->getRepository(RefreshTokenEntity::class)->fetchOne($this->createQuery()->byIdentifier($tokenId));
		if ($refreshTokenEntity !== null) {
			$refreshTokenEntity->setRevoked(true);
			$this->em->flush();
		}
	}

	/**
	 * @param string $tokenId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function isRefreshTokenRevoked($tokenId): bool
	{
		/** @var RefreshTokenEntity|null $refreshTokenEntity */
		$refreshTokenEntity = $this->em->getRepository(RefreshTokenEntity::class)->fetchOne($this->createQuery()->byIdentifier($tokenId));
		return $refreshTokenEntity !== null ? $refreshTokenEntity->isRevoked() : true;
	}

	protected function createQuery(): RefreshTokenQuery
	{
		return new RefreshTokenQuery();
	}

}
