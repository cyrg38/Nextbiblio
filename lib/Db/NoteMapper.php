<?php

namespace OCA\Nextbiblio\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class NoteMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'biblio', Note::class);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Entity|Note
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id, string $userId): Note {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
		return $this->findEntity($qb);
	}

	/**
	 * @param string $isbn
	 * @param string $userId
	 * @return Entity|Note
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function findByISBN(string $isbn, string $userId) {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
		   ->from($this->getTableName())
		   ->where($qb->expr()->eq('isbn', $qb->createNamedParameter($isbn)))
		   ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
		return $this->findEntity($qb);
	}
	
	/**
	 * @param string $userId
	 * @return array
	 */
	public function findAll(string $userId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
			->orderBy('title','ASC');
		return $this->findEntities($qb);
	}
	
	/**
	 * @param string $userId
	 * @return array
	 */
	public function findAllByAuthors(string $userId, string $authors): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
			->andWhere($qb->expr()->iLike('authors', $qb->createNamedParameter($authors)));
		return $this->findEntities($qb);
	}

}
