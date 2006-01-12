<?php
/*
 * $RCSfile: GalleryAlbumItem.inc,v $
 *
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2005 Bharat Mediratta
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
/**
 * @package GalleryCore
 * @subpackage ClassInterfaces
 * @version $Revision: 1.45 $ $Date: 2005/08/23 03:49:04 $
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * Interface class for the GalleryAlbumItem class.
 * (Class schema version: 1.1)
 *
 * This file is automatically generated from XML embedded in the
 * class, combined with the ClassCode.xsl file.  You should not modify
 * it by hand, as your changes will be lost next time this file is
 * rebuilt.  If you want to add more getters/setters, you should add
 * them in the core class.  If you want to change the format of this
 * file, then you should edit the XSL template.
 *
 * @package GalleryCore
 * @subpackage Classes
 */
class GalleryAlbumItem extends GalleryAlbumItem_core {
  
    /**
     * Return meta information about the members of this class
     *
     * @return array member => array(type, class)
     */
    function getPersistentMemberInfo() {
	$meta = parent::getPersistentMemberInfo();
	$meta['members']['theme'] = array('class' => 'GalleryAlbumItem', 'type' => STORAGE_TYPE_STRING);
    
	$meta['members']['orderBy'] = array('class' => 'GalleryAlbumItem', 'type' => STORAGE_TYPE_STRING);
    
	$meta['members']['orderDirection'] = array('class' => 'GalleryAlbumItem', 'type' => STORAGE_TYPE_STRING);
    
	return $meta;
    }

    /**
     * Get the data from this persistent object as an associative array
     *
     * @return array memberName => memberValue
     */
    function getMemberData() {
	$data = parent::getMemberData();
        $data['theme'] = isset($this->_theme) ? $this->_theme : null;
        $data['orderBy'] = isset($this->_orderBy) ? $this->_orderBy : null;
        $data['orderDirection'] = isset($this->_orderDirection) ? $this->_orderDirection : null;
	return $data;
    }
  
    /**
     * Return the name of this class with the proper mix of uppercase and
     * lowercase (get_class() always returns lowercase)
     *
     * @return string class name
     */
    function getClassName() {
	return 'GalleryAlbumItem';
    }

    /** 
     * Return the path to the PHP file for this class, relative to the gallery2 dir
     * 
     * @return string path
     */ 
    function getClassFile() { 
        return 'modules/' .  
            basename(dirname(dirname(dirname(__FILE__)))) .  
            '/classes/GalleryAlbumItem.class'; 
    }
    
    /**
     * Get the value of theme
     *
     * @return STRING the value
     */
    function gettheme() {
	if (!isset($this->_theme)) {
	    return null;
	}
	return $this->_theme;
    }

    /**
     * Set the value of theme
     *
     * @param STRING the value
     */
    function settheme($value) {
	/* Convert unset values to null, to avoid generating warnings. */
	$value = isset($value) ? $value : null;
        $value = $this->_truncateString($value, 32);
	$orig = isset($this->_theme) ? $this->_theme : null;

        /* Only take action if the value actually changes */
        if ($value != $orig) {
	    $this->setModifiedFlag('theme', $value);
	    $this->_theme = $value;
	}
    }
  
    /**
     * Get the value of orderBy
     *
     * @return STRING the value
     */
    function getorderBy() {
	if (!isset($this->_orderBy)) {
	    return null;
	}
	return $this->_orderBy;
    }

    /**
     * Set the value of orderBy
     *
     * @param STRING the value
     */
    function setorderBy($value) {
	/* Convert unset values to null, to avoid generating warnings. */
	$value = isset($value) ? $value : null;
        $value = $this->_truncateString($value, 128);
	$orig = isset($this->_orderBy) ? $this->_orderBy : null;

        /* Only take action if the value actually changes */
        if ($value != $orig) {
	    $this->setModifiedFlag('orderBy', $value);
	    $this->_orderBy = $value;
	}
    }
  
    /**
     * Get the value of orderDirection
     *
     * @return STRING the value
     */
    function getorderDirection() {
	if (!isset($this->_orderDirection)) {
	    return null;
	}
	return $this->_orderDirection;
    }

    /**
     * Set the value of orderDirection
     *
     * @param STRING the value
     */
    function setorderDirection($value) {
	/* Convert unset values to null, to avoid generating warnings. */
	$value = isset($value) ? $value : null;
        $value = $this->_truncateString($value, 32);
	$orig = isset($this->_orderDirection) ? $this->_orderDirection : null;

        /* Only take action if the value actually changes */
        if ($value != $orig) {
	    $this->setModifiedFlag('orderDirection', $value);
	    $this->_orderDirection = $value;
	}
    }
  
}
?>