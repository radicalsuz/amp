<?php

class UserDataSearch_Form extends AMPSearchForm {
    var $fieldFile = "AMP/UserData/Search/Fields.xml";

    function UserDataSearch_Form () {
        $this->init( 'UDMSearch' );
    }

    function setDynamicValues() {
		$regionset=new Region();
        $this->setFieldValueSet( 'state', $regionset->regions['US AND CANADA'] )
        $this->setFieldValueSet( 'country', $regionset->regions['WORLD'] )
    }
}

?>
