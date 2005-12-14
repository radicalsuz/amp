
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXRewriteMap (
  DB_COLUMN_PREFIXpattern
      varchar(
      
          255
        
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXmodule
      varchar(
      
          32
        
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXruleId
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXmatch
      varchar(
      
          128
        
      )
    
    , 
  
      PRIMARY KEY (DB_COLUMN_PREFIXpattern)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'RewriteMap',
      1,
      0
      );

  