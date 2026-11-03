DataTables Plugin for DokuWiki
==============================

Add DataTables support to your DokuWiki

## Plain tables and presentation

Keep the same wrapper for a document table without interactive controls:

```text
<datatables plain="true" striped="false" border="true" header-background="#eef2f5">

^ SWP ref | LC-2 |
^ Version | @APPROVE_VERSION@ |
^ Prepared by | @APPROVE_USER@ |

</datatables>
```

- `plain="true"` leaves the ordinary wiki table intact: no searching, sorting,
  paging, export buttons or fixed headers. Merged cells are supported.
- `scroll-x="true"` (the default) lets plain tables shrink and wrap text to fit,
  then scroll horizontally if their contents still need more room. This works
  without JavaScript. `scroll-x="false"` disables the wrapper's scrolling.
- `striped="true"` / `striped="false"` enables or disables alternating row colours.
- `border="true"` enables the usual light grey borders; `border="false"` removes them.
  Use a colour name or three-/six-digit hex value for custom borders, for example
  `border="black"` or `border="#94a3b8"`.
- `header-background="#cbdcf0"` colours all heading cells (`^`), including
  labels in the left column. Accepts three- or six-digit hexadecimal colours.

Omitted options retain the template's existing appearance and normal DataTables
behaviour. Styling options also work on interactive tables.

All documentation for this plugin can be found at
http://www.dokuwiki.org/plugin:datatables

If you install this plugin manually, make sure it is installed in
lib/plugins/datatables/ - if the folder is called different it
will not work!

Please refer to http://www.dokuwiki.org/plugins for additional info
on how to install plugins in DokuWiki.

----
Copyright (C) Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; version 2 of the License

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

See the COPYING file in your DokuWiki folder for details
