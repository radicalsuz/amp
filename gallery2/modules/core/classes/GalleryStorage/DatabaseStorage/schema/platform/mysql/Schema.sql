
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXSchema (
  DB_COLUMN_PREFIXname
      varchar(
      
	  128
	
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXmajor
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXminor
      int(11)
    
    NOT NULL
  
    ,
  
      PRIMARY KEY (DB_COLUMN_PREFIXname)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'Schema',
      1,
      0
      );

  