
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXItem (
  DB_COLUMN_PREFIXid
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXcanContainChildren
      int(1)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXdescription
      text
    
      ,
    DB_COLUMN_PREFIXkeywords
      varchar(
      
	  255
	
      )
    
      ,
    DB_COLUMN_PREFIXownerId
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXsummary
      varchar(
      
	  255
	
      )
    
      ,
    DB_COLUMN_PREFIXtitle
      varchar(
      
	  128
	
      )
    
      ,
    DB_COLUMN_PREFIXviewedSinceTimestamp
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXoriginationTimestamp
      int(11)
    
    NOT NULL
  
    ,
  
      PRIMARY KEY (DB_COLUMN_PREFIXid)
    
    ,
  
    INDEX DB_TABLE_PREFIXItem_99070 (DB_COLUMN_PREFIXkeywords)
    
      ,
    
    INDEX DB_TABLE_PREFIXItem_21573 (DB_COLUMN_PREFIXownerId)
    
      ,
    
    INDEX DB_TABLE_PREFIXItem_54147 (DB_COLUMN_PREFIXsummary)
    
      ,
    
    INDEX DB_TABLE_PREFIXItem_90059 (DB_COLUMN_PREFIXtitle)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'Item',
      1,
      1
      );

  