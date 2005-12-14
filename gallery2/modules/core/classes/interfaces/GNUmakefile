GALLERYDIR ?= ../../../..
TOOLDIR ?= $(GALLERYDIR)/lib/tools
CLASSDIR ?= ..
XSLFILE ?= $(GALLERYDIR)/modules/core/classes/interfaces/ClassCode.xsl
PACKAGE ?= GalleryCore
G2_TMPDIR ?= tmp

include $(TOOLDIR)/GNUmakefile.inc

CLASSFILES  = $(wildcard $(CLASSDIR)/*.class)
XMLFILES    = $(patsubst $(CLASSDIR)/%.class,%.xml,$(CLASSFILES))
INCFILES    = $(patsubst %.xml,%.inc,$(XMLFILES))

inc: $(G2_TMPDIR) $(INCFILES)

$(G2_TMPDIR):
	mkdir $(G2_TMPDIR)

%.inc: $(G2_TMPDIR)/%.xml $(XSLFILE)
	if [ -f $< ]; then $(SAXON) $< $(XSLFILE) \
		| perl -pe 's/\@\@package\@\@/$(PACKAGE)/' \
		> $@; fi

$(G2_TMPDIR)/%.xml: $(CLASSDIR)/%.class
	perl $(TOOLDIR)/bin/extractClassXml.pl \
		--dtd=../$(TOOLDIR)/dtd/GalleryClass2.0.dtd \
		--stub-ok \
		--out $@ \
		$^ 
	if [ -f $@ ]; then $(VALIDATOR) $@; fi

clean:
	rm -rf $(G2_TMPDIR)

scrub: clean
	rm -f *.inc

# Gmake will automatically delete $(G2_TMPDIR)/*.xml files after creating .inc files
# because it thinks that they're intermediate files.  But, we want to save
# them (for now), so mark them as PRECIOUS.
#
.PRECIOUS: $(G2_TMPDIR)/%.xml
