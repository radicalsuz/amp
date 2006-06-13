Installation
============
1.Place the contents of the html folder where it can be accessed from the web.
2.Place the contents of the cgi-bin folder in a directory where the execution of cgi-bin scripts are allowed.
3.Edit the file upload_settings.inc to match your settings.
4.Place upload_settings.inc one step above your DOCUMENT_ROOT. For example, if your html documents are in /var/www/html, upload_settings.inc should be in /var/www, or if your html documents are in c:\apache\htdocs, place upload_settings.inc in c:\apache. 
5.Done!

Troubleshooting
===============
The most common reason for this not working is because perl is not installed or is not working properly.
You can test the perl script by adding ?test to its url, for example
http://localhost/cgi-bin/upload.cgi?test
A success message should be printed. If that doesn't work, either perl is not installed or it's not working properly, please check your error log. One common cause for this is problems with permissions and ownership. Make sure the file is executable (try chmod 755) and that the owner of the script is permitted to execute cgi scripts (in many configurations the root user isn't allowed to execute cgi scripts).

For other problems, please visit the Sourceforge project page for support, http://sourceforge.net/projects/tesupload/


This package is based on megaupload by Raditha Dissanyake (http://www.raditha.com).

All files in this archive are subject to the Mozilla Public License (included), except for the Prototype library (prototype.js) which is distributed under the terms of an MIT-style license (see http://prototype.conio.net/)
