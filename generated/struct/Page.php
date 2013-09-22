<?php

/**
 * Page structure comment
 */
class Page extends Entity {

    /** @var string */
    public $authorId;

    /** @var null|int */
    public $publishTime;

    /** @var string */
    public $title;

    /** @var string body element comment */
    public $body;

    /** @var PageCategory */
    public $category;

    /** @var null|string[] */
    public $tags;

}
