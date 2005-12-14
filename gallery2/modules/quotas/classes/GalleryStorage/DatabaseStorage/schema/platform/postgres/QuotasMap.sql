
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXQuotasMap (
  DB_COLUMN_PREFIXuserOrGroupId
      INTEGER
    
      ,
    DB_COLUMN_PREFIXquotaSize
      INTEGER
    
      NOT NULL
    
    );

  
    CREATE INDEX DB_TABLE_PREFIXQuotasMap_48775
    ON DB_TABLE_PREFIXQuotasMap
    (DB_COLUMN_PREFIXuserOrGroupId);

  

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'QuotasMap',
      1,
      0
      );

  