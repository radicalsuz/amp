update articles set usedate = if( isnull(usedate) or usedate = 0, 1, 0) where class=2;
update articles set enteredby=1 where enteredby=0 or isnull(enteredby); 
