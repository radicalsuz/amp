<?php

global $g2moddata;
if (isset($g2moddata['sidebarBlocksHtml'])){
    echo '<div id="gsSidebar" class="gcBorder1">';
    foreach ($g2moddata['sidebarBlocksHtml'] as $value) {
        echo $value;
    }
    echo '</div>';
}
?>