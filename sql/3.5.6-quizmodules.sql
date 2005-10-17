INSERT INTO `modules` ( `id` , `name` , `userdatamod` , `userdatamodid` , `file` , `perid` , `navhtml` , `publish` , `module_type` )
VALUES (
'46', 'Quiz', NULL , NULL , 'quiz.php' , '103' , NULL , '1', '1'
);


INSERT INTO `moduletext` ( `id` , `title` , `name` , `subtitile` , `test` , `html` , `searchtype` , `date` , `type` , `subtype` , `catagory` , `templateid` , `modid` )
VALUES (
'63', 'Quiz', 'Quiz', NULL , NULL , '0', 'quiz.php', '0000-00-00', '1' , '0' ,'0' ,'0' , '46'
);

INSERT INTO `per_description` ( `id` , `name` , `description` , `publish` )VALUES ('103', 'Quiz', NULL , '1');

INSERT INTO `permission` ( `id` , `groupid` , `perid` )VALUES ('', '1', '103'), ('', '3', '103');
