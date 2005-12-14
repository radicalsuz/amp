
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXG1MigrateMap (
  DB_COLUMN_PREFIXitemId
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXg1album
      varchar(
      
          128
        
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXg1item
      varchar(
      
          128
        
      )
    
    , 
  
      PRIMARY KEY (DB_COLUMN_PREFIXitemId)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'G1MigrateMap',
      1,
      0
      );

  