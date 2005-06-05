<?php
class AMPSystem_CopyOverride {
    var $override_sets;

    function AMPSystem_CopyOverride() {
    }

    function addOverride($id, $fieldname, $new_value, $old_value=null ) {
        $this->override_sets[$id][$fieldname][] = new AMPSystem_CopyOverrideValue($fieldname, $new_value, $old_value);

    }

    function Inherit($set, $id, $parent_id) {
        foreach ($set as $ovtype=>$fieldset) {
            if ($parent_set = $this->activeValues($parent_id, $ovtype)) {;
                #print "<P>" . $id ." inheriting $ovtype from ". $parent_id.'<BR>';
                foreach ($parent_set as $parent_val) {
                    $old_value = $parent_val->old_value;
                    $new_value = $parent_val->new_value;
                    foreach ($fieldset as $newfield) {
                        $this->addOverride($id, $newfield, $new_value, $old_value);
                        #print $newfield . " was " . $old_value . " now $new_value<BR>";
                    }
                }
            }
        }
    }

    function activeValues($id, $which=null) {
        if (!isset($this->override_sets[$id])) return false;

        if (isset($which)) {
            if (isset($this->override_sets[$id][$which])) return $this->override_sets[$id][$which];
            return false;
        }

        return $this->override_sets[$id];
    }

    function returnValues($value_array, $id) {

        foreach ($this->activeValues($id) as $ov1=>$override_set) {

            foreach ($override_set as $ov => $override) {
                if (!isset($value_array[$override->fieldname])) continue;


                if ($override->text_replace && $override->old_value) {
                    $value_array[$override->fieldname] = 
                        str_replace($override->old_value, $override->new_value, $value_array[$override->fieldname]);
                } else {
                    $value_array[$override->fieldname] = $override->new_value;
                }
            }
        }
        return $value_array;
    }

}

class AMPSystem_CopyOverrideValue {

    var $fieldname;
    var $old_value;
    var $new_value;
    var $text_replace;

    function AMPSystem_CopyOverrideValue ($fieldname, $new_value, $old_value=null) {
        $this->new_value = $new_value;
        $this->fieldname = $fieldname;
        
        if (isset($old_value)) {
            $this->old_value = $old_value;
            $this->text_replace = true;
        } else {
            $this->text_replace = false;
        }
    }
}
?>
