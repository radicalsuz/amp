GALLERYDIR ?= ../../../../../../..
TOOLDIR ?= $(GALLERYDIR)/lib/tools
CLASSDIR ?= ../../../..
XSLFILE ?= $(GALLERYDIR)/modules/core/classes/GalleryStorage/DatabaseStorage/schema/xml-src/DbXml.xsl
G2_TMPDIR ?= tmp
EXTRAXML ?= $(wildcard *.xml)
OUTDIR ?= ../xml-out

include $(TOOLDIR)/GNUmakefile.inc

CLASSFILES  = $(wildcard $(CLASSDIR)/*.class)
XMLFILES = $(patsubst $(CLASSDIR)/%.class,$(OUTDIR)/%.xml,$(CLASSFILES))

xml: $(G2_TMPDIR) $(OUTDIR) $(XMLFILES)
ifneq ($(EXTRAXML),)
	cp $(EXTRAXML) $(OUTDIR)
endif

$(G2_TMPDIR):
	mkdir $(G2_TMPDIR)

$(OUTDIR):
	mkdir $(OUTDIR)

$(OUTDIR)/%.xml: $(G2_TMPDIR)/%.xml $(XSLFILE)
	if [ -f $< ]; then \
		$(SAXON) $< $(XSLFILE) > $@; \
		$(VALIDATOR) $@; \
	fi

$(G2_TMPDIR)/%.xml: $(CLASSDIR)/%.class
	perl $(TOOLDIR)/bin/extractClassXml.pl \
		--dtd=../$(TOOLDIR)/dtd/GalleryClass2.0.dtd $^ \
		--out=$@
	if [ -f $@ ]; then $(VALIDATOR) $@; fi

scrub: clean

clean:
	rm -rf $(G2_TMPDIR)
	rm -rf $(OUTDIR)

# Gmake will automatically delete $(G2_TMPDIR)/.xml files after creating .xml files
# because it thinks that they're intermediate files.  But, we want to save
# them (for now), so mark them as PRECIOUS.
#
.PRECIOUS: $(G2_TMPDIR)/%.xml
