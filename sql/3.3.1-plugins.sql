CREATE TABLE IF NOT EXISTS userdata_plugins_options (

    plugin_id    INT            NOT NULL,
    name         VARCHAR(16)    NOT NULL,
    value        VARCHAR(255)   NOT NULL,

    PRIMARY KEY ( plugin_id, name ),

    INDEX (plugin_id),

);

CREATE TABLE IF NOT EXISTS userdata_plugins_fields (

    id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    plugin_id   INT NOT NULL, 

    required    TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    public      TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    publish     TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,

    f_label     VARCHAR(255) DEFAULT NULL,
    f_size      SMALLINT DEFAULT NULL,
    f_type      enum('text','checkbox','textarea','header','hidden','select',
                     'radio','date','file','password','button','image','reset',
                     'submit','xbutton','advcheckbox','autocomplete',
                     'hierselect','html','link','static') DEFAULT 'text',

    f_values    TEXT, -- Yuck, just don't hurt me.
    f_default   TEXT,

    INDEX (plugin_id),

);
