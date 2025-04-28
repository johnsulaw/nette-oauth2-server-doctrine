<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{

	/**
	 * @var \Doctrine\ORM\EntityManagerInterface
	 */
	private $em;

	public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	public function getNewAuthCode(): AuthCodeEntity
	{
		return new AuthCodeEntity();
	}

	public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
	{
		if ($authCodeEntity instanceof AuthCodeEntity) {
			$this->em->persist($authCodeEntity);
			$this->em->flush();
		}
	}

	/**
	 * @param string $codeId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function revokeAuthCode($codeId): void
	{
		/** @var AuthCodeEntity|null $authCodeEntity */
		$authCodeEntity = $this->em->getRepository(AuthCodeEntity::class)->fetchOne($this->createQuery()->byIdentifier($codeId));
		if ($authCodeEntity !== null) {
			$authCodeEntity->setRevoked(true);
			$this->em->flush();
		}
	}

	/**
	 * @param string $codeId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function isAuthCodeRevoked($codeId): bool
	{
		/** @var AuthCodeEntity|null $authCodeEntity */
		$authCodeEntity = $this->em->getRepository(AuthCodeEntity::class)->fetchOne($this->createQuery()->byIdentifier($codeId));
		return $authCodeEntity !== null ? $authCodeEntity->isRevoked() : true;
	}

	protected function createQuery(): AuthCodeQuery
	{
		return new AuthCodeQuery();
	}

}
