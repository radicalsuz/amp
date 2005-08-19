<?php
require_once('AMP/System/Copy/Override.inc.php');
require_once('AMP/System/Copy/Paginator.inc.php');

class AMPSystem_Copy {
    var $datatable;
    var $dbcon;
    var $original;
    var $copy_id;
    var $parent;
    var $id;
    var $current_original;
    var $current_copy;
    var $paginator;
    var $UpdateParent_Field;

    var $dependencies;
    var $override;
    var $error;

    function AMPSystem_Copy(&$dbcon) {

        $this->init($dbcon);
    }

    function init($dbcon, $original_id = null) {
        $this->dbcon=&$dbcon;
        if (isset($original_id)) {
            $this->setOriginal("id=".$original_id);
        }
    }

    function setOriginal($crit=null) {

        $sql = "Select * from ".$this->datatable." where $crit";
        if ($this->original =  $this->dbcon->GetAll($sql)) {
            $this->id = get_class($this) ." ## ". $crit;
            return true;
        }
    }

    function error_throw($message) {
        if (isset($this->parent)) {
            $this->parent->error .= $message;
        } else {
            $this->error .= $message;
        }
    }

    function execute() {
        if ($this->original) {
            $this->dbcon->StartTrans();

            foreach ($this->original as $aCopy) {
                $this->makeCopy($aCopy);
            }

            if ($this->dbcon->CompleteTrans()) {
                return $this->copy_id;
            } else {
                return false;
            }
        }
        return false;
    }

    function skipCopy() {
        if (isset($this->paginator) && $this->paginator->isCopied( $this )) {
            if ($item_id = $this->paginator->getNewPage($this->datatable, $this->current_original['id'])){
                $this->updateCopy($item_id);
            }
            return true;
        }
        return false;
    }


    function makeCopy($aCopy) {
        $this->current_original=$aCopy;
        if ($this->skipCopy()) return false;

        $sql = $this->copySQL($aCopy);
        $this->dbcon->Execute($sql);

        if (!$this->dbcon->HasFailedTrans()) {
            $this->current_copy['id'] = $this->copy_id = $this->dbcon->Insert_ID();
            $this->Paginate();
            $this->copyDependencies();
            $this->updateParent();
        } else {
            $this->dbcon->FailTrans();
            $this->error_throw($this->dbcon->ErrorMsg() . "<BR>". $sql."<BR> From ".$this->id);
        }
    }
    function &PaginateOn() {
        if (!isset($this->paginator)) {
            return ($this->paginator = new AMPSystem_CopyPaginator($this));
        }
    }

    function Paginate() {
        if (!isset($this->paginator)) return false;

        $this->paginator->addPage( $this );
    }

    function updateParent() {
        if (isset($this->UpdateParent_Field)) {
            $sql = $this->updateSQL( $this->parent->current_copy['id'], array($this->UpdateParent_Field=>$this->current_copy[$this->UpdateParent_Child]), $this->parent->datatable );
            #print $sql.'<BR>';
            $this->dbcon->Execute($sql) or $this->error_throw($this->dbcon->ErrorMsg().'<BR>Update Parent<BR>'.$sql);
        }
    }

    function &makeCopier( $classname ) {

        $filename = 'AMP/System/' . str_replace("_", DIRECTORY_SEPARATOR, $classname) . '/Copy.inc.php';

        if (file_exists_incpath($filename)) {
            include_once ($filename);
        }
        $new_class = 'AMPSystem_' . $classname . '_Copy' ;

        if (class_exists($new_class)) return new $new_class($this->dbcon);

        trigger_error ( 'AMPSystem_Copier: '.$new_class.' not found' );
        return false;
    }

    function startChildCopier ( $def, &$parent ) {
        $this->parent = & $parent;

        if (!isset($def['parent_field'])) $def['parent_field']='id';
        $parent_id = $parent->current_original[ $def['parent_field'] ];

        if ($def['parent_field'] != 'id' ) { 
            $this->UpdateParent_Field = $def['parent_field'];
            $this->UpdateParent_Child = $def['child_field'];
        }
        $criteria = $def['child_field'].'='.$parent_id;

        return $this->startCopier( $criteria, $def );
    }

    function startCopier ( $criteria, $def ) {


        if ($this->setOriginal($criteria)) {
            $this->setupOverrides($def);
            $this->paginator = & $this->parent->paginator;

            return true;
        }
        return false;
    }


    function copyDependencies() {
        if (!is_array($this->dependencies)) return false;

        $this->setOverride('id', $this->copy_id, $this->current_original['id'] );

        foreach ($this->dependencies as $depend) {

            if ($d_Copier = $this->makeCopier($depend['class'])) {

                if ($d_Copier->startChildCopier($depend, $this)) {
                    $d_Copier->execute();
                }
            }

        }
        return true;
    }

    function overrideValues($value_array) {
        unset($value_array['id']);

        if (!isset($this->override)) return $value_array;

        return $this->override->returnValues($value_array, $this->id);
    }

    function setOverride( $field, $new_value, $old_value = null) {
        if (!isset($this->override)) $this->override = new AMPSystem_CopyOverride();
        $this->override->addOverride($this->id, $field, $new_value, $old_value);
    }


    function setupOverrides( $def ) {
        extract($def);

        $this->override = $this->parent->override;

        if (isset($child_field) && isset($parent_field)) {
            $this->setOverride($child_field, $this->parent->current_copy[$parent_field]);
        }

        if (isset($override)) $this->override->Inherit($override, $this->id, $this->parent->id);

    }

    function ErrorMsg() {
       return $this->error;
    }


    function updateCopy($id) {
        if (isset($this->override)) {
            $sql = "Select * from ".$this->datatable." where id=".$id;
            if ($tempCopy = $this->dbcon->GetRow($sql)) {

                $newCopy = $this->overrideValues($tempCopy);
                $updateSet = array_diff($newCopy, $tempCopy);

                $sql = $this->updateSQL($id, $updateSet);
                $this->dbcon->Execute($sql);
            }
        }
    }
        

    function copySQL ($value_array) {

            if (!is_array($value_array)) return false;
            $value_array = $this->overrideValues($value_array);
            $this->current_copy = $value_array;

            $db_fields = $this->dbcon->MetaColumnNames( $this->datatable);
            $def_fields = array_keys( $value_array );

            $write_fields = array_intersect ($db_fields, $def_fields);
            foreach ($write_fields as $field) {
                $value = $value_array[$field];
                $values[] = $this->dbcon->qstr( $value );
            }

            $sql = "INSERT into ".$this->datatable ." (";
            
            $sql .= join (", ",$write_fields) .
                    ") VALUES (" .
                    join (", ", $values ) .
                    ")";
            #print '<P>'.get_class($this).'<BR>the sql<P>'.$sql;
            return $sql;
    }


    function updateSQL ( $id, $data, $datatable = null ) {

        $dbcon =& $this->dbcon;
        if (!isset($datatable)) $datatable = $this->datatable;

        $sql = "UPDATE ". $datatable." SET ";

        foreach ($data as $field => $value) {
            $elements[] = $field . "=" . $dbcon->qstr( $value );
        }

        $sql .= implode( ", ", $elements );
        $sql .= " WHERE id=" . $dbcon->qstr( $id );

        return $sql;

    }
}
?>
