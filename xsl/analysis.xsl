<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
    xmlns:oai="http://www.openarchives.org/OAI/2.0/" xmlns:mods="http://www.loc.gov/mods/v3">
    <xsl:output method="html"/>
    <xsl:template match="/">

            <xsl:for-each select="//oai:record[oai:header[not(@status='deleted')]]">
                <xsl:variable name="geo"
                    select="normalize-space(.//mods:mods/mods:subject/mods:geographic[1])"/>
                <xsl:variable name="date"
                    select="normalize-space(.//mods:dateCreated[@keyDate='yes'][1])"/>
                <xsl:variable name="thumburl"
                    select="normalize-space(.//mods:url[@access='preview'][1])"/>
                <xsl:variable name="type" select="normalize-space(.//mods:genre[1])"/>
                <xsl:variable name="originalurl"
                    select="normalize-space(.//mods:url[@usage='primary display'][1])"/>
                <xsl:variable name="id" select="./oai:header/oai:identifier"/>
                <xsl:variable name="rights" select="normalize-space(.//mods:accessCondition[@type='use and reproduction'][1])"/>
                <xsl:if test="not($geo) or not($date) or not($thumburl) or not($type) or not($rights)">
                    <record>
                        <url>
                            <xsl:value-of select="$originalurl"/>
                        </url>
                        <title><xsl:value-of select=".//mods:mods/mods:titleInfo/mods:title[1]"/></title>
                        <xsl:if test="not($geo)">
                            <geo>Missing</geo>
                        </xsl:if>
                        <xsl:if test="not($date)">
                            <date>Missing</date>
                        </xsl:if>
                        <xsl:if test="not($thumburl)">
                            <thumburl>Missing</thumburl>
                        </xsl:if>
                        <xsl:if test="not($type)">
                            <type>Missing</type>
                        </xsl:if>
                        <xsl:if test="not($rights)">
                            <rights>Missing</rights>
                        </xsl:if>
                        <oai_id><xsl:value-of select="$id"/></oai_id>
                    </record>
                </xsl:if>
            </xsl:for-each>

    </xsl:template>
</xsl:stylesheet>
