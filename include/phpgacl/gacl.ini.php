;<? if (; //Cause parse error to hide from prying eyes?> 
;
; *WARNING* 
;
; DO NOT PUT THIS FILE IN YOUR WEBROOT DIRECTORY. 
;
; *WARNING*
;
; Anyone can view your database password if you do!
;
debug 			= FALSE

;
;Database
;
db_type 		= AMP_DB_TYPE 
db_host			= AMP_DB_HOST
db_user			= AMP_DB_USER 
db_password		= AMP_DB_PASS 
db_name			= AMP_DB_NAME 
db_table_prefix		= "acl_"

;
;Caching
;
caching			= FALSE
force_cache_expire	= TRUE
cache_dir		= "/cache"
cache_expire_time	= 600

;
;Admin interface
;
items_per_page 		= 100
max_select_box_items 	= 100
max_search_return_items = 200

;NO Trailing slashes
smarty_dir 		= "phpgacl/admin/smarty/libs"
smarty_template_dir 	= "phpgacl/admin/templates"
smarty_compile_dir 	= "/home/speakoutnow/custom/templates_c"

