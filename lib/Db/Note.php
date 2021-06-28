<?php

namespace OCA\Nextbiblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Note extends Entity implements JsonSerializable {
    protected $title;
    protected $userId;
    protected $emplacement;
    protected $lu;
    protected $period;
    protected $uuid;
    protected $publisher;
    protected $isbn;
    protected $identifiers;
    protected $authors;
    protected $timestamp;
    protected $pubdate;
    protected $tags;
    protected $languages;
    protected $cover;
    protected $comments;

    public function __construct() {
        $this->addType('id','integer');
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'user_id' => $this->user_id,
            'emplacement' => $this->emplacement,
            'lu' => $this->lu,
            'period' => $this->period,
            'uuid' => $this->uuid,
            'publisher' => $this->publisher,
            'isbn' => $this->isbn,
            'identifiers' => $this->identifiers,
            'authors' => $this->authors,
            'timestamp' => $this->timestamp,
            'pubdate' => $this->pubdate,
            'tags' => $this->tags,
            'languages' => $this->languages,
            'cover' => $this->cover,
            'comments' => $this->comments
        ];
    }
}
