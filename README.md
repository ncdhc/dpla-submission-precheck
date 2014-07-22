DPLA Submission Pre-Check
=========================

These tools are intended for use by a Service Hub's partners as a means to preview their content after the Hub has normalized it for submission to the DPLA.

The preview mirrors, as closely as possible, the actual output of harvested content on the DPLA's website: <http://dp.la>

Included is an OAI proxy viewer. This is useful if the repository itself is closed to the public; as long as interactions from the server where this tool resides is allowed by the server hosting the repository, this will allow outside users to view single OAI records as a means of troubleshooting.


Version
-------

1.0

Technology
----------

The DPLA Submission Pre-Check tool requires the following to function properly:
  - PHP 5 or higher
  - libxml extension (enabled in PHP 5 by default)
  - php_curl extension
  - php_xsl extension

In addition, the following will need to be provided in the included config.php file:
  - provider name
  - provider OAI base URL
  - the metadata prefix of the records harvested by the DPLA
    -  if this is anything other than NCDHC-mapped MODS, new "analysis" and "samplerecord" XSL stylesheets will be required
  - a help contact name
  - a help contact email
  - a help contact phone number

This tool is built with Bootstrap 3.1.1 and Bootstrap Theme, included in this release.

Installation
------------

This tool assumes that the data set specifications in your repository follow a partner-specific naming convention:

```sh
somecode_setname
```

i.e.,

```sh
ncdhc_postcards
```

The partner-specific code in front of the set name (in the above examples, "somecode" or "ncdhc") is required to begin using this tool.

Unzip files into a web-accessible directory or local evironment. Visit:

```sh
http://[path_to_your_directory]/index.php?dataprovider=somecode
```

License
-------

NCDHC DPLA Submission Pre-Check 1.0

Copyright (C) 2014 North Carolina Digital Heritage Center <http://www.digitalnc.org/about>.

This program is free software: you can redistribute it and/or modify
it under the terms of the **GNU General Public License** as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses>.


Attribution
-----------

**jQuery** <https://jquery.org/>

    Copyright 2005, 2014 jQuery Foundation and other contributors,
    https://jquery.org/
    
    This software consists of voluntary contributions made by many
    individuals. For exact contribution history, see the revision history
    available at https://github.com/jquery/jquery
    
    The following license applies to all parts of this software except as
    documented below:
    
    ====
    
    Permission is hereby granted, free of charge, to any person obtaining
    a copy of this software and associated documentation files (the
    "Software"), to deal in the Software without restriction, including
    without limitation the rights to use, copy, modify, merge, publish,
    distribute, sublicense, and/or sell copies of the Software, and to
    permit persons to whom the Software is furnished to do so, subject to
    the following conditions:
    
    The above copyright notice and this permission notice shall be
    included in all copies or substantial portions of the Software.
    
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
    MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
    LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
    OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
    WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
    
    ====
    
    All files located in the node_modules and external directories are
    externally maintained libraries used by this software which have their
    own licenses; we recommend you read them, as their terms may differ from
    the terms above.


**Twitter Bootstrap** <http://getbootstrap.com>

    The MIT License (MIT)
    
    Copyright (c) 2011-2014 Twitter, Inc
    
    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:
    
    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.
    
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.

**Glyphicons** <http://glyphicons.com>
