
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXImageBlockCacheMap (
  DB_COLUMN_PREFIXuserId
      INTEGER
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXitemType
      INTEGER
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXitemTimestamp
      INTEGER
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXitemId
      INTEGER
    
    NOT NULL
  
    );

  
    CREATE INDEX DB_TABLE_PREFIXImageBlockCacheMap_1627 
    ON DB_TABLE_PREFIXImageBlockCacheMap
    (DB_COLUMN_PREFIXuserId
        ,
      DB_COLUMN_PREFIXitemType);

  

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'ImageBlockCacheMap',
      1,
      0
      );

  