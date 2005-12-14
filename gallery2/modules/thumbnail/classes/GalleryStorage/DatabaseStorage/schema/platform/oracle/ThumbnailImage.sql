
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXThumbnailImage (
  DB_COLUMN_PREFIXid
      INTEGER
    
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
    DB_COLUMN_PREFIXitemMimeTypes
      VARCHAR2(
       128 
      )
    
    );

  
    ALTER TABLE DB_TABLE_PREFIXThumbnailImage
      ADD PRIMARY KEY (DB_COLUMN_PREFIXid)
  
      ADD UNIQUE (DB_COLUMN_PREFIXfileName)
  ;
  

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'ThumbnailImage',
      1,
      1
      );

  