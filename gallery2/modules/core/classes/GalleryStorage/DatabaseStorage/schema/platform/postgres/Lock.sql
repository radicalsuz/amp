
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXLock (
  DB_COLUMN_PREFIXlockId
      INTEGER
    
      ,
    DB_COLUMN_PREFIXreadEntityId
      INTEGER
    
      ,
    DB_COLUMN_PREFIXwriteEntityId
      INTEGER
    
      ,
    DB_COLUMN_PREFIXfreshUntil
      INTEGER
    
      ,
    DB_COLUMN_PREFIXrequest
      INTEGER
    
    );

  
    CREATE INDEX DB_TABLE_PREFIXLock_11039
    ON DB_TABLE_PREFIXLock
    (DB_COLUMN_PREFIXlockId);

  

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'Lock',
      1,
      0
      );

  