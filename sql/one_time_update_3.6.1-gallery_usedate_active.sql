update gallery set usedate = 1 where `date` < '2006-07-11' and !isnull(`date`) and `date` != '0000-00-00';
