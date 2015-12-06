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

For the tree view, you need to fill the config form with the hierarchical list
of subjects, formatted like:
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

So, format is:
- One subjet by line.
- Each subject is preceded by zero, one or more "-" to indicate the hierarchy
level.
- Separate the "-" and the subject with a space.
- A subject cannot begin with a "-" or a space.
- Empty lines are not considered.

These contents can be displayed on any page via the helpers `subjectBrowseList()`
and `subjectBrowseTree()`:

For list view:
```
echo $this->subjectBrowseList(null, array(
    'linked' => true,
    'skiplinks_top' => true,
    'headers' => true,
    'skiplinks_bottom' => true,
));
```

For tree view:
```
echo $this->subjectBrowseTree(null, array(
    'linked' => true,
    'expanded' => true,
));
```


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
