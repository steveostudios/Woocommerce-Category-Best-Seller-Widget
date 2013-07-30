=== Woocommerce Category Best Seller Widget ===

Contributors: steveostudios
Tags: widget, woocommerce, category, best seller
Requires at least: 3.0.1
Tested up to: 3.5.2
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Widget that displays a specified number of best sellers in the same category.

== Description ==

This widget only works on a Woocommerce single product page. When it is placed there, it will parse the `Category location` (set in the widget's settings) for the any subcategories of the parent category. It will then list the best sellers, by subcategory, in widget form.

Here is an example case: We have mutliple authors that each have tons of products. Product categories are as follows:
- Books
  - fiction
  - nonfiction
- Authors
 - Author 1
 - Author 2
 - Author 3

In our case, our widget's `Category location` is `Authors` (the category). When you go to a product filed in `Author 1`, in the sidebar, all of Author 1's other products are listed in order of best selling.

If there is more than one category that it falls under, it will list them all. It ignores the current product. It also hide itself if this is the only product in that subcategory.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the `Woocommerce Category Best Seller` widget to a single product sidebar.
4. Change the `Category location` to the parent category that you wish to display.

== Frequently Asked Questions ==

= Why do I have to have a `Category location`? =

As of right now, I needed a way to exclude other parent categories. This seemed like the best method so far.

= How do I get this to only show up on a single product page? =

Here is a great free plugin that does it all for you: [WooSidebars](http://www.woothemes.com/woosidebars)

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 1.0 =
* Option for a title.
* Option for a parent category.
* Option for maximum best sellers to display.
* Hides if it's the only item in that category.
* Ignores current product.
* Multiple lists if the product is in multiple categories.