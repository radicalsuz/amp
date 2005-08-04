
CREATE TABLE IF NOT EXISTS `maps` (
  `id` int(11) NOT NULL auto_increment,
  `name` text,
  `title` text,
  `description` text,
  `intro` text,
  `type` varchar(50) default NULL,
  `map_table` varchar(50) default NULL,
  `extra_sql` text,
  `default_color` varchar(50) default NULL,
  `background_color` varchar(50) default NULL,
  `outline_color` varchar(50) default NULL,
  `default_point_color` varchar(50) default NULL,
  `default_point_size` varchar(50) default NULL,
  `default_point_src` varchar(50) default NULL,
  `defualt_point_opacity` varchar(50) default NULL,
  `line_color` varchar(50) default NULL,
  `font_size` varchar(50) default NULL,
  `arc_color` varchar(50) default NULL,
  `target` varchar(50) default NULL,
  `state_info_icon` varchar(50) default NULL,
  `map_height` varchar(50) default NULL,
  `map_width` varchar(50) default NULL,
  `label_field` varchar(50) default NULL,
  `hover_field` varchar(50) default NULL,
  `point_url` varchar(50) default NULL,
  `state_url` varchar(50) default NULL,
  `center_lat` varchar(50) default NULL,
  `center_long` varchar(50) default NULL,
  `span_lat` varchar(50) default NULL,
  `span_long` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;