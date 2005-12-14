all:
	cd interfaces && $(MAKE) -$(MAKEFLAGS)
	cd GalleryStorage/DatabaseStorage/schema && $(MAKE) -$(MAKEFLAGS)

%:
	cd interfaces && $(MAKE) -$(MAKEFLAGS) $@
	cd GalleryStorage/DatabaseStorage/schema && $(MAKE) -$(MAKEFLAGS) $@

