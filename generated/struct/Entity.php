<?php

/**
 * Basic entities for Content items
 */
class Entity {

    /** @var string The entity ID */
    public $id;

    /** @var int Unix timestamp to be populated at creation */
    public $createdTime;

    /** @var int At every edit, mark this with the current unixtimestamp */
    public $updatedTime;

    /** @var int how many times has this entity been changed Multi line is silly */
    public $version;

}
