<?php

namespace App\Entity;

interface PublishedDateTimeInterface
{
    public function setPublished(\DateTimeInterface $published): PublishedDateTimeInterface;
}