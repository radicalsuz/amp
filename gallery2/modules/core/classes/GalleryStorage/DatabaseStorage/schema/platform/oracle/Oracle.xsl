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
    );

  <xsl:for-each select="index">
    CREATE INDEX <xsl:call-template name="indexName"/>
    ON <xsl:value-of select="$tablePrefix"/><xsl:value-of select="../table-name"/>
    (<xsl:call-template name="indexColumns"/>);
  </xsl:for-each>

  <xsl:if test="key">
    ALTER TABLE <xsl:value-of select="$tablePrefix"/><xsl:value-of select="table-name"/>
    <xsl:for-each select="key">
      ADD <xsl:call-template name="key"/>
    </xsl:for-each>;
  </xsl:if>

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
    <xsl:if test="add/column or add/key or remove/column or remove/key or alter">
      ALTER TABLE <xsl:value-of select="$tablePrefix"/><xsl:value-of select="table-name"/>
      <xsl:apply-templates select="remove"/>
      <xsl:apply-templates select="alter"/>
      <xsl:apply-templates select="add"/>
      ;
    </xsl:if>

    <xsl:for-each select="remove/index">
      DROP INDEX <xsl:call-template name="indexName"/>;
    </xsl:for-each>

    <xsl:for-each select="add/index">
      CREATE INDEX <xsl:call-template name="indexName"/>
      ON <xsl:value-of select="$tablePrefix"/><xsl:value-of select="../../table-name"/>
      (<xsl:call-template name="indexColumns"/>);
    </xsl:for-each>

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
    <xsl:if test="column">
      ADD (
	<xsl:for-each select="column">
	  <xsl:call-template name="column"/>
	  <xsl:if test="position()!=last()">
	   ,
	  </xsl:if>
	</xsl:for-each>
      )
    </xsl:if>

    <xsl:for-each select="key">
      ADD <xsl:call-template name="key"/>
    </xsl:for-each>
  </xsl:template>

  <!-- Change/remove -->
  <xsl:template match="remove">
    <xsl:if test="column">
      DROP (
	<xsl:for-each select="column">
	  <xsl:value-of select="$columnPrefix"/><xsl:value-of select="column-name"/>
	  <xsl:if test="position()!=last()">
	    ,
	  </xsl:if>
	</xsl:for-each>
      )
    </xsl:if>

    <xsl:for-each select="key">
      DROP <xsl:call-template name="key"/>
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
    MODIFY (
    <xsl:for-each select="column">
      <xsl:call-template name="column"/>
      <xsl:if test="position()!=last()">
	,
      </xsl:if>
    </xsl:for-each>
    )
  </xsl:template>

  <!-- General purpose column definition -->
  <xsl:template name="column">
    <xsl:value-of select="$columnPrefix"/><xsl:value-of select="column-name"/>
  <xsl:choose>
    <xsl:when test="column-type='INTEGER'">
      INTEGER
    </xsl:when>
    <xsl:when test="column-type='BIT'">
      INTEGER
    </xsl:when>
    <xsl:when test="column-type='STRING'">
      VARCHAR2(
      <xsl:choose>
	<xsl:when test="column-size='SMALL'"> 32 </xsl:when>
	<xsl:when test="column-size='MEDIUM'"> 128 </xsl:when>
	<xsl:when test="column-size='LARGE'"> 255 </xsl:when>
      </xsl:choose>
      )
    </xsl:when>
    <xsl:when test="column-type='TEXT'">
      VARCHAR2(4000)
    </xsl:when>
    <xsl:when test="column-type='BOOLEAN'">
      NUMBER(1)
    </xsl:when>
    <xsl:when test="column-type='TIMESTAMP'">
      TIMESTAMP
    </xsl:when>
    <xsl:otherwise>
      UNKNOWN COLUMN TYPE: <xsl:value-of select="column-type"/>
    </xsl:otherwise>
  </xsl:choose>
  <xsl:if test="not-null[@empty='disallowed']">
    NOT NULL
  </xsl:if>
  </xsl:template>

  <!-- General purpose key definition -->
  <xsl:template name="key">
    <xsl:choose>
      <xsl:when test="@primary='true'">PRIMARY KEY</xsl:when>
      <xsl:otherwise>UNIQUE</xsl:otherwise>
    </xsl:choose> (<xsl:call-template name="indexColumns"/>)
  </xsl:template>

  <!-- General purpose index definition -->
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

  <xsl:template name="indexColumns">
    <xsl:for-each select="column-name">
      <xsl:value-of select="$columnPrefix"/><xsl:value-of select="."/>
      <xsl:if test="position()!=last()">
	,
      </xsl:if>
    </xsl:for-each>
  </xsl:template>

  <!-- InstallerTest -->
  <xsl:template match="test">
    <xsl:apply-templates select="*"/> 
  </xsl:template>

</xsl:stylesheet>
