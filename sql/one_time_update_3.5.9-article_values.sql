update articles set usedate = if( isnull(usedate) or usedate = 0, 1, 0) where class=2;
update articles set enteredby=1 where enteredby=0 or isnull(enteredby); 
update articles set picture='' where isnull( picuse ) or picuse=0; 
