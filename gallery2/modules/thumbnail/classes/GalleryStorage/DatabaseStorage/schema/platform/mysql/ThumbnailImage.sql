
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXThumbnailImage (
  DB_COLUMN_PREFIXid
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXfileName
      varchar(
      
          128
        
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXmimeType
      varchar(
      
          128
        
      )
    
      ,
    DB_COLUMN_PREFIXsize
      int(11)
    
      ,
    DB_COLUMN_PREFIXwidth
      int(11)
    
      ,
    DB_COLUMN_PREFIXheight
      int(11)
    
      ,
    DB_COLUMN_PREFIXitemMimeTypes
      varchar(
      
          128
        
      )
    
    , 
  
      PRIMARY KEY (DB_COLUMN_PREFIXid)
    
      ,
    
      UNIQUE KEY (DB_COLUMN_PREFIXfileName)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'ThumbnailImage',
      1,
      1
      );

  