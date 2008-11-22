;<?php
//[content map]
//root section=1

[system]
version_id="3.8.1"
;unique id=AMP_DB_NAME
menu_version=1
cache="memcache"
cache_timeout=600
file_owner=false


[system memcache]
server="localhost"
port=11211

[component map]
filename="ComponentMap.inc.php"
classname="ComponentMap"

[sort]
asc=" ASC"
desc=" DESC"
end="zzzzzzzzzzzzzzzzzzz"
max="9999999999"

[null date value]
db="0000-00-00"
form="2001-01-01"
rss="1969-12-31"

[null datetime value]
db=   "0000-00-00 00:00:00"
form= "2001-11-30 00:00:00"
form2="2001-01-01 00:00:00"
unix= "1969-12-31 16:33:26"
unix2="1969-12-31 16:33:25"

;used in Article.inc.php to subtract the current date, creating a meaningful ordering value
;should always be in the future :)
[future datetime value]
unix="2100-12-31 16:33:26"

[system setting]
db_id=1

[phplist]
config_admin_id=1

[adodb]
replace_inserted=2
replace_updated=1

[system item type]
form="form"
event="event"
article="article"
file="file"
gallery="gallery"
gallery_image="gallery_image"
link="link"

[path]
phpgacl="phpgacl"
phpgacl_admin="phpgacl/admin"

[system mode]
production=1

;?>
