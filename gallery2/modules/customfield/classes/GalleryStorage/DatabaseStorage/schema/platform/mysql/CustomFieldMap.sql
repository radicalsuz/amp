
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXCustomFieldMap (
  DB_COLUMN_PREFIXitemId
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXfield
      varchar(
      
          128
        
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXvalue
      varchar(
      
          255
        
      )
    
      ,
    DB_COLUMN_PREFIXsetId
      int(11)
    
      ,
    DB_COLUMN_PREFIXsetType
      int(11)
    
    , 
  
    INDEX (DB_COLUMN_PREFIXitemId)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'CustomFieldMap',
      1,
      0
      );

  