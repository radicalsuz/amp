
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXMultiLangItemMap (
  DB_COLUMN_PREFIXitemId
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXlanguage
      varchar(
      
          32
        
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXtitle
      varchar(
      
          128
        
      )
    
      ,
    DB_COLUMN_PREFIXsummary
      varchar(
      
          255
        
      )
    
      ,
    DB_COLUMN_PREFIXdescription
      text
    
    , 
  
      PRIMARY KEY (DB_COLUMN_PREFIXitemId
        ,
      DB_COLUMN_PREFIXlanguage)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'MultiLangItemMap',
      1,
      0
      );

  