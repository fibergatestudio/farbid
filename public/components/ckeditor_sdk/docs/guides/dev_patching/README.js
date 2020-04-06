Ext.data.JsonP.dev_patching({"guide":"<!--\nCopyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.\nFor licensing, see LICENSE.md.\n-->\n\n\n<h1 id='dev_patching-section-patching-old-ckeditor-versions'>Patching Old CKEditor Versions</h1>\n<div class='toc'>\n<p><strong>Contents</strong></p>\n<ol>\n<li><a href='#!/guide/dev_patching-section-requirements'>Requirements</a></li>\n<li>\n<a href='#!/guide/dev_patching-section-source-version-vs-release-version'>Source Version vs Release Version</a><ol>\n<li>\n<a href='#!/guide/dev_patching-section-release-version'>Release Version</a></li>\n<li>\n<a href='#!/guide/dev_patching-section-source-version'>Source Version</a></li>\n<li>\n<a href='#!/guide/dev_patching-section-how-to-check-which-distribution-i-use%3F'>How to Check Which Distribution I Use?</a></li>\n</ol>\n<li>\n<a href='#!/guide/dev_patching-section-getting-the-source-code-of-ckeditor'>Getting the Source Code of CKEditor</a><ol>\n<li>\n<a href='#!/guide/dev_patching-section-ckeditor-dev-vs-ckeditor-presets'>ckeditor-dev vs ckeditor-presets</a></li>\n<li>\n<a href='#!/guide/dev_patching-section-downloading-ckeditor-dev'>Downloading ckeditor-dev</a><ol>\n<li>\n<a href='#!/guide/dev_patching-section-which-version-to-download%3F'>Which Version to Download?</a></li>\n<li>\n<a href='#!/guide/dev_patching-section-downloading-with-git-clone'>Downloading with git clone</a></li>\n<li>\n<a href='#!/guide/dev_patching-section-downloading-without-git'>Downloading without Git</a></li>\n</ol>\n<li>\n</li>\n</ol>\n<li>\n<a href='#!/guide/dev_patching-section-patching-process'>Patching Process</a><ol>\n<li>\n<a href='#!/guide/dev_patching-section-select-the-changes-you-want-to-port'>Select the Changes You Want to Port</a><ol>\n<li>\n<a href='#!/guide/dev_patching-section-example'>Example</a></li>\n</ol>\n<li>\n<a href='#!/guide/dev_patching-section-applying-the-changes'>Applying the Changes</a><ol>\n<li>\n<a href='#!/guide/dev_patching-section-without-git'>Without Git</a></li>\n<li>\n<a href='#!/guide/dev_patching-section-with-git'>With Git</a></li>\n</ol>\n<li>\n</li>\n</ol>\n<li>\n<a href='#!/guide/dev_patching-section-building-ckeditor'>Building CKEditor</a><ol>\n<li>\n<a href='#!/guide/dev_patching-section-prepare-build-config.js'>Prepare build-config.js</a></li>\n<li>\n<a href='#!/guide/dev_patching-section-add-missing-plugins-to-the-plugins-folder'>Add Missing Plugins to the plugins Folder</a><ol>\n<li>\n<a href='#!/guide/dev_patching-section-your-custom-plugins'>Your Custom Plugins</a></li>\n<li>\n<a href='#!/guide/dev_patching-section-spell-checker-plugins'>Spell Checker Plugins</a></li>\n<li>\n<a href='#!/guide/dev_patching-section-third-party-plugins-from-the-addons-repository'>Third-Party Plugins from the Addons Repository</a></li>\n</ol>\n<li>\n<a href='#!/guide/dev_patching-section-run-ckbuilder'>Run CKBuilder</a></li>\n</ol>\n<li>\n<a href='#!/guide/dev_patching-section-using-automated-tests-to-check-the-patched-version'>Using Automated Tests to Check the Patched Version</a><ol>\n<li>\n<a href='#!/guide/dev_patching-section-testing-ckeditor-4.4.4-%28example%29'>Testing CKEditor 4.4.4 (example)</a></li>\n</ol>\n<li>\n<a href='#!/guide/dev_patching-section-further-reading'>Further Reading</a></li></ol>\n</div>\n\n<p>It may happen that upgrading your project to use the latest CKEditor version is not an option, although the situation requires it, for example if your application is running on a production environment and you are not allowed to perform major upgrades without prior testing of the entire application by the QA team.</p>\n\n<p class=\"tip\">\n    Please note that this article describes a method that is unrecommended and requires deep understanding of the code you are porting as well as the build process. It also comes with no guarantee that it will work in all scenarios. <a href=\"#!/guide/dev_upgrade\">Full upgrade</a> is always a recommended soultion.\n</p>\n\n\n<p>It is possible to keep using the old version of CKEditor with selected patches applied, although keep in mind that you should be really careful when doing it.</p>\n\n<p>Note that depending on the complexity of changes made in CKEditor, <strong>porting selected features might be hardly possible</strong>. For example, when using CKEditor 4.0.3 and trying to port a relatively simple change added in CKEditor 4.3.2, it may turn out to be impossible because that change is using API introduced in CKEditor 4.2.</p>\n\n<h2 id='dev_patching-section-requirements'>Requirements</h2>\n\n<p>In order to patch CKEditor and build a release version, the following required components must be installed:</p>\n\n<ul>\n<li>Java (CKBuilder is a Java application)</li>\n<li>Bash (Unix systems) or \"Git Bash\" on Windows (provided by <a href=\"http://msysgit.github.io/\">msysGit</a>)</li>\n<li>Git (optional)</li>\n<li>Node.js (optional)</li>\n</ul>\n\n\n<h2 id='dev_patching-section-source-version-vs-release-version'>Source Version vs Release Version</h2>\n\n<p>It is unlikely that you can apply a patch to the package that you already run on production, because most of the time your production environment runs the release version of CKEditor.</p>\n\n<h3 id='dev_patching-section-release-version'>Release Version</h3>\n\n<p>The release version is a CKEditor package that was processed by <a href=\"#!/guide/dev_build-section-about-ckbuilder-%28command-line%29\">CKBuilder</a> in order to reduce the number of files and minify the resulting code. The release version is offered for download on <a href=\"http://ckeditor.com/download\">CKEditor download page</a> and also by the <a href=\"http://ckeditor.com/builder\">CKEditor online builder</a>.</p>\n\n<h3 id='dev_patching-section-source-version'>Source Version</h3>\n\n<p>Without the build process, the CKEditor \"full\" package would require over 250 files (HTTP requests) to run due to having to load separate plugin files, language files and icons. The \"source\" version of CKEditor is a version that you can download from the Git repository; it consists of hundreds of files. This is the version on which patches can be applied and as mentioned earlier, it is very unlikely that you are using it on a production environment.</p>\n\n<h3 id='dev_patching-section-how-to-check-which-distribution-i-use%3F'>How to Check Which Distribution I Use?</h3>\n\n<p>Check the size of the <code>ckeditor.js</code> file located in the <code>ckeditor</code> folder that is installed on your website. If the size of that file is larger than 50KB, you are using the release version.</p>\n\n<h2 id='dev_patching-section-getting-the-source-code-of-ckeditor'>Getting the Source Code of CKEditor</h2>\n\n<p>In order to apply patches to CKEditor and then build the release version, you need the source version. The source version of CKEditor is stored on GitHub.</p>\n\n<h3 id='dev_patching-section-ckeditor-dev-vs-ckeditor-presets'>ckeditor-dev vs ckeditor-presets</h3>\n\n<p>There are two repositories where CKEditor source files are kept: <a href=\"https://github.com/ckeditor/ckeditor-dev\"><code>ckeditor-dev</code></a> and <a href=\"https://github.com/ckeditor/ckeditor-presets\"><code>ckeditor-presets</code></a>.</p>\n\n<p>The <code>ckeditor-presets</code> repository is used by the CKEditor team to build the Basic/Standard/Full distributions. It uses <code>ckeditor-dev</code> as a dependency and scripts included there further automate the build process:</p>\n\n<ul>\n<li>It has information about which plugins should be included in which preset.</li>\n<li>It loads spell checker plugins (<code>scayt</code> and <code>wsc</code>) from separate repositories where they are developed, if they are to be included in a release.</li>\n<li>It sets the proper configuration file in the release package depending on the created preset.</li>\n</ul>\n\n\n<p>Although <code>ckeditor-presets</code> saves time in the long term, to reduce the complexity of this documentation article we recommend cloning the <code>ckeditor-dev</code> repository.</p>\n\n<h3 id='dev_patching-section-downloading-ckeditor-dev'>Downloading ckeditor-dev</h3>\n\n<p>The following section descibes how to download the source version of CKEditor from its GitHub repository.</p>\n\n<h4 id='dev_patching-section-which-version-to-download%3F'>Which Version to Download?</h4>\n\n<p>Before downloading files you need to know which version and revision you are currently using. The revision can be checked by opening the <code>ckeditor.js</code> file and searching for the first occurrence of \"revision\". You should see something like this in the middle of <code>ckeditor.js</code>:</p>\n\n<pre><code>{timestamp:\"E7KD\",version:\"4.4.4\",revision:\"1ba5105\"\n</code></pre>\n\n<p><img src=\"guides/dev_patching/patching_01.png\" width=\"552\" height=\"187\" alt=\"Finding the revision number\"></p>\n\n<p>In this case the version of CKEditor is <em>4.4.4</em> and the revision is <em>1ba5105</em>.</p>\n\n<h4 id='dev_patching-section-downloading-with-git-clone'>Downloading with git clone</h4>\n\n<p>Use the following steps to download CKEditor with a command line tool.</p>\n\n<ol>\n<li><code>git clone https://github.com/ckeditor/ckeditor-dev.git</code></li>\n<li><code>cd ckeditor-dev</code></li>\n<li><code>git checkout &lt;revision&gt;</code></li>\n</ol>\n\n\n<p>Where <code>&lt;revision&gt;</code> has to be set to exactly the same revision that you are using. For the example above that would be:</p>\n\n<pre><code>git checkout 1ba5105\n</code></pre>\n\n<h4 id='dev_patching-section-downloading-without-git'>Downloading without Git</h4>\n\n<p>Use the following steps to download CKEditor directly from the browser.</p>\n\n<ol>\n<li><p>Open <code>https://github.com/ckeditor/ckeditor-dev/tree/&lt;revision&gt;</code> in your browser. For the example above the proper URL would be: <a href=\"https://github.com/ckeditor/ckeditor-dev/tree/1ba5105\">https://github.com/ckeditor/ckeditor-dev/tree/1ba5105</a>.</p></li>\n<li><p>Press the \"Download ZIP\" button.</p>\n\n<p> <img src=\"guides/dev_patching/patching_download_zip.png\" width=\"814\" height=\"448\" alt=\"Downloading a selected revision from GitHub\"></p></li>\n<li><p>Unzip the file and rename the top-level folder into <code>ckeditor-dev</code>.</p></li>\n</ol>\n\n\n<h2 id='dev_patching-section-patching-process'>Patching Process</h2>\n\n<p>CKEditor source code is stored in the Git repository. The development takes place in the Git repository hosted on GitHub. Explaining how Git works is out of the scope of this document. All further instructions will cover one selected scenario based on which it should be much easier to understand the entire procedure.</p>\n\n<h3 id='dev_patching-section-select-the-changes-you-want-to-port'>Select the Changes You Want to Port</h3>\n\n<p>If you are reading this document, most probably you already know which feature you want to port. Whenever you find a change that you need to port it is recommended to find in which ticket on the <a href=\"http://dev.ckeditor.com/\">Development site</a> the change has been tracked.</p>\n\n<h4 id='dev_patching-section-example'>Example</h4>\n\n<p>Suppose you are interested in porting a patch for the following problem:</p>\n\n<pre><code>Remove Format button did not remove the &lt;cite&gt; element in versions prior to 4.4.5.\n</code></pre>\n\n<p>By looking into the <a href=\"http://ckeditor.com/whatsnew\">changelog</a> you find a link to <a href=\"http://dev.ckeditor.com/ticket/12311\">ticket #12311</a>. The ticket not only explains what the problem was and how to reproduce it, but at the end it also contains a link to a changeset where the code fix was introduced: <a href=\"http://github.com/ckeditor/ckeditor-dev/commit/b373ace\">http://github.com/ckeditor/ckeditor-dev/commit/b373ace</a></p>\n\n<p>The hash of the changeset is <code>b373ace</code>.</p>\n\n<h3 id='dev_patching-section-applying-the-changes'>Applying the Changes</h3>\n\n<h4 id='dev_patching-section-without-git'>Without Git</h4>\n\n<p>If for any reason you cannot use Git, you have to apply the changes manually. It is highly unrecommended though. Be careful when changing the files manually.</p>\n\n<ol>\n<li>View the changes on GitHub using the link found in the ticket: <a href=\"http://github.com/ckeditor/ckeditor-dev/commit/b373ace\">http://github.com/ckeditor/ckeditor-dev/commit/b373ace</a></li>\n<li>Search for relevant files in your <code>ckeditor-dev</code> folder. Open them in your editor.</li>\n<li>Add/modify/remove code as shown in the diff.</li>\n</ol>\n\n\n<p>Note that the <strong>line numbers</strong> displayed on GitHub and in your editor <strong>might be different</strong> due to different versions on which you work and to which the change was applied. In worst case it may turn out that the lines of code that have been changed do not even exist. In such case a <a href=\"#!/guide/dev_upgrade\">full upgrade</a> and testing will require less effort than continuing with patching.</p>\n\n<h4 id='dev_patching-section-with-git'>With Git</h4>\n\n<p>As mentioned earlier, this is not a Git manual, so we will continue the instructions for the previous example without more in-depth Git explanations.</p>\n\n<p>The hash of the changeset is <code>b373ace</code>, so open your command line tool and go through the steps below:</p>\n\n<ol>\n<li><code>cd ckeditor-dev</code></li>\n<li><p><code>git show b373ace</code></p>\n\n<p> <img src=\"guides/dev_patching/patching_02.png\" width=\"434\" height=\"100\" alt=\"Merge commit shown\"></p>\n\n<p> You can see that this is a merge commit. This means that in order to create a patch from it you need to call:</p>\n\n<pre><code class=\"`\"> git diff 558f516...5148dff &gt; 12311.patch\n</code></pre></li>\n<li><p>You can now apply the patch\n <code>\n git apply -v 12311.patch\n</code></p></li>\n</ol>\n\n\n<p><strong>Resolving Conflicts</strong></p>\n\n<p>The <code>git apply</code> command will fail in the example above due to the <code>CHANGES.md</code> file.</p>\n\n<p>Fortunately, since you know that this file is not required by CKEditor, you can apply the patch ignoring that single file:</p>\n\n<pre><code>git apply -v 12311.patch --exclude=CHANGES.md\n</code></pre>\n\n<p>Alternatively, when the patch does not apply cleanly, you may fall back on the 3-way merge with the <code>--3way</code> flag. Git will then leave the conflict markers in the files in the working tree for the user to resolve:</p>\n\n<pre><code>git apply -v 12311.patch --3way\n</code></pre>\n\n<p><strong>Note:</strong> Check the Git documentation for more information about resolving conflicts.</p>\n\n<h2 id='dev_patching-section-building-ckeditor'>Building CKEditor</h2>\n\n<p>Building CKEditor from source is described in a separate article: <a href=\"#!/guide/dev_build\">Building CKEditor from Source Code</a>.\nPlease read that article just to understand the basic concept before going further.</p>\n\n<h3 id='dev_patching-section-prepare-build-config.js'>Prepare build-config.js</h3>\n\n<p>As explained in the documentation, the build configuration file (<code>dev/builder/build-config.js</code>) defines which plugins will be included in the created build.</p>\n\n<p>In order to be able to create the same build that you used so far, you should take <code>build-config.js</code> from the root folder of the CKEditor distribution that you are still using on production and overwrite <code>dev/builder/build-config.js</code> with correct <code>build-config.js</code>.</p>\n\n<h3 id='dev_patching-section-add-missing-plugins-to-the-plugins-folder'>Add Missing Plugins to the plugins Folder</h3>\n\n<p>It is possible that <code>ckeditor-dev/plugins</code> does not contain all plugins that your build had. Before building you need to verify <code>build-config.js</code> and check whether each plugin listed there exists in the <code>plugins</code> folder.</p>\n\n<h4 id='dev_patching-section-your-custom-plugins'>Your Custom Plugins</h4>\n\n<p>If you used a custom build of CKEditor with your own custom plugins, copy them to the <code>ckeditor-dev/plugins</code> folder.</p>\n\n<h4 id='dev_patching-section-spell-checker-plugins'>Spell Checker Plugins</h4>\n\n<p>If the build that you used had spell checker plugins (<code>scayt</code> or <code>wsc</code>), then you need to copy them as well. Spell checker plugins must be downloaded with the proper revision. The revision of <code>scayt</code> and <code>wsc</code> plugin that was used with release versions of CKEditor can be checked in the <code>ckeditor-presets</code> repository</p>\n\n<ol>\n<li><p>Open <a href=\"https://github.com/ckeditor/ckeditor-presets\">https://github.com/ckeditor/ckeditor-presets</a>.</p></li>\n<li><p>Press the \"branch:\" selection list and then select the Tags tab.</p></li>\n<li><p>Select the tag that matches the version of CKEditor that you are using.</p>\n\n<p> <img src=\"guides/dev_patching/patching_03.png\" width=\"387\" height=\"390\" alt=\"Finding the tag in GitHub\"></p></li>\n<li><p>After selecting the tag (<code>4.4.4</code> in this example), open the <code>plugins</code> folder.</p>\n\n<p> <img src=\"guides/dev_patching/patching_04.png\" width=\"294\" height=\"324\" alt=\"Selecting the tag in GitHub\"></p></li>\n<li><p>In the <code>plugins</code> folder you will find a link to the correct revision of a plugin that was included in CKEditor 4.4.4.</p>\n\n<p> <img src=\"guides/dev_patching/patching_05.png\" width=\"391\" height=\"181\" alt=\"Plugin revision in GitHub\"></p></li>\n<li><p>Click both links to exact revisions of the <code>scayt</code> and <code>wsc</code> plugins. You will be redirected to their project pages, where the \"Download ZIP\" option on the right side will return the correct version of each plugin.</p>\n\n<p> <img src=\"guides/dev_patching/patching_06.png\" width=\"799\" height=\"347\" alt=\"Downloading SCAYT and WSC plugins\"></p></li>\n<li><p>Unpack the downloaded plugins, renaming their folders to <code>scayt</code> and <code>wsc</code>, respectively.</p></li>\n</ol>\n\n\n<h4 id='dev_patching-section-third-party-plugins-from-the-addons-repository'>Third-Party Plugins from the Addons Repository</h4>\n\n<p>If you used third-party plugins from the addons repository, make sure you download them again from the <a href=\"http://ckeditor.com/addons/plugins/all\">addons repository</a>, taking the appropriate versions.</p>\n\n<h3 id='dev_patching-section-run-ckbuilder'>Run CKBuilder</h3>\n\n<p>Once all required plugins are available, you are finally ready to run the command line builder to create the release version of CKEditor.</p>\n\n<p>On Unix system open the terminal, on Windows open the \"Git Bash\" window, and type the following:</p>\n\n<ol>\n<li><code>cd dev/builder</code></li>\n<li><code>./build.sh</code></li>\n</ol>\n\n\n<p>Assuming that Java is installed on your computer, the CKEditor release version should be created in less than a minute.</p>\n\n<h2 id='dev_patching-section-using-automated-tests-to-check-the-patched-version'>Using Automated Tests to Check the Patched Version</h2>\n\n<p>Starting from version 4.4.2, the CKEditor project is using <a href=\"https://github.com/benderjs/benderjs\">Bender.js</a> for testing. You can use the automated tests to quickly verify that patching did not break anything. If after applying the patch you notice that some tests started failing although they did not fail on the original version, you should understand what the test is checking and verify the results of the failed test manually. There is a chance that the test fails because the expected result is now different.</p>\n\n<p><strong>Note:</strong> At the moment of writing this documentation, due to constant enhancements, Bender.js is not yet backwards compatible with older versions of CKEditor (tests). It means that in order to test older versions of CKEditor you need to install the exact version of Bender that was required to test that particular release.</p>\n\n<h3 id='dev_patching-section-testing-ckeditor-4.4.4-%28example%29'>Testing CKEditor 4.4.4 (example)</h3>\n\n<p>CKEditor 4.4.4 has been released on August 19th, 2014:<br>\n<a href=\"https://github.com/ckeditor/ckeditor-dev/releases\">https://github.com/ckeditor/ckeditor-dev/releases</a></p>\n\n<p>The latest version of Bender.js available on that day was 0.1.7:<br>\n<a href=\"https://github.com/benderjs/benderjs/releases\">https://github.com/benderjs/benderjs/releases</a></p>\n\n<ol>\n<li><p>Clone the <code>ckeditor-dev</code> repository.</p>\n\n<pre><code class=\"`\"> git clone https://github.com/ckeditor/ckeditor-dev.git\n</code></pre></li>\n<li><p>Checkout the tag that indicates the version you want to work with.</p>\n\n<pre><code class=\"`\"> git checkout 4.4.4\n</code></pre></li>\n<li><p>Uninstall the globally installed Bender.js, just in case you used it already in the past.</p>\n\n<pre><code class=\"`\"> npm uninstall -g benderjs\n</code></pre>\n\n<p> <strong>Note:</strong> You may need administrative rights to do this (e.g. <code>sudo</code>).\n <br></p></li>\n<li><p>Install the correct version of Bender.js for the CKEditor version you want to test.</p>\n\n<pre><code class=\"`\"> npm install -g benderjs@0.1.7\n</code></pre>\n\n<p> <strong>Note:</strong> You may need administrative rights to do this (e.g. <code>sudo</code>).\n <br></p></li>\n<li><p>Install the dependencies that are required to test CKEditor.</p>\n\n<pre><code class=\"`\"> npm install\n</code></pre>\n\n<p> Do not use <code>sudo</code> or the global <code>-g</code> flag here.\n <br></p></li>\n<li><p>Initialize the Bender.js project.</p>\n\n<pre><code class=\"`\"> bender init\n</code></pre></li>\n<li><p>Run the test server.</p>\n\n<pre><code class=\"`\"> bender server run\n</code></pre></li>\n<li><p>Open the URL returned by Bender.js in your browser. You can now run tests in your browser!</p></li>\n</ol>\n\n\n<p><strong>Note:</strong> There might be problems with running Bender.js on CKEditor installations older than 4.4.4 due to broken dependencies. The instruction is valid for version 4.4.4 and above.</p>\n\n<p>For more information about the testing CKEditor, check the <a href=\"#!/guide/dev_tests\">CKEditor Testing Environment (Bender.js)</a> article.</p>\n\n<h2 id='dev_patching-section-further-reading'>Further Reading</h2>\n\n<p>The following resources discuss related issues:</p>\n\n<ul>\n<li>The <a href=\"#!/guide/dev_upgrade\">Upgrading CKEditor</a> article explains how to upgrade your CKEditor 4.x installation to the latest version.</li>\n<li>The <a href=\"#!/guide/dev_source\">Getting the Source Code</a> article explains where you can get and examine CKEditor source code.</li>\n<li>The <a href=\"#!/guide/dev_build\">Getting the Source Code</a> article explains how to build CKEditor from source.</li>\n<li>The <a href=\"#!/guide/dev_tests\">CKEditor Testing Environment (Bender.js)</a> article gives an overview of Bender.js.</li>\n</ul>\n\n","title":"Patching Older Versions","meta_description":"How to patch older versions of CKEditor 4.","meta_keywords":"ckeditor, editor, upgrade, upgrading, update, updating, patch, patching, download, install, installation"});