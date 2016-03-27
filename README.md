Reference (plugin for Omeka)
=================================

[Reference] is a plugin for [Omeka] that allows to serve an alphabetized index
of links to searches for all metadata of all items of an Omeka instance, or an
expandable hierarchical list of specified subjects. These lists can be displayed
in any page via a helper or a shortcode.

This plugin is an upgrade of [Subject Browse (1.x)], with some ideas from
[Metadata Browser] and [Category Browse].


Installation
------------

Uncompress files and rename plugin folder "Reference".

Then install it like any other Omeka plugin and follow the config instructions.


Usage
-----

The plugin adds secondary links in the secondary navigation bar:
* "Browse by Reference" (http://www.example.com/references).
* "Hierarchy of Subjects" (http://www.example.com/subjects/tree).

For the list view, the references are defined in the  config page.

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

These contents can be displayed on any page via the helper `reference()`:

```
echo $this->reference($references, array(
    'mode' => 'list',
    'slug' => 'subject'
    'skiplinks' => true,
    'headings' => true,
    'strip' => true,
    'raw' => false,
));
```

For tree view:
```
echo $this->reference($subjects, array(
    'mode' => 'tree',
    'expanded' => true,
    'strip' => true,
    'raw' => false,
));
```

All arguments are optional and the default ones are set in the config page, but
they can be overridden in the theme. So a simple `echo $this->reference();`
is enough. For list, the default is the "Dublin Core : Subject".

The shortcodes "reference" and "subjects" can be used too, in particular in
exhibits and in simple pages:

```
[reference]
[reference slug=date skiplinks=true headings=true raw=false]
[subjects]
[subjects expanded=true raw=false]
```

Arguments that are not set use the default values.

The arguments "references" or "subjects" allow to display only a delimited list
references or subjects.


Warning
-------

Use it at your own risk.

It's always recommended to backup your files and database regularly so you can
roll back if needed.


Troubleshooting
---------------

See online issues on the [plugin issues] page on GitHub.


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
* Daniel Berthereau (see [Daniel-KM] on GitHub, release [Reference])

First version of this plugin has been built by [Wiliam David Mayo]. Upgrade and
improvements has been made for [Jane Addams Digital Edition].


Copyright
---------

* Copyright William Mayo, 2011
* Copyright Daniel Berthereau, 2014-2015
* Copyright Philip Collins, 2013 ([jQuery tree view])


[Omeka]: https://omeka.org
[Reference]: https://github.com/pobocks/Reference
[Subject Browse (1.x)]: https://github.com/pobocks/SubjectBrowse
[Metadata Browser]: https://github.com/Daniel-KM/MetadataBrowser
[Category Browse]: https://github.com/Daniel-KM/CategoryBrowse
[plugin issues]: https://github.com/Daniel-KM/Reference/issues
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html "GNU/GPL v3"
[MIT]: http://http://opensource.org/licenses/MIT
[pobocks]: https://github.com/pobocks
[Wiliam David Mayo]: https://github.com/pobocks
[Jane Addams Digital Edition]: http://digital.janeaddams.ramapo.edu
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"
[jQuery tree view]: https://github.com/collinsp/jquery-simplefolders
