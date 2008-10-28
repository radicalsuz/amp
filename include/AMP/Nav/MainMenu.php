<?
class AMP_Nav_MainMenu{
    var $options = array();

    function AMP_Nav_MainMenu($options){
        $this->__construct($options);
    }

    function __construct($options){
        $this->options = $options;
    }

    function execute(){
        $menu_items= (isset($this->options['menu_items']) && ($this->options['menu_items'])) ? $this->options['menu_items']  : false;
        if (!$menu_items) return false;  
        $html = '<ul id="nav" class="main_menu">';

        foreach($menu_items as $menu_item){
            $html.= (isset($menu_item['title']) && $menu_item['title']) ? $this->render_menu_item_text($menu_item) : $this->render_menu_item_image($menu_item) ;
        }
        $html .= '</ul>';
        return $html;            
    }

    function nav_sub_section($type,$sort) {
	   $finder= new Section(AMP_Registry::getDbcon());
		if(isset($sort) && $sort){
        	$finder_source = &$finder->getSearchSource();
        	$finder_source->addSort($sort);
		}
    	$sections = $finder->find(array('parent'=>$type,'displayable'=>'1'));
        if (!$sections) return;
        $html = '<ul>';
    	foreach($sections as $section) {
	       		$html .= '<li><a href="section.php?id='.$section->id.'">'.$section->getName().'</a><img src="img/spacer.gif" height="4" width="3" align="left"> </li>'; 
	   }
    	$html .= '</ul>'; 
	   return $html;
    }

    function render_menu_item_text($menu_item){
            $title= (isset($menu_item['title']) && ($menu_item['title'])) ? $menu_item['title']  : '';
            $type=  (isset($menu_item['type']) && ($menu_item['type'])) ? $menu_item['type']  : false;
            $url=  (isset($menu_item['url']) && ($menu_item['url'])) ? $menu_item['url']  : false;
            $menu_item_html=    '<li><span class="main-menu-title"><a ';
            if  ($url)  $menu_item_html.= 'href="'.$url.'"';
            $menu_item_html.=   '>'.$title.
                                                '</a></span>';
            if($type) $menu_item_html.= $this->nav_sub_section($type) ;
            $menu_item_html.= "</li>";
            $html.= $menu_item_html;
            return $html;
    }

    function render_menu_item_image($menu_item){
			$html = '';
            $alt= (isset($menu_item['alt']) && ($menu_item['alt'])) ? $menu_item['alt']  : '';
            $name= (isset($menu_item['name']) && ($menu_item['name'])) ? $menu_item['name']  : '';
            $img_off=  (isset($menu_item['img_off']) && ($menu_item['img_off'])) ? $menu_item['img_off']  : 'img/spacer.gif';
            $img_on=  (isset($menu_item['img_on']) && ($menu_item['img_on'])) ? $menu_item['img_on']  : $img_off;
            $type=  (isset($menu_item['type']) && ($menu_item['type'])) ? $menu_item['type']  : false;
            $sort=  (isset($menu_item['sort']) && $menu_item['sort']) ? $menu_item['sort']  : false;
            $url=  (isset($menu_item['url']) && ($menu_item['url'])) ? $menu_item['url']  : false;
            $menu_item_html=    '<li> <a ';
            if  ($url)  $menu_item_html.= 'href="'.$url.'"';
            $menu_item_html.=   ' onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage(\''.
                                                $name.
                                                "','','".
                                                $img_on.
                                                '\',1)"><img src="'.
                                                $img_off.
                                                '" name="'.
                                                $name.
                                                '" border="0"'.
												' alt ="'.$alt.'"></a>';
            if($type) $menu_item_html.= $this->nav_sub_section($type, $sort) ;
            $menu_item_html.= "</li>";
            $html.= $menu_item_html;
            return $html;
    }
}
?>
