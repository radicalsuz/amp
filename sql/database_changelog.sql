ALTER TABLE `articles_version` CHANGE `pageorder` `pageorder` INT DEFAULT NULL;
ALTER TABLE `articles` CHANGE `pageorder` `pageorder` INT DEFAULT NULL;




CREATE TABLE userdata_plugins_fields (

    id int(11) NOT NULL auto_increment,
    plugin_id int(11) NOT NULL default '0',
    required tinyint(1) unsigned NOT NULL default '0',
    public tinyint(1) unsigned NOT NULL default '0',

    publish tinyint(1) unsigned NOT NULL default '0',

    f_label varchar(255) default NULL,

                f_size smallint(6) default NULL,

                                f_type enum('text','checkbox','textarea','header','hidden','select','radio','date','file','password','button','image','reset','submit','xbutton','advcheckbox','autocomplete','hierselect','html','link','static') default 'text',

                                                    f_values text,

                                                                        f_default text,

                                                                                            PRIMARY KEY  (id),

                                                                                                                    KEY plugin_id (plugin_id)

                                                                                                                                            ) ENGINE=MyISAM; 





                                                                                                                                                                                                            
                                                                                                                                                                                                            CREATE TABLE userdata_plugins_options (

                                                                                                                                                                                                              plugin_id int(11) NOT NULL default '0',

                                                                                                                                                                                                                name varchar(16) NOT NULL default '',

                                                                                                                                                                                                                  `value` varchar(255) NOT NULL default '',

                                                                                                                                                                                                                    PRIMARY KEY  (plugin_id,name),

                                                                                                                                                                                                                      KEY plugin_id (plugin_id)

                                                                                                                                                                                                                        ) ENGINE=MyISAM;



                                                                                                                                                                                                                                  

