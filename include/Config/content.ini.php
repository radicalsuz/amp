;<?php
[content list]
order_max=9999999999
display_max=300
limit_default=20
intro_display="ListIntro_Display"

[sectionlist]
articles=1
newsroom=2
subsections=5
articles_by_subsection=3
subsections_plus_articles=6
articles_aggregator=7

[section display]
default="ArticleSet_Display"
articles="ArticleSet_Display"
subsections="SectionSet_Display"
articles_aggregator="ArticleSet_Display"
grouped="Article_Public_List"

[content section listsort article]
alpha="title"
default="date DESC, id DESC"
newest="date DESC, id DESC"
ordered="if ( !isnull( pageorder ) and pageorder, pageorder, 9999999 ), if ( date != '0000-00-00', date, '2050-12-31') DESC, id DESC"

[content article search map]
list_prefix="legacy"

[content section listsort section]
alpha="type"
default="date2 DESC, id DESC"
newest="date2 DESC, id DESC"
ordered="if ( !isnull( textorder ) and textorder, textorder, 9999999 ), if ( date2 != '0000-00-00', date2, '2050-12-31') DESC, id DESC"

[content classlist display]
default="ContentClass_Display"
blog="ContentClass_Display_Blog"
frontpage="ContentClass_Display_FrontPage"

[content status]
live=1
draft=0
pending=2
revision=3

[content class]
default=1
frontpage=2
sectionheader=8
news=3
morenews=4
pressrelease=10
usersubmitted=9
actionitem=5
blog=20
sectionfooter=false

;used by AMPContent_Page
[content listtype]
class="class"
section="type"
frontpage="index"
region="region"
tag="tag"

;used by breadcrumb
[content pagetype]
article="article"
list="list"
tool="tool"

[content article]
blurb_length_default=750
blurb_length_max=9000

[article display]
default="Article_Display"
frontpage="ArticleDisplay_FrontPage"
news="ArticleDisplay_News"
pressrelease="ArticleDisplay_PressRelease"
blog="ArticleDisplay_Blog"

[content tag display]
default="Article_Public_List"

[content comment]
default_status=1

[content sidebar class]
default="sidebar_right"
left="sidebar_left"
right="sidebar_right"

[icon]
spacer="spacer.gif"
up="go-up.png"
down="go-down.png"
list="list_page.png"
list_add="list_page_create.png"
content="content_page.png"
content_add="content_page_create.png"
column_footer=false

word="worddoc.gif"
pdf="pdf.gif"
img="img.gif"
image="img.gif"
wmv="wmv.jpg"
flv="flv.jpg"
mov="mov.jpg"

[system icon]
edit="/system/images/edit.png"
view="/system/images/view2.png"
preview="/system/images/view.gif"
delete="/system/images/delete.png"
enlarge="/img/magnify-clip.png"
crop="/system/images/crop.png"

[include]
start_tag="{{"
end_tag="}}"

[image class]
original="original"
thumb="thumb"
optimized="pic"
crop="crop"

[image]
default_alignment="right"
path="/img/"
gallery_page_limit=24

[content nav]
limit_default=20
archive_limit=10
no_limit_qty=700
legacy_element_template=0

[content intro id]
default=1
frontpage=2
article=3

[content publicpage id]
article_input=41
article_response=49
search=40
links_display=12
comment_input=34
tags_display=28
share=33

[content rss]
fulltext=false
customformat=false

[module id]
content=19

[content section id]
tool_pages=2
trash=2
root=1

;[content section name]
;root=AMP_SITE_NAME

[content map]
root section=1

[content trackbacks]
enabled=false

[content document]
path="downloads"

[content document type]
pdf="pdf"
word="word"
default="file"
image="img"
mov="mov"
flv="flv"
wmv="wmv"

[content container id]
buffer=false
flash="AMP_flash"

[content display key]
flash="flash"
intro="intro"
buffer="buffer"

[content redirect]
sectionheaders_to_sections=true

[content nav block]
right="r"
left="l"

[content template token]
standard="[-%s nav-]"
body="[-body-]"

[content media url]
youtube_thumbnail="http://img.youtube.com/vi/%s/default.jpg"
youtube="http://youtube.com/watch?v=%s"

[content display excluded class]
sectionheader=8
usersubmitted=9
frontpage=2

[content workflow]
enabled=0

[content date]
format="F jS, Y"

[render article include]
media="render_media"
doc="render_document"
sidebar="render_sidebar"

[render article]
photocredit=false

[render display class]
publicpage="Article_Public_Detail_Page"
;?>
