<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:saxon="http://icl.com/saxon"
  xmlns:crc="http://icl.com/saxon"
  extension-element-prefixes="saxon crc"
  version="1.0">

  <xsl:output method="text"/>
  <xsl:variable name="tablePrefix">DB_TABLE_PREFIX</xsl:variable>
  <xsl:variable name="columnPrefix">DB_COLUMN_PREFIX</xsl:variable>

  <saxon:script implements-prefix="crc" language="java" src="java:gallery.CRC32"/>

  <!-- TABLE -->
  <xsl:template match="table">
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE <xsl:value-of select="$tablePrefix"/><xsl:value-of select="table-name"/> (
  <xsl:for-each select="column">
    <xsl:call-template name="column"/>
    <xsl:if test="position()!=last()">
      ,
    </xsl:if>
  </xsl:for-each>

  <xsl:if test="column and (key or index)">
    ,
  </xsl:if>

  <xsl:for-each select="key">
    <xsl:if test="@primary='true'">
      PRIMARY KEY (<xsl:call-template name="key"/>)
    </xsl:if>

    <xsl:if test="@primary!='true'">
      UNIQUE KEY (<xsl:call-template name="key"/>)
    </xsl:if>

    <xsl:if test="position()!=last()">
      ,
    </xsl:if>
  </xsl:for-each>

  <xsl:if test="key and index">
    ,
  </xsl:if>

  <xsl:for-each select="index">
    INDEX <xsl:call-template name="indexName"/> (<xsl:call-template name="index"/>)
    <xsl:if test="position()!=last()">
      ,
    </xsl:if>
  </xsl:for-each>

    ) TYPE=DB_TABLE_TYPE;

    INSERT INTO <xsl:value-of select="$tablePrefix"/>Schema (
      <xsl:value-of select="$columnPrefix"/>name,
      <xsl:value-of select="$columnPrefix"/>major,
      <xsl:value-of select="$columnPrefix"/>minor
      ) VALUES (
      '<xsl:value-of select="table-name"/>',
      <xsl:value-of select="schema/schema-major"/>,
      <xsl:value-of select="schema/schema-minor"/>
      );

  </xsl:template>

  <!-- CHANGE -->
  <xsl:template match="change">
    <xsl:if test="add or alter or remove">
      ALTER TABLE <xsl:value-of select="$tablePrefix"/><xsl:value-of select="table-name"/>
      <xsl:apply-templates select="remove"/>
      <xsl:if test="remove and (alter or add)">,</xsl:if>
      <xsl:apply-templates select="alter"/>
      <xsl:if test="alter and add">,</xsl:if>
      <xsl:apply-templates select="add"/>
      ;
    </xsl:if>

    UPDATE <xsl:value-of select="$tablePrefix"/>Schema
      SET <xsl:value-of select="$columnPrefix"/>major=<xsl:value-of
							   select="schema-to/schema-major"/>,
	  <xsl:value-of select="$columnPrefix"/>minor=<xsl:value-of
							   select="schema-to/schema-minor"/>
      WHERE <xsl:value-of select="$columnPrefix"/>name='<xsl:value-of select="table-name"/>' AND
	  <xsl:value-of select="$columnPrefix"/>major=<xsl:value-of
							   select="schema-from/schema-major"/> AND
	  <xsl:value-of select="$columnPrefix"/>minor=<xsl:value-of
							   select="schema-from/schema-minor"/>;
  </xsl:template>

  <!-- Change/add -->
  <xsl:template match="add">
    <xsl:for-each select="column">
      ADD COLUMN <xsl:call-template name="column"/>
    <xsl:if test="position()!=last()">
      ,
    </xsl:if>
    </xsl:for-each>

    <xsl:if test="column and (key or index)">,</xsl:if>

    <xsl:for-each select="key">
      ADD UNIQUE KEY (<xsl:call-template name="key"/>)
    <xsl:if test="position()!=last()">
      ,
    </xsl:if>
    </xsl:for-each>

    <xsl:if test="key and index">,</xsl:if>

    <xsl:for-each select="index">
      ADD INDEX <xsl:call-template name="indexName"/> (<xsl:call-template name="index"/>)
    <xsl:if test="position()!=last()">
      ,
    </xsl:if>
    </xsl:for-each>
  </xsl:template>

  <!-- Change/remove -->
  <xsl:template match="remove">
    <xsl:for-each select="column">
      DROP COLUMN <xsl:value-of select="$columnPrefix"/><xsl:value-of select="column-name"/>
    <xsl:if test="position()!=last()">
      ,
    </xsl:if>
    </xsl:for-each>

    <xsl:if test="column and (key or index)">,</xsl:if>

    <xsl:for-each select="key">
      DROP KEY <xsl:value-of select="$columnPrefix"/><xsl:value-of select="column-name"/>
    <xsl:if test="position()!=last()">
      ,
    </xsl:if>
    </xsl:for-each>

    <xsl:if test="key and index">,</xsl:if>

    <xsl:for-each select="index">
      DROP INDEX <xsl:call-template name="indexName"/>
    <xsl:if test="position()!=last()">
      ,
    </xsl:if>
    </xsl:for-each>

    <!-- REMOVE table -->
    <xsl:if test="table-name">
      DROP TABLE <xsl:value-of select="$tablePrefix"/><xsl:value-of select="table-name"/>;

      DELETE FROM <xsl:value-of select="$tablePrefix"/>Schema
      WHERE <xsl:value-of select="$columnPrefix"/>name = '<xsl:value-of select="table-name"/>';
    </xsl:if>
  </xsl:template>

  <!-- Change/alter -->
  <xsl:template match="alter">
    <xsl:for-each select="column">
      MODIFY COLUMN <xsl:call-template name="column"/>
    <xsl:if test="position()!=last()">
      ,
    </xsl:if>
    </xsl:for-each>
  </xsl:template>

  <!-- General purpose column definition -->
  <xsl:template name="column">
    <xsl:value-of select="$columnPrefix"/><xsl:value-of select="column-name"/>
  <xsl:choose>
    <xsl:when test="column-type='INTEGER'">
      int(11)
    </xsl:when>
    <xsl:when test="column-type='BIT'">
      int(11)
    </xsl:when>
    <xsl:when test="column-type='STRING'">
      varchar(
      <xsl:choose>
	<xsl:when test="column-size='SMALL'">
	  32
	</xsl:when>
	<xsl:when test="column-size='MEDIUM'">
	  128
	</xsl:when>
	<xsl:when test="column-size='LARGE'">
	  255
	</xsl:when>
      </xsl:choose>
      )
    </xsl:when>
    <xsl:when test="column-type='TEXT'">
      text
    </xsl:when>
    <xsl:when test="column-type='BOOLEAN'">
      int(1)
    </xsl:when>
    <xsl:when test="column-type='TIMESTAMP'">
      datetime
    </xsl:when>
    <xsl:otherwise>
      UNKNOWN COLUMN TYPE: <xsl:value-of select="column-type"/>
    </xsl:otherwise>
  </xsl:choose>
  <xsl:if test="not-null">
    NOT NULL
  </xsl:if>
  </xsl:template>

  <!-- General purpose key definition -->
  <xsl:template name="key">
    <xsl:variable name="keyColumnName" saxon:assignable="yes"/>
    <xsl:for-each select="column-name">
      <xsl:value-of select="$columnPrefix"/><xsl:value-of select="."/>

      <saxon:assign name="keyColumnName"><xsl:value-of select="."/></saxon:assign>
      <xsl:for-each select="/table/column">
	<xsl:if test="column-name=$keyColumnName">
	  <xsl:if test="column-type='TEXT'">
	  (255)
	  </xsl:if>
	</xsl:if>
      </xsl:for-each>

      <xsl:if test="position()!=last()">
	,
      </xsl:if>
    </xsl:for-each>
  </xsl:template>

  <!-- General purpose index definition -->
  <xsl:template name="index">
    <xsl:variable name="indexColumnName" saxon:assignable="yes"/>
    <xsl:for-each select="column-name">
      <xsl:value-of select="$columnPrefix"/><xsl:value-of select="."/>

      <saxon:assign name="indexColumnName"><xsl:value-of select="."/></saxon:assign>
      <xsl:for-each select="/table/column">
	<xsl:if test="column-name=$indexColumnName">
	  <xsl:if test="column-type='TEXT'">
	  (255)
	  </xsl:if>
	</xsl:if>
      </xsl:for-each>
    <xsl:if test="position()!=last()">
      ,
    </xsl:if>
    </xsl:for-each>
  </xsl:template>

  <xsl:template name="indexName">
    <xsl:value-of select="crc:reset()"/>
    <xsl:for-each select="column-name">
      <xsl:value-of select="crc:update(.)"/>
    </xsl:for-each>
    <xsl:value-of select="$tablePrefix"/><xsl:choose>
      <xsl:when test="../table-name"><xsl:value-of select="../table-name"/></xsl:when>
      <xsl:otherwise><xsl:value-of select="../../table-name"/></xsl:otherwise>
    </xsl:choose>_<xsl:value-of select="crc:getValue()"/>
  </xsl:template>

  <!-- InstallerTest -->
  <xsl:template match="test">
    <xsl:apply-templates select="*"/>
  </xsl:template>

</xsl:stylesheet>
