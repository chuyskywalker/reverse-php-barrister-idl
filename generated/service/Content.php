<?php

/**
 * Manage Page Content on site
 */
class Content {

    /**
     * Add a new page to the system.
     * 
     * createdTime, updatedTime, version, and id are automatically set upon creation.
     * 
     * Returns The generated page id
     * 
     * @var string $authorId
     * @var string $title
     * @var string $body
     * @var PageCategory $category
     * 
     * @return string
     */
    public function addPage($authorId, $title, $body, PageCategory $category) {}

    /**
     * Update the given page if possible
     * 
     * Raises error code 30 if page.version is out of date
     * Raises error code 40 if no page exists with the given page.id
     * 
     * Returns The version of the revised page
     * 
     * @var Page $page
     * 
     * @return int
     */
    public function updatePage(Page $page) {}

    /**
     * Deletes the page as long as the version passed is current
     * 
     * Throws error code 30 if version is out of date
     * 
     * Returns True if page deleted, false if page did not exist
     * 
     * @var string $id
     * @var int $version
     * 
     * @return bool
     */
    public function deletePage($id, $version) {}

    /**
     * Force deletes a list of pages
     * 
     * Returns The pages which were actually found and deleted
     * 
     * @var string[] $id
     * 
     * @return string[]
     */
    public function forceDeletePages(array $id) {}

    /**
     * Fetch a page
     * 
     * Returns The page, if it exists, or null
     * 
     * @var string $id
     * 
     * @return null|Page
     */
    public function getPage($id) {}

    /**
     * Fetch a page, more consistent interface
     * 
     * Returns A list with 1 Page item if found, otherwise an empty list
     * 
     * @var string $id
     * 
     * @return Page[]
     */
    public function betterGetPage($id) {}

}
