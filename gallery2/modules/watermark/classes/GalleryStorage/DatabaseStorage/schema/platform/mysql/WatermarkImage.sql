
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXWatermarkImage (
  DB_COLUMN_PREFIXid
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXapplyToPreferred
      int(1)
    
      ,
    DB_COLUMN_PREFIXapplyToResizes
      int(1)
    
      ,
    DB_COLUMN_PREFIXapplyToThumbnail
      int(1)
    
      ,
    DB_COLUMN_PREFIXname
      varchar(
      
          128
        
      )
    
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
    DB_COLUMN_PREFIXownerId
      int(11)
    
      ,
    DB_COLUMN_PREFIXxPercentage
      varchar(
      
          32
        
      )
    
      ,
    DB_COLUMN_PREFIXyPercentage
      varchar(
      
          32
        
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
      'WatermarkImage',
      1,
      1
      );

  