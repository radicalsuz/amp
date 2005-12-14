
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXExternalIdMap (
  DB_COLUMN_PREFIXexternalId
      varchar(
      
	  128
	
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXentityType
      varchar(
      
	  32
	
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXentityId
      int(11)
    
    NOT NULL
  
    ,
  
      PRIMARY KEY (DB_COLUMN_PREFIXexternalId
	,
      DB_COLUMN_PREFIXentityType)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'ExternalIdMap',
      1,
      0
      );

  