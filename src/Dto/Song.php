<?php

namespace App\Dto;

class Song
{
    public function __construct(
        private ?string $title = null,
        private ?string $artist = null,
        private ?int $duration = null,
        private ?\DateTimeImmutable $releasedAt = null,
        private ?string $album = null,
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Song
    {
        $this->title = $title;
        return $this;
    }

    public function getArtist(): string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): Song
    {
        $this->artist = $artist;
        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): Song
    {
        $this->duration = $duration;
        return $this;
    }

    public function getReleasedAt(): \DateTimeImmutable
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(\DateTimeImmutable $releasedAt): Song
    {
        $this->releasedAt = $releasedAt;
        return $this;
    }

    public function getAlbum(): string
    {
        return $this->album;
    }

    public function setAlbum(string $album): Song
    {
        $this->album = $album;
        return $this;
    }
}
