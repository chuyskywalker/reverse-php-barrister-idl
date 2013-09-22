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
     * @param string $authorId
     * @param string $title
     * @param string $body
     * @param PageCategory $category
     *
     * @return string The generated page id
     */
    public function addPage($authorId, $title, $body, PageCategory $category) {}

    /**
     * Update the given page if possible
     *
     * Raises error code 30 if page.version is out of date
     * Raises error code 40 if no page exists with the given page.id
     *
     * @param Page $page
     *
     * @return int The version of the revised page
     */
    public function updatePage(Page $page) {}

    /**
     * Deletes the page as long as the version passed is current
     *
     * Throws error code 30 if version is out of date
     *
     * @param string $id
     * @param int $version
     * @return bool True if page deleted, false if page did not exist
     */
    public function deletePage($id, $version) {}

    /**
     * Force deletes a list of pages
     *
     * @param string[] $id
     * @return string[] The pages which were actually found and deleted
     */
    public function forceDeletePages($id) {}

    /**
     * Fetch a page
     *
     * @param string $id
     *
     * @return null|Page The page, if it exists, or null
     */
    public function getPage($id) {}

    /**
     * Fetch a page, more consistent interface
     *
     * @param string $id
     *
     * @return Page[] A list with 1 Page item if found, otherwise an empty list
     */
    public function betterGetPage($id) {}

    /**
     * The RPC does not have access to this
     */
    private function internalMethod() {}

}