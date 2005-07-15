<?php

class AMP_MenuComponent_FWmenuScriptItem extends AMP_MenuComponent {

		var $core_template = "\"%2\$s\", \"location='%1\$s'\"";
		var $template = "fw_menu_%1\$s.addMenuItem( %2\$s );\n";
        var $folder_template = "\n\nwindow.fw_menu_%1\$s = new Menu(\"%2\$s\",,,,,,,,);";
		var $folder_core_template = "fw_menu_%1\$s , \"location='%2\$s'\"";
        var $separator = "fw_menu_%1\$s.addMenuSeparator();\n";

		var $css_template = "%1\$s, %2\$s, \"%3\$s\", %4\$s, \"#%5\$s\", \"#%6\$s\", \"#%7\$s\", \"#%8\$s\");\nfw_menu_%9\$s.menuItemBorder=0;
										 	fw_menu_%9\$s.fontWeight=\"%12\$s\";\n fw_menu_%9\$s.hideOnMouseOut=true;\n fw_menu_%9\$s.childMenuIcon = \"%10\$s\";\n fw_menu_%9\$s.childMenuIconHover = \"%11\$s\";\n";
		var $css_template_vars = array('width', 'height', 'font_face', 'font_size','color','color_hover','bgcolor','bgcolor_hover', 'id', 'bg_image', 'bg_image_hover', 'font_weight');
		var $returned_output = false;

		function AMP_MenuComponent_FWmenuScriptItem( &$menu, $def ) {
				$this->init($menu, $def);
		}

        function _register_def( $def ) {
            if (isset($def['separator']) && $def['separator']) $this->addSeparator();
        }

        function addSeparator() {
            $this->template = $this->separator . $this->template;
        }

		function setCSS($recursive=true) {
				if (!$this->hasChildren()) return false;
				
				if ($styleinfo = $this->evalCSS()) {
						$this->folder_template = str_replace(",,,,,,,);", $styleinfo, $this->folder_template);
				}

				if ($recursive) $this->doChildren('setCSS');
		}

		function output($recursive = true) {
				$output = $this->make_folder() . $this->output_parent_id();	
				$this->returned_output=true;
				return $output;
		}

		function make_folder() {
				if ($this->hasChildren() && (!$this->returned_output)) {

					  $output = $this->doChildren("make_folder");	

						$output .=  sprintf($this->folder_template, $this->id, $this->label);
						$output .=  $this->outputChildren(false);
						$this->returned_output = true;
						return $output;
				}
		}
						
		function output_parent_id() {
				//we don't have a single root menu in this case, so skip this for root
				//folders
				$show_folder = ($this->parent->id != $this->menu->name);

				return ( $show_folder? sprintf($this->template, $this->parent->id, $this->make_core()):"");
		}

		function make_core() {

				if ($this->hasChildren()) return sprintf($this->folder_core_template, $this->id, $this->href);
			  return parent::make_core();	
		}
}

class AMP_MenuComponent_FWmenuScriptHeader extends AMP_MenuComponent {
		var $template = "
            <script language=\"JavaScript1.2\" src=\"/scripts/fw_menu.js\"></script>
            <script language=\"javascript\" type = \"text/javascript\">
            
            //<!--
            
            function getOffTop ( item ) {
                if ( item.offsetParent) {
                    return (item.offsetTop + getOffTop( item.offsetParent));
                }

                return item.offsetTop;
            }

            function getOffRight( item ) {
                if (item.offsetParent) {
                    return (getRightSize( item ) + getOffRight( item.offsetParent ));
                }

                return getRightSize ( item );
            }

            function getRightSize( item ) {
                return window.innerWidth - (getOffLeft( item ) + item.offsetWidth);
            }


            function getOffLeft ( item ) {
                if ( item.offsetParent ) {
                    
                    return (item.offsetLeft + getOffLeft( item.offsetParent));
                }

                return item.offsetLeft;
            }

						function fwLoadMenus() {
								if (window.fw_menu_%1\$s) return;
								%2\$s

								window.fw_menu_%1\$s.writeMenus();
						}
						fwLoadMenus();
						//-->
						</script>";
			
		  var $css_template = "
        div.FW_menuItem { cursor: pointer; cursor: hand; }
        span.FW_menuItem { display:block; padding: 3px 0px 0px 0px; color: #%1\$s}
        span.FW_menuItem:hover, .FW_menuItemHilite {  display:block; padding: 3px 0px 0px 0px; color: #%2\$s; }
				";
			var $css_template_vars = array ("color", "color_hover");
			var $_child_component = "AMP_MenuComponent_FWmenuScriptItem";


			function AMP_MenuComponent_FWmenuScriptHeader ( &$menu, $def ) {
					$this->init( $menu, $def );
			}

			function output() {
					$lastChild=end($this->getChildren());
					return sprintf($this->template, $lastChild->id, $this->make_core());
			}

			
			function make_core() {
					return $this->outputChildren();
			}
}
?>
