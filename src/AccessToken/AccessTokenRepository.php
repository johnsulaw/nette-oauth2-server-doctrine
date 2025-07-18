<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{

	/**
	 * @var \Doctrine\ORM\EntityManagerInterface
	 */
	private $em;

	public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @param ScopeEntityInterface[] $scopes
	 * @param string|null $userIdentifier
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntity
	{
		$accessToken = new AccessTokenEntity();
		$accessToken->setClient($clientEntity);
		foreach ($scopes as $scope) {
			$accessToken->addScope($scope);
		}
		$accessToken->setUserIdentifier($userIdentifier);
		return $accessToken;
	}

	public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
	{
		if ($accessTokenEntity instanceof AccessTokenEntity) {
			$this->em->persist($accessTokenEntity);
			$this->em->flush();
		}
	}

	/**
	 * @param string $tokenId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function revokeAccessToken($tokenId): void
	{
		/** @var AccessTokenEntity|null $accessTokenEntity */
		$accessTokenEntity = $this->em->getRepository(AccessTokenEntity::class)->findOneBy(
			['identifier' => $tokenId]
		);
		if ($accessTokenEntity !== null) {
			$accessTokenEntity->setRevoked(true);
			$this->em->flush();
		}
	}

	/**
	 * @param string $tokenId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function isAccessTokenRevoked($tokenId): bool
	{
		/** @var AccessTokenEntity|null $accessTokenEntity */
		$accessTokenEntity = $this->em->getRepository(AccessTokenEntity::class)->findOneBy(
			['identifier' => $tokenId]
		);
		return $accessTokenEntity !== null ? $accessTokenEntity->isRevoked() : true;
	}

}
