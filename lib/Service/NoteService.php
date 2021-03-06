<?php

namespace OCA\Nextbiblio\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Nextbiblio\Db\Note;
use OCA\Nextbiblio\Db\NoteMapper;

class NoteService {

	/** @var NoteMapper */
	private $mapper;

	public function __construct(NoteMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function findAll(string $userId): array {
		return $this->mapper->findAll($userId);
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new NoteNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find($id, $userId) {
		try {
			return $this->mapper->find($id, $userId);

			// in order to be able to plug in different storage backends like files
		// for instance it is a good idea to turn storage related exceptions
		// into service related exceptions so controllers and service users
		// have to deal with only one type of exception
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

    public function create(string $title, string $content, string $userId) {
        $note = new Note();
         $note->setTitle($title);
         $note->setEmplacement($emplacement);
         $note->setLu($lu);
         $note->setPeriod($period);
         $note->setUuid($uuid);
         $note->setPublisher($publisher);
         $note->setIsbn($isbn);
         $note->setIdentifiers($identifiers);
         $note->setAuthors($authors);
         $note->setTimestamp($timestamp);
         $note->setPubdate($pubdate);
         $note->setTags($tags);
         $note->setLanguages($languages);
         $note->setCover($cover);
         $note->setComments($comments);
         $note->setUserId($this->userId);
        $note->setUserId($userId);
        return $this->mapper->insert($note);
    }

    public function update(int $id, string $title, string $content, string $userId) {
        try {
            $note = $this->mapper->find($id, $userId);
         $note->setTitle($title);
         $note->setEmplacement($emplacement);
         $note->setLu($lu);
         $note->setPeriod($period);
         $note->setUuid($uuid);
         $note->setPublisher($publisher);
         $note->setIsbn($isbn);
         $note->setIdentifiers($identifiers);
         $note->setAuthors($authors);
         $note->setTimestamp($timestamp);
         $note->setPubdate($pubdate);
         $note->setTags($tags);
         $note->setLanguages($languages);
         $note->setCover($cover);
         $note->setComments($comments);
         $note->setUserId($this->userId);

            return $this->mapper->update($note);
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

	public function delete($id, $userId) {
		try {
			$note = $this->mapper->find($id, $userId);
			$this->mapper->delete($note);
			return $note;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
