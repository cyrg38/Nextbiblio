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
	 */
	public function searchByIsbn(string $isbn) {
		$request = 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . $isbn;
		$response = file_get_contents($request);
		$results = json_decode($response);
		$infos = [];

		if($results->totalItems > 0){
			// avec de la chance, ce sera le 1er trouvé
			$book = $results->items[0];
			$infos['isbn'] = $book->volumeInfo->industryIdentifiers[0]->identifier;
			$infos['title'] = $book->volumeInfo->title;
			$infos['authors'] = $book->volumeInfo->authors[0];
			$infos['languages'] = $book->volumeInfo->language;
			$infos['pubdate'] = $book->volumeInfo->publishedDate;
			$infos['pages'] = $book->volumeInfo->pageCount;
			$infos['tags'] = $book->categories;
			$infos['comments'] = $book->searchInfo->textSnippet;

			if( isset($book->volumeInfo->imageLinks) ){
				$infos['image'] = str_replace('&edge=curl', '', $book->volumeInfo->imageLinks->thumbnail);
			}
			//print_r($infos);
		}
		return $infos;
	}
	
    /**
     * @NoAdminRequired
     *
     * @param string $title
     * @param string|null $emplacement
     * @param boolean $lu
     * @param string|null $period
     * @param string|null $uuid
     * @param string|null $publisher
     * @param string $isbn
     * @param string|null $identifiers
     * @param string $authors
     * @param string|null $timestamp
     * @param string $pubdate
     * @param string|null $tags
     * @param string|null $languages
     * @param string|null $cover
     * @param text|null $comments
     */
	public function create(string $title, ?string $emplacement, boolean $lu, ?string $period, ?string $uuid, 
		?string $publisher, string $isbn, ?string $identifiers, string $authors, string $timestamp, 
		string $pubdate, ?string $tags, ?string $languages, ?string $cover, ?text $comments, string $userId) {
		return $this->service->create( $title,  $emplacement, $lu, $period, $uuid, $publisher, $isbn, $identifiers,
			$authors, $timestamp, $pubdate, $tags, $languages, $cover, $comments, $this->userId);
    }

    /**
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $title
     * @param string|null $emplacement
     * @param boolean $lu
     * @param string|null $period
     * @param string|null $uuid
     * @param string|null $publisher
     * @param string $isbn
     * @param string|null $identifiers
     * @param string $authors
     * @param string $timestamp
     * @param string|null $pubdate
     * @param string|null $tags
     * @param string|null $languages
     * @param string|null $cover
     * @param text|null $comments
     */
	public function update(int $id, string $title, ?string $emplacement, boolean $lu, ?string $period, ?string $uuid, 
			?string $publisher, string $isbn, ?string $identifiers, string $authors, string $timestamp, 
			?string $pubdate, ?string $tags, ?string $languages, ?string $cover, ?text $comments) {
		return $this->handleNotFound(function () 
			use ($id, $title, $emplacement, $lu, $period, $uuid, $publisher, $isbn, $identifiers,
				$authors, $timestamp, $pubdate, $tags, $languages, $cover, $comments) {
			return $this->service->update($id, $title,  $emplacement, $lu, $period, $uuid, $publisher, $isbn, $identifiers,
				$authors, $timestamp, $pubdate, $tags, $languages, $cover, $comments, $this->userId);
		});
    }

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->delete($id, $this->userId);
		});
	}
}
