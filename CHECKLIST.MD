# Launch Checklist

## General

### Schedules / Schedule Block

All occurances of the `$timestamp` variable must be checked and fixed. This was necessary for working on the staging site, but will break the live site.

### Social Media Icons

The "Contact" icon is weird... maybe the little dongle should go off to the left?

### Search Form

Make sure search is searching in the right place. See `searchform.php`.

`searchform.php` uses `<?php echo home_url('/') ?>` but on the staging site this is incorrect because the site is in the /tutv/ directory. Because the live site's `home_url()` is not in a subdirectory, this should work fine, but do make sure to verify when live.

### Supplemental Show Pages / Show Menu

https://github.com/tutv/tutv/issues/6

New pages can be added to menu by going to Appearance > Menu, then clicking Screen Options and checking the appropriate box to display the metabox.

### Page Header Submenus

How should submenus be handled? Maybe the page name should stay as the parent on all subpages and the subpage (current page) should still stay in the submenu and merely have its formatting change (as currently styled with selector `li.current_page_item`). Yes.

I haven't touched Sam's function that generates these because trying to approximate Bella's mockup would mean removing some of his code.

### Flexslider Nav Gone in Firefox

Not in Chrome.

### Regenerate Thumbnails

We have two new sizes.

	add_image_size( 'thumbnail', 180, 130, true ); // Thumbnail size
	add_image_size( 'thumbnail-square', 200, 200, true ); // square thumbnail size for show page headers

Technically, the `'thumbnail'` size is not new, but its dimensions have increased. In places where only 140px is necessary, limit in stylesheet using `width: 140px;` or `max-width: 100%;` on an image with a 140px container. __Check to make sure all thumbnails are fitting properly.__

The `'thumbnail-square'` size is only used in the show header.

### Excerpts

Still a problem. How did we resolve it before? Excluding elements?

For Temple Update, suggest moving meta info below Top Stories.

### Watch Live

Ensemble embed must be made proportionately smaller in post editor to fit within container.

### "Temple Update Blog" Button

Is this an accurate title? Methinks not.


## Remaining Tasks

- [x] front slider
- [x] episode pages
- [x] clips pages
- [] watch live page
	- [] schedule block
- [/] submissions page
	- [/] menu
- [x] contact page
- [] blog
	- [/] single post
	- [] archive
- [] regenerate thumbnails test


# The Future

### Double Scrollbars when Logged In

WTF?!

