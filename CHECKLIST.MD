# Launch Checklist

## General

### Supplemental Show Pages / Show Menu

https://github.com/tutv/tutv/issues/6

New pages can be added to menu by going to Appearance > Menu, then clicking Screen Options and checking the appropriate box to display the metabox.

### Regenerate Thumbnails

We have two new sizes.

	add_image_size( 'thumbnail', 180, 130, true ); // Thumbnail size
	add_image_size( 'thumbnail-square', 200, 200, true ); // square thumbnail size for show page headers

Technically, the `'thumbnail'` size is not new, but its dimensions have increased.

The `'thumbnail-square'` size is only used in the show header.