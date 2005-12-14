
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXUser (
  DB_COLUMN_PREFIXid
      int(11)
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXuserName
      varchar(
      
	  32
	
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXfullName
      varchar(
      
	  128
	
      )
    
      ,
    DB_COLUMN_PREFIXhashedPassword
      varchar(
      
	  128
	
      )
    
      ,
    DB_COLUMN_PREFIXemail
      varchar(
      
	  128
	
      )
    
      ,
    DB_COLUMN_PREFIXlanguage
      varchar(
      
	  128
	
      )
    
    ,
  
      PRIMARY KEY (DB_COLUMN_PREFIXid)
    
      ,
    
      UNIQUE KEY (DB_COLUMN_PREFIXuserName)
    

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'User',
      1,
      0
      );

  