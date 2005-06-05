<?php
class AMPSystem_CopyPaginator {

    var $pages;
    var $copier;

    function AMPSystem_CopyPaginator( &$copier ) {
        $this->copier = &$copier;
    }

    function addPage( &$copier ) {
        if (isset($copier->current_original['id'])) {
            $this->pages[$copier->datatable][$copier->current_original['id']] = $copier->current_copy['id'];
        }
    }

    function getNewPage ($datatable, $id) {
        if (!isset($this->pages[$datatable][$id])) return false;
        return $this->pages[$datatable][$id];
    }
    function isCopied( &$copier ) {
        if (!isset($copier->current_original['id'])) return false;
        $wascopied = (isset($this->pages[$copier->datatable][$copier->current_original['id']]));
        if (!$wascopied) $wascopied = $this->isCopy( $copier ); 
        #print ($copier->datatable).": ".$copier->current_original['id'].($wascopied?" YES YES ":"no").'<BR>';
        return $wascopied;
    }

    function isCopy ( &$copier ) {
        if (!isset($this->pages[$copier->datatable])) return false;
        return $this->getOldPage($copier->datatable, $copier->current_original['id']);
    }

    function getOldPage ($datatable, $id) {
        return array_search( $id, $this->pages[$datatable]);
    }
}
?>
