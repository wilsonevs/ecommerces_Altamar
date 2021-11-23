<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/doc">
        <html>
            <head>
                <style type="text/css">
                    <xsl:value-of select="css"/>
                </style>
            </head>
            <body>
                <div class="main">
                    <div class="intro">
                        <xsl:value-of select="intro" disable-output-escaping="yes" />
                    </div>

                    <xsl:for-each select="method">
                        <table class="method" cellpadding="0" cellspacing="0">
                            <caption>
                                Method:
                                <xsl:value-of select="@name"/>
                            </caption>
                            <tr>
                                <th class="param-label">Param</th>
                                <th class="param-name">
                                    <a>
                                        <xsl:attribute name="href">
                                            <xsl:text>#</xsl:text>
                                            <xsl:value-of select="@param" />
                                        </xsl:attribute>
                                        <xsl:value-of select="@param"/>
                                        <xsl:value-of select="@param_array" />
                                    </a>
                                </th>
                                <th class="return-label">Return</th>
                                <th class="return-name">
                                    <a>
                                        <xsl:attribute name="href">
                                            <xsl:text>#</xsl:text>
                                            <xsl:value-of select="@return" />
                                        </xsl:attribute>
                                        <xsl:value-of select="@return"/>
                                        <xsl:value-of select="@return_array" />
                                    </a>
                                </th>
                            </tr>
                            <tr>
                                <td colspan="4" class="doc">
                                    <xsl:value-of select="@doc" disable-output-escaping="yes"/>
                                </td>
                            </tr>
                        </table>
                        <br/>
                        <br/>
                    </xsl:for-each>

                    <hr/>
                    <br/>
                    <br/>
                    <br/>

                    <xsl:for-each select="datatype">
                        <a>
                            <xsl:attribute name="name">
                                <xsl:value-of select="@name"/>
                            </xsl:attribute>
                        </a>
                        <table class="datatype" cellpadding="0" cellspacing="0">
                            <caption>
                                DataType: 
                                <xsl:value-of select="@name"/>
                            </caption>
                            <tr>
                                <th class="prop-name-title column-title">Name</th>
                                <th class="prop-type-title column-title">Type</th>
                                <th class="prop-doc-title column-title">Doc</th>
                            </tr>
                            <xsl:for-each select="property">
                                <tr>
                                    <td class="prop-name">
                                        <xsl:value-of select="@name" />
                                    </td>
                                    <td class="prop-type">
                                        <a>
                                            <xsl:attribute name="href">
                                                <xsl:value-of select="concat('#',@type)" />
                                            </xsl:attribute>
                                            <xsl:value-of select="@type" />
                                            <xsl:value-of select="@array" />
                                        </a>
                                    </td>
                                    <td class="prop-doc" valign="top">
                                        <xsl:value-of select="@doc" disable-output-escaping="yes" />
                                        <span></span>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </table>
                        <br/>
                        <br/>
                    </xsl:for-each>


                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
