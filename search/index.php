<?php

	require("config/config.php");

	$installBase = ereg_replace("(^.*)[/\\]$", "\\1", $config["installBase"]);

	/* Display header if set. */
	$header = $config["header"];
	if ($header) {
		if ($header[0] != $config["fileSeparator"] && $header[0] != ".")
			$header = $installBase . $config["fileSeparator"] . "config" . $config["fileSeparator"] . $header;
		if (is_readable($header)) {
			include($header);
		} else {
			printf("Error: Unable to access header '%s'. Verify \$config[\"installBase\"] is correct.<BR>\n", $header);
		}
	}

?>
	
	<TABLE WIDTH="100%" CELLSPACING="0" CELLPADDING="0" BORDER="0">
		<TR>
			<TD ALIGN="center">
				<FORM METHOD="get" ACTION="search.php" TARGET="_self">
					<TABLE CELLSPACING="0" CELLPADDING="0" BORDER="0">
						<TR>
							<TD><INPUT TYPE="text" NAME="q" SIZE="25" TABINDEX="1">&nbsp;</TD>
							<TD><INPUT TYPE="submit" VALUE="Search" TABINDEX="3">&nbsp;</TD>
							<TD>
								<SELECT NAME="r" TABINDEX="2">
									<OPTION VALUE="0">All results</OPTION>
									<OPTION VALUE="5">5 results</OPTION>
									<OPTION SELECTED VALUE="10">10 results</OPTION>
									<OPTION VALUE="20">20 results</OPTION>
									<OPTION VALUE="30">30 results</OPTION>
									<OPTION VALUE="50">50 results</OPTION>
								</SELECT>
							</TD>
						</TR>
						<TR>
							<TD>
								
							</TD>
							<TD COLSPAN="2">&nbsp;</TD>
						</TR>
					</TABLE>
				</FORM>
			</TD>
		</TR>
	</TABLE>
<?php

	/* Display footer if set. */
	$footer = $config["footer"];
	if ($footer) {
		if ($footer[0] != $config["fileSeparator"] && $footer[0] != ".")
			$footer = $installBase . $config["fileSeparator"] . "config" . $config["fileSeparator"] . $footer;
		if (is_readable($footer)) {
			include($footer);
		} else {
			printf("Error: Unable to access footer '%s'. Verify \$config[\"installBase\"] is correct.<BR>\n", $footer);
		}
	}

?>
