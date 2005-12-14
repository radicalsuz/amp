
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXPendingUser (
  DB_COLUMN_PREFIXid
      INTEGER
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXuserName
      VARCHAR2(
       32 
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXfullName
      VARCHAR2(
       128 
      )
    
      ,
    DB_COLUMN_PREFIXhashedPassword
      VARCHAR2(
       128 
      )
    
      ,
    DB_COLUMN_PREFIXemail
      VARCHAR2(
       128 
      )
    
      ,
    DB_COLUMN_PREFIXlanguage
      VARCHAR2(
       128 
      )
    
      ,
    DB_COLUMN_PREFIXregistrationKey
      VARCHAR2(
       32 
      )
    
    );

  
    ALTER TABLE DB_TABLE_PREFIXPendingUser
      ADD PRIMARY KEY (DB_COLUMN_PREFIXid)
  
      ADD UNIQUE (DB_COLUMN_PREFIXuserName)
  ;
  

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'PendingUser',
      1,
      0
      );

  