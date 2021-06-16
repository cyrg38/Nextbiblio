<?php

namespace OCA\Nextbiblio\Controller;

use OCA\Nextbiblio\AppInfo\Application;
use OCA\Nextbiblio\Service\NoteService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class NoteController extends Controller {
	/** @var NoteService */
	private $service;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
			NoteService $service, $userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		return new DataResponse($this->service->findAll($this->userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->find($id, $this->userId);
		});
	}

    /**
     * @NoAdminRequired
     *
     * @param string $title
     * @param string $_emplacement
     * @param string $_isbn
     * @param string $_lu
     * @param string $_period
     * @param string $uuid
     * @param string $publisher
     * @param string $isbn
     * @param string $identifiers
     * @param string $authors
     * @param string $timestamp
     * @param string $pubdate
     * @param string $tags
     * @param string $languages
     * @param string $cover
     * @param string $library_name
     * @param text $comments
     */
    public function create(string $title, string $_emplacement, string $_isbn, string $_lu, 
                           string $_period, string $uuid, string $publisher, string $isbn, string $identifiers,
                           string $authors, string $timestamp, string $pubdate, string $tags, string $languages,
                           string $cover, string $library_name, text $comments,
                           string $userId) {
        return $this->service->create( $title,  $_emplacement,  $_isbn,  $_lu, 
                            $_period,  $uuid,  $publisher,  $isbn,  $identifiers,
                            $authors,  $timestamp,  $pubdate,  $tags,  $languages,
                            $cover,  $library_name, $comments,
                            $this->userId);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $title
     * @param string $_emplacement
     * @param string $_isbn
     * @param string $_lu
     * @param string $_period
     * @param string $uuid
     * @param string $publisher
     * @param string $isbn
     * @param string $identifiers
     * @param string $authors
     * @param string $timestamp
     * @param string $pubdate
     * @param string $tags
     * @param string $languages
     * @param string $cover
     * @param string $library_name
     * @param text $comments
     */
    public function update(int $id, string $title, string $_emplacement, string $_isbn, string $_lu, 
                           string $_period, string $uuid, string $publisher, string $isbn, string $identifiers,
                           string $authors, string $timestamp, string $pubdate, string $tags, string $languages,
                           string $cover, string $library_name, text $comments) {
        return $this->handleNotFound(function () use ($id, $title,  $_emplacement,  $_isbn,  $_lu, 
                            $_period,  $uuid,  $publisher,  $isbn,  $identifiers,
                            $authors,  $timestamp,  $pubdate,  $tags,  $languages,
                            $cover,  $library_name, $comments)
            return $this->service->update($id, $title,  $_emplacement,  $_isbn,  $_lu, 
                            $_period,  $uuid,  $publisher,  $isbn,  $identifiers,
                            $authors,  $timestamp,  $pubdate,  $tags,  $languages,
                            $cover,  $library_name, $comments, $this->userId);


	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->delete($id, $this->userId);
		});
	}
}
