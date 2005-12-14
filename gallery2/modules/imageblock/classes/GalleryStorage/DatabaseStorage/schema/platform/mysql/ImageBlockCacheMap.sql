
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXImageBlockCacheMap (
  DB_COLUMN_PREFIXuserId
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXitemType
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXitemTimestamp
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXitemId
      int(11)
    
    NOT NULL
  
    , 
  
    INDEX (DB_COLUMN_PREFIXuserId
      ,
    DB_COLUMN_PREFIXitemType)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'ImageBlockCacheMap',
      1,
      0
      );

  