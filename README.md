Automated Installer Scripts
==

Installer scripts vary by Linux distro.  So far, I have Ubuntu 12.04 and Centos 6 available, and will add more as I need them.

The requirement to use these are pretty minimal, but they are designed to be served from an HTTP server that supports PHP.

I recommend dumping the directory in a web accessible directory, and call up the file you want to use in a web browser.  Pass Get parameters until you get the desired file, and then copy that URL into your PXE boot menu, ISO, whatever.
