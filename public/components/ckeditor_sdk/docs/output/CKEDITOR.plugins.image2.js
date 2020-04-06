Ext.data.JsonP.CKEDITOR_plugins_image2({"tagname":"class","name":"CKEDITOR.plugins.image2","autodetected":{},"files":[{"filename":"plugin.js","href":"plugin49.html#CKEDITOR-plugins-image2"}],"singleton":true,"members":[{"name":"checkHasNaturalRatio","tagname":"method","owner":"CKEDITOR.plugins.image2","id":"method-checkHasNaturalRatio","meta":{}},{"name":"getLinkAttributesGetter","tagname":"method","owner":"CKEDITOR.plugins.image2","id":"method-getLinkAttributesGetter","meta":{}},{"name":"getLinkAttributesParser","tagname":"method","owner":"CKEDITOR.plugins.image2","id":"method-getLinkAttributesParser","meta":{}},{"name":"getNatural","tagname":"method","owner":"CKEDITOR.plugins.image2","id":"method-getNatural","meta":{}}],"alternateClassNames":[],"aliases":{},"id":"class-CKEDITOR.plugins.image2","component":false,"superclasses":[],"subclasses":[],"mixedInto":[],"mixins":[],"parentMixins":[],"requires":[],"uses":[],"html":"<div><pre class=\"hierarchy\"><h4>Files</h4><div class='dependency'><a href='source/plugin49.html#CKEDITOR-plugins-image2' target='_blank'>plugin.js</a></div></pre><div class='doc-contents'><p>A set of Enhanced Image (image2) plugin helpers.</p>\n</div><div class='members'><div class='members-section'><div class='definedBy'>Defined By</div><h3 class='members-title icon-method'>Methods</h3><div class='subsection'><div id='method-checkHasNaturalRatio' class='member first-child not-inherited'><a href='#' class='side expandable'><span>&nbsp;</span></a><div class='title'><div class='meta'><span class='defined-in' rel='CKEDITOR.plugins.image2'>CKEDITOR.plugins.image2</span><br/><a href='source/plugin49.html#CKEDITOR-plugins-image2-method-checkHasNaturalRatio' target='_blank' class='view-source'>view source</a></div><a href='#!/api/CKEDITOR.plugins.image2-method-checkHasNaturalRatio' class='name expandable'>checkHasNaturalRatio</a>( <span class='pre'>image</span> ) : Boolean<span class=\"signature\"></span></div><div class='description'><div class='short'>Checks whether the current image ratio matches the natural one\nby comparing dimensions. ...</div><div class='long'><p>Checks whether the current image ratio matches the natural one\nby comparing dimensions.</p>\n<h3 class=\"pa\">Parameters</h3><ul><li><span class='pre'>image</span> : <a href=\"#!/api/CKEDITOR.dom.element\" rel=\"CKEDITOR.dom.element\" class=\"docClass\">CKEDITOR.dom.element</a><div class='sub-desc'>\n</div></li></ul><h3 class='pa'>Returns</h3><ul><li><span class='pre'>Boolean</span><div class='sub-desc'>\n</div></li></ul></div></div></div><div id='method-getLinkAttributesGetter' class='member  not-inherited'><a href='#' class='side expandable'><span>&nbsp;</span></a><div class='title'><div class='meta'><span class='defined-in' rel='CKEDITOR.plugins.image2'>CKEDITOR.plugins.image2</span><br/><a href='source/plugin49.html#CKEDITOR-plugins-image2-method-getLinkAttributesGetter' target='_blank' class='view-source'>view source</a></div><a href='#!/api/CKEDITOR.plugins.image2-method-getLinkAttributesGetter' class='name expandable'>getLinkAttributesGetter</a>( <span class='pre'></span> ) : Function<span class=\"signature\"></span></div><div class='description'><div class='short'>Returns an attribute getter function. ...</div><div class='long'><p>Returns an attribute getter function. Default getter comes from the Link plugin\nand is documented by <a href=\"#!/api/CKEDITOR.plugins.link-method-getLinkAttributes\" rel=\"CKEDITOR.plugins.link-method-getLinkAttributes\" class=\"docClass\">CKEDITOR.plugins.link.getLinkAttributes</a>.</p>\n\n<p><strong>Note:</strong> It is possible to override this method and use a custom getter e.g.\nin the absence of the Link plugin.</p>\n\n<p><strong>Note:</strong> If a custom getter is used, a data model format it produces\nmust be compatible with <a href=\"#!/api/CKEDITOR.plugins.link-method-getLinkAttributes\" rel=\"CKEDITOR.plugins.link-method-getLinkAttributes\" class=\"docClass\">CKEDITOR.plugins.link.getLinkAttributes</a>.</p>\n\n<p><strong>Note:</strong> A custom getter must understand the data model format produced by\n<a href=\"#!/api/CKEDITOR.plugins.image2-method-getLinkAttributesParser\" rel=\"CKEDITOR.plugins.image2-method-getLinkAttributesParser\" class=\"docClass\">getLinkAttributesParser</a> to work correctly.</p>\n        <p>Available since: <b>4.5.5</b></p>\n<h3 class='pa'>Returns</h3><ul><li><span class='pre'>Function</span><div class='sub-desc'><p>A function that gets (composes) link attributes.</p>\n</div></li></ul></div></div></div><div id='method-getLinkAttributesParser' class='member  not-inherited'><a href='#' class='side expandable'><span>&nbsp;</span></a><div class='title'><div class='meta'><span class='defined-in' rel='CKEDITOR.plugins.image2'>CKEDITOR.plugins.image2</span><br/><a href='source/plugin49.html#CKEDITOR-plugins-image2-method-getLinkAttributesParser' target='_blank' class='view-source'>view source</a></div><a href='#!/api/CKEDITOR.plugins.image2-method-getLinkAttributesParser' class='name expandable'>getLinkAttributesParser</a>( <span class='pre'></span> ) : Function<span class=\"signature\"></span></div><div class='description'><div class='short'>Returns an attribute parser function. ...</div><div class='long'><p>Returns an attribute parser function. Default parser comes from the Link plugin\nand is documented by <a href=\"#!/api/CKEDITOR.plugins.link-method-parseLinkAttributes\" rel=\"CKEDITOR.plugins.link-method-parseLinkAttributes\" class=\"docClass\">CKEDITOR.plugins.link.parseLinkAttributes</a>.</p>\n\n<p><strong>Note:</strong> It is possible to override this method and use a custom parser e.g.\nin the absence of the Link plugin.</p>\n\n<p><strong>Note:</strong> If a custom parser is used, a data model format produced by the parser\nmust be compatible with <a href=\"#!/api/CKEDITOR.plugins.image2-method-getLinkAttributesGetter\" rel=\"CKEDITOR.plugins.image2-method-getLinkAttributesGetter\" class=\"docClass\">getLinkAttributesGetter</a>.</p>\n\n<p><strong>Note:</strong> If a custom parser is used, it should be compatible with the\n<a href=\"#!/api/CKEDITOR.plugins.link-method-parseLinkAttributes\" rel=\"CKEDITOR.plugins.link-method-parseLinkAttributes\" class=\"docClass\">CKEDITOR.plugins.link.parseLinkAttributes</a> data model format. Otherwise the\nLink plugin dialog may not be populated correctly with parsed data. However\nas long as Enhanced Image is <strong>not</strong> used with the Link plugin dialog, any custom data model\nwill work, being stored as an internal property of Enhanced Image widget's data only.</p>\n        <p>Available since: <b>4.5.5</b></p>\n<h3 class='pa'>Returns</h3><ul><li><span class='pre'>Function</span><div class='sub-desc'><p>A function that parses attributes.</p>\n</div></li></ul></div></div></div><div id='method-getNatural' class='member  not-inherited'><a href='#' class='side expandable'><span>&nbsp;</span></a><div class='title'><div class='meta'><span class='defined-in' rel='CKEDITOR.plugins.image2'>CKEDITOR.plugins.image2</span><br/><a href='source/plugin49.html#CKEDITOR-plugins-image2-method-getNatural' target='_blank' class='view-source'>view source</a></div><a href='#!/api/CKEDITOR.plugins.image2-method-getNatural' class='name expandable'>getNatural</a>( <span class='pre'>image</span> ) : Object<span class=\"signature\"></span></div><div class='description'><div class='short'>Returns natural dimensions of the image. ...</div><div class='long'><p>Returns natural dimensions of the image. For modern browsers\nit uses natural(Width|Height). For old ones (IE8) it creates\na new image and reads the dimensions.</p>\n<h3 class=\"pa\">Parameters</h3><ul><li><span class='pre'>image</span> : <a href=\"#!/api/CKEDITOR.dom.element\" rel=\"CKEDITOR.dom.element\" class=\"docClass\">CKEDITOR.dom.element</a><div class='sub-desc'>\n</div></li></ul><h3 class='pa'>Returns</h3><ul><li><span class='pre'>Object</span><div class='sub-desc'>\n</div></li></ul></div></div></div></div></div></div></div>","meta":{}});