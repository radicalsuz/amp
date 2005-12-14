<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                version="1.0">

  <xsl:output 
    method="xml" 
    indent="yes"
    doctype-system="../../../../../../../lib/tools/dtd/DatabaseTableDefinition2.0.dtd"/>

  <xsl:template match="class">
    <table>
      <table-name><xsl:value-of select="schema/schema-name"/></table-name>

      <xsl:apply-templates select="schema"/>

      <xsl:if test="requires-id">
        <column>
          <column-name>id</column-name>
          <column-type>INTEGER</column-type>
          <column-size>MEDIUM</column-size>
          <not-null/>
        </column>
      </xsl:if>

      <xsl:apply-templates select="map"/>
      <xsl:apply-templates select="member"/>

      <xsl:if test="requires-id">
        <key primary="true">
          <column-name>id</column-name>
        </key>
      </xsl:if>

      <xsl:for-each select="member[indexed]">
        <index>
          <column-name><xsl:value-of select="member-name"/></column-name>
        </index>
      </xsl:for-each>

      <xsl:for-each select="map/member[indexed]">
        <index>
          <column-name><xsl:value-of select="member-name"/></column-name>
        </index>
      </xsl:for-each>

      <xsl:for-each select="member[unique]">
        <key>
          <column-name><xsl:value-of select="member-name"/></column-name>
        </key>
      </xsl:for-each>

      <xsl:for-each select="map/member[unique]">
        <key>
          <column-name><xsl:value-of select="member-name"/></column-name>
        </key>
      </xsl:for-each>

      <xsl:for-each select="member[primary]">
        <key primary="true">
          <column-name><xsl:value-of select="member-name"/></column-name>
        </key>
      </xsl:for-each>

      <xsl:for-each select="map/member[primary]">
        <key primary="true">
          <column-name><xsl:value-of select="member-name"/></column-name>
        </key>
      </xsl:for-each>
    </table>
  </xsl:template>

  <xsl:template match="schema">
    <schema>
      <schema-major><xsl:value-of select="schema-major"/></schema-major>
      <schema-minor><xsl:value-of select="schema-minor"/></schema-minor>
    </schema>
  </xsl:template>

  <xsl:template match="member">
    <column>
      <column-name><xsl:value-of select="member-name"/></column-name>
      <column-type><xsl:value-of select="member-type"/></column-type>
      <xsl:if test="member-size">
        <column-size><xsl:value-of select="member-size"/></column-size>
      </xsl:if>
      <xsl:if test="not(member-size)">
        <column-size>MEDIUM</column-size>
      </xsl:if>
      <xsl:if test="required">
        <xsl:element name="not-null">
          <xsl:attribute name="empty"><xsl:value-of select="required/@empty"/></xsl:attribute>
        </xsl:element>
      </xsl:if>
      <xsl:if test="primary">
        <not-null/>
      </xsl:if>
    </column>
  </xsl:template>

  <xsl:template match="map">
    <xsl:for-each select="member">
      <column>
        <column-name><xsl:value-of select="member-name"/></column-name>
        <column-type><xsl:value-of select="member-type"/></column-type>
        <xsl:if test="member-size">
          <column-size><xsl:value-of select="member-size"/></column-size>
        </xsl:if>
        <xsl:if test="not(member-size)">
          <column-size>MEDIUM</column-size>
        </xsl:if>
        <xsl:if test="required">
          <xsl:element name="not-null">
            <xsl:attribute name="empty"><xsl:value-of select="required/@empty"/></xsl:attribute>
          </xsl:element>
        </xsl:if>
        <xsl:if test="primary">
          <not-null/>
        </xsl:if>
      </column>
    </xsl:for-each>

    <xsl:for-each select="key">
      <xsl:if test="@primary='true'">
        <key primary="true">
          <xsl:for-each select="member-name">
            <column-name><xsl:value-of select="."/></column-name>
          </xsl:for-each>
        </key>
      </xsl:if>

      <xsl:if test="@primary!='true'">
        <key>
          <xsl:for-each select="member-name">
            <column-name><xsl:value-of select="."/></column-name>
          </xsl:for-each>
        </key>
      </xsl:if>
    </xsl:for-each>

    <xsl:for-each select="index">
      <index>
        <xsl:for-each select="member-name">
          <column-name><xsl:value-of select="."/></column-name>
        </xsl:for-each>
      </index>
    </xsl:for-each>

  </xsl:template>

</xsl:stylesheet>
