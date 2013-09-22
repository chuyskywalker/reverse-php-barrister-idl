<?php

// IDL:
// struct Page extends Entity {
//     authorId        string
//     publishTime     int           [optional]
//     title           string
//     body            string
//     category        PageCategory
//     tags            []string      [optional]
// }

class Page extends Entity {

    /** @var string */
    public $authorId;

    /** @var null|int */
    public $publishTime;

    /** @var string */
    public $title;

    /** @var string */
    public $body;

    /** @var PageCategory */
    public $category;

    /** @var null|string[] */
    public $tags;

}