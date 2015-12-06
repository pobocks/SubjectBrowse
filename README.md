Subject Browse (plugin for Omeka)
=================================

[Subject Browse] is a plugin for [Omeka] that allows to serve an alphabetized
page of links to searches for all subjects of all items of an Omeka instance,
or an expandable hierarchical list of all subjects. These pages can be displayed
in any page via a helper.

It is available for Omeka 1.5 ([Subject Browse (1.x)]) and for Omeka 2 ([Subject Browse (2.x)]).


Installation
------------

Uncompress files and rename plugin folder "SubjectBrowse".

Then install it like any other Omeka plugin and follow the config instructions.


Usage
-----

The plugin adds secondary links in the secondary navigation bar:
* "Browse by Subject" (http://www.example.com/subjects/list).
* "Hierarchy of Subjects" (http://www.example.com/subjects/tree).

For the list view, the subjects are the standard "Dublin Core Subject".

For the tree view, the subjects are set in the config form with the hierarchical
list of subjects, formatted like:
```
Europe
- France
- Germany
- United Kingdom
-- England
-- Scotland
-- Wales
Asia
- Japan
```

So, the format is the config page for the tree view is:
- One subjet by line.
- Each subject is preceded by zero, one or more "-" to indicate the hierarchy
level.
- Separate the "-" and the subject with a space.
- A subject cannot begin with a "-" or a space.
- Empty lines are not considered.

These contents can be displayed on any page via the helper `subjectBrowse()`:

```
echo $this->subjectBrowse($subjects, array(
    'mode' => 'list',
    'skiplinks' => true,
    'headings' => true,
    'strip' => true,
    'raw' => false,
));
```

For tree view:
```
echo $this->subjectBrowse($subjects, array(
    'mode' => 'tree',
    'expanded' => true,
    'strip' => true,
    'raw' => false,
));
```

All arguments are optional and the default ones are set in the config page, but
they can be overridden in the theme. So a simple `echo $this->subjectBrowse();`
is enough.

The helper shortcode "subjects" can be used too, in particular in exhibits and
in simple pages:

```
[subjects]
[subjects mode=list]
[subjects mode=list skiplinks=true headings=true raw=false]
[subjects mode=tree]
[subjects mode=tree expanded=true raw=false]
```

Arguments that are set are not set use the default values.

The argument "subjects" allows to display only a delimited list of subjects.


Warning
-------

Use it at your own risk.

It's always recommended to backup your files and database regularly so you can
roll back if needed.


Troubleshooting
---------------

See online issues on the [plugin issues (2.x)] page on GitHub (or [plugin issues (1.x)]).


License
-------

This plugin is published under [GNU/GPL].

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
details.

You should have received a copy of the GNU General Public License along with
this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.


The plugin uses a jQuery library for the tree view, released under the [MIT]
licence.


Contact
-------

Current maintainers:

* William Mayo (mail: <mayow@simmons.edu>, see [pobocks] on GitHub)
* Daniel Berthereau (see [Daniel-KM] on GitHub, release [Subject Browse (2.x)])


First version of this plugin has been built by [Wiliam David Mayo]. Upgrade and
improvements has been made for an anonymous project.


Copyright
---------

* Copyright William Mayo, 2011
* Copyright Daniel Berthereau, 2014-2015
* Copyright Philip Collins, 2013 ([jQuery tree view])


[Omeka]: https://omeka.org
[Subject Browse]: https://github.com/pobocks/SubjectBrowse
[Subject Browse (1.x)]: https://github.com/pobocks/SubjectBrowse
[Subject Browse (2.x)]: https://github.com/Daniel-KM/SubjectBrowse
[plugin issues (1.x)]: https://github.com/pobocks/SubjectBrowse/issues
[plugin issues (2.x)]: https://github.com/Daniel-KM/SubjectBrowse/issues
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html "GNU/GPL v3"
[MIT]: http://http://opensource.org/licenses/MIT
[pobocks]: https://github.com/pobocks
[Wiliam David Mayo]: https://github.com/pobocks
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"
[jQuery tree view]: https://github.com/collinsp/jquery-simplefolders
