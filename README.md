Really Bad Static Site Generator
================================
A really bad static site generator.

Description
-----------
I was doing some testing for a client's headless WordPress project, and realized I was 75% of the way to a static site generator. So I did the extra 25% for kicks.

The file `render.php` will create an index with links to all your posts. It also creates `html` files using the predefined slugs. The script should grab all your posts in reverse chronological order.

Usage
-----
First off, don't use it.

Secondly:
* you will need a WordPress server somewhere populated with all your content
* upload to a server with PHP
* copy `sample.config.php` to `config.php`, update with the URL of your WordPress install
* changes to the rendered html can be made in `_header.php` and `_footer.php`. Make sure the header and footer together make valid  `HTML`!
* all `CSS` is in `_header.php`; change that if you want things to be pretty
* visit `http://installlocation.com/render.php` to perform the rendering. Hit that up every time you post a new post  

To Do
-----
* ~~create a config~~
* import images
* ~~duplicate the source WordPress site's path structure~~ (I have added the ability to put posts in subdirectories by `year` or `year/month`)
* find a way to eliminate that original API call to get the `X-WP-Total` value. Maybe do one call for the first 100 posts, _then_ loop if needed?
* have WordPress call `render.php` automatically on publishing?
* create an RSS feed