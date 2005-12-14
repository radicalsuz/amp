
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXWatermarkImage (
  DB_COLUMN_PREFIXid
      INTEGER
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXapplyToPreferred
      NUMBER(1)
    
      ,
    DB_COLUMN_PREFIXapplyToResizes
      NUMBER(1)
    
      ,
    DB_COLUMN_PREFIXapplyToThumbnail
      NUMBER(1)
    
      ,
    DB_COLUMN_PREFIXname
      VARCHAR2(
       128 
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXfileName
      VARCHAR2(
       128 
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXmimeType
      VARCHAR2(
       128 
      )
    
      ,
    DB_COLUMN_PREFIXsize
      INTEGER
    
      ,
    DB_COLUMN_PREFIXwidth
      INTEGER
    
      ,
    DB_COLUMN_PREFIXheight
      INTEGER
    
      ,
    DB_COLUMN_PREFIXownerId
      INTEGER
    
      ,
    DB_COLUMN_PREFIXxPercentage
      VARCHAR2(
       32 
      )
    
      ,
    DB_COLUMN_PREFIXyPercentage
      VARCHAR2(
       32 
      )
    
    );

  
    ALTER TABLE DB_TABLE_PREFIXWatermarkImage
      ADD PRIMARY KEY (DB_COLUMN_PREFIXid)
  
      ADD UNIQUE (DB_COLUMN_PREFIXfileName)
  ;
  

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'WatermarkImage',
      1,
      1
      );

  