<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');

class JBusinessDirectoryModelBookmark extends JModelLegacy{

    public function __construct(){
        parent::__construct();
    }

    /**
     * Returns a Table object, always creating it
     *
     * @param   type	The table type to instantiate
     * @param   string	A prefix for the table class name. Optional.
     * @param   array  Configuration array for model. Optional.
     * @return  JTable	A database object
     */
    public function getTable($type = 'Bookmark', $prefix = 'JTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Add item bookmark
     *
     * @param $data
     * @return bool
     */

    public function addBookmark($data) {
        $row = $this->getTable();

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($row->getError());
            return false;
        }

        // if($data["item_type"] == BOOKMARK_TYPE_BUSINESS){
        //     NotificationService::sendBookmarkNotification($data["item_id"]);
        // }

        return true;
    }

    /**
     * Update Bookmark
     *
     * @param $data
     * @return bool
     */
    public function updateBookmark($data) {
        //save in banners table
        $table = $this->getTable("Bookmark");
        $table->updateBookmark( $data["item_id"],$data["user_id"], $data["item_type"],$data["note"]);

        return true;
    }

    /**
     * Remove bookmark
     *
     * @param $data
     * @return mixed
     */
    public function removeBookmark($data) {
        $table = $this->getTable("Bookmark");
        $result = $table->deleteBookmark($data["user_id"], $data["item_id"], $data["item_type"]);

        return $result;
    }


    /**
     * Retrive bookmark for an item
     * @param $data
     * @return mixed
     */
    public function getBookmark($data){
        $table = $this->getTable("Bookmark");
        $result = $table->getBookmark( $data["item_id"], $data["user_id"], $data["item_type"]);

        return $result;
    }

}