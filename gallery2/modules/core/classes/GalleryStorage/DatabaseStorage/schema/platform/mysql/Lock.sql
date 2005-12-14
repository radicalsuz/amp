
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXLock (
  DB_COLUMN_PREFIXlockId
      int(11)
    
      ,
    DB_COLUMN_PREFIXreadEntityId
      int(11)
    
      ,
    DB_COLUMN_PREFIXwriteEntityId
      int(11)
    
      ,
    DB_COLUMN_PREFIXfreshUntil
      int(11)
    
      ,
    DB_COLUMN_PREFIXrequest
      int(11)
    
    ,
  
    INDEX DB_TABLE_PREFIXLock_11039 (DB_COLUMN_PREFIXlockId)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'Lock',
      1,
      0
      );

  