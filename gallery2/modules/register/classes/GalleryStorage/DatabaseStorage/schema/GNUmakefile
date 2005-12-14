all:
	cd xml-src && $(MAKE) -$(MAKEFLAGS) xml
	cd platform && $(MAKE) -$(MAKEFLAGS) sql

%:
	cd xml-src && $(MAKE) -$(MAKEFLAGS) $@
	cd platform && $(MAKE) -$(MAKEFLAGS) $@
