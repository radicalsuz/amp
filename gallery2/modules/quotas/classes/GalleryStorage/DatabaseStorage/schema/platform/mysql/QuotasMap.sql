
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXQuotasMap (
  DB_COLUMN_PREFIXuserOrGroupId
      int(11)
    
      ,
    DB_COLUMN_PREFIXquotaSize
      int(11)
    
    NOT NULL
  
    ,
  
    INDEX DB_TABLE_PREFIXQuotasMap_48775 (DB_COLUMN_PREFIXuserOrGroupId)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'QuotasMap',
      1,
      0
      );

  