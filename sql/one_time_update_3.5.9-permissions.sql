insert into roles select id, name, description from per_group;
insert into roles_users select id, permission from users;
update users set status = 1;
