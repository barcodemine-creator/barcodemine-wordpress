=== UiCore Elements - Free Elementor widgets and templates  ===
Contributors: uicore
Tags: elementor, widgets, page builder, ui elements
Requires at least: 4.6
Requires PHP: 7.4
Tested up to: 6.7.1
Stable tag: 1.0.16
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enhance your website with UiCore Elements – a free plugin offering diverse widgets for effortless design enrichment.

== Description ==

<strong>Unlock Creative Possibilities with UiCore Elements</strong>

[Explore All Widgets](https://elements.uicore.co/)


UiCore Elements is a versatile plugin designed to extend the capabilities of Elementor Page Builder. Elevate your website's design with an array of widgets, each crafted to bring innovation and interactivity to your pages.

- **Diverse Range of Widgets:** From dynamic sliders to interactive forms, UiCore Elements provides a collection of widgets that seamlessly integrate with Elementor, empowering you to create captivating layouts.

- **Intuitive Widget Customization:** Effortlessly customize each widget to match your design preferences. Adjust colors, styles, and content with ease, ensuring your website reflects your unique brand identity.

- **Seamless Elementor Integration:** UiCore Elements seamlessly integrates with Elementor, allowing you to enhance your page-building experience without any compatibility issues.

== Installation ==

1. Install UiCore Elements either via the WordPress plugin directory or by uploading the files to your server at wp-content/plugins.

== Frequently Asked Questions ==

= Can I use UiCore Elements with any theme? =

Yes, UiCore Elements is designed to work seamlessly with any WordPress theme that supports Elementor Page Builder.

= Is UiCore Elements compatible with the latest version of Elementor? =

Absolutely! UiCore Elements is tested up to latest version of Elementor to ensure compatibility and a smooth user experience.

= Are there any performance concerns with UiCore Elements? =

No need to worry. UiCore Elements is optimized for performance and ensures a lightweight footprint for your website.

== Screenshots ==

1. /assets/screenshots/1.png

== Changelog ==
= 1.0.16 = 
* FIX - PHP 8.x warnings
* FIX - Minor small bugs fixes

= 1.0.15 =
* NEW - Added 'Pause on Hover' option to Carousel widgets
* NEW - Added `tag` to meta options for Advanced Post Widgets
* UPD - Major assets update. Improved file organization; decreased some widget's required assets; added extra enqueue conditions and more
* UPD - Adjusted compatibility issues and some other updates required by the Elementor 3.26 release
* FIX - Fixed carousel undefined key error log
* FIX - Fixed Custom Carousel dots navigation bug where dragging wouldn't update the active dot
* FIX - Fixed Advanced Post Grid and Advanced Product Grid bug where you couldn't preview posts/products with 'current' query
* FIX - Fixed incorrect php tag on Gallery widgets.

= 1.0.14 =
* NEW - Added three new widgets: Gallery Grid, Gallery Carousel and Gallery Slider
* FIX - Fixed Carousel dots navigation position bug, where dots started above widgets by default
* FIX - Logged-in issues with latest Elementor Pro

= 1.0.13 =
* FIX - Fixed APG bug where Portfolio posts worked only by setting a taxonomy
* FIX - Fixed Contact Form bug where forms without required checkboxes failed on submitting

= 1.0.12 =
* NEW - Added 'required' option to Checkbox field on Contact Form
* FIX - Fixed Icon List gap bug that only applied horizontal spacing
* FIX - Fixed Advanced Carousel Grid bug where you couldn't edit pages that used the widget
* FIX - Fixed APG bug where meta icons weren't inheriting the font-size if SVG experiment was enabled

= 1.0.11 =
* NEW - Added new widget: Advanced Product Grid
* NEW - Added new widget: Custom Table (with Nested capabilities)
* NEW - Added suffix/prefix style options and motion animation transition options to Counter widget
* UPD - Improved the Advanced Post Grid widget core
* FIX - Removed unnecessary bottom spacing from multiple widgets
* FIX - Fixed Advanced Product Grid and Carousel widgets bug where ‘current’ query ignored taxonomy on archive pages
* FIX - Fixed Post Grid width bug on Masonry layout
* FIX - Fixed border-radius bug on Custom Slider
* FIX - Fixed Contact Form and Newsletter widgets bug that prevented the form from being sent if the widget was added to footer/header templates
* FIX - Fixed Icon Box icon animation mouse-out transition
* FIX - Fixed Contact Form compatibility with Elementor PRO

= 1.0.10 =
* FIX - Disabled autocomplete on honeypot fields on form Widgets, preventing browsers from adding data and blocking form submission
* FIX - Fixed Carousel/Slide widgets mobile overflow bug
* FIX - Fixed Icon List Widget bug where Icons sometimes had different sizes
* UPD - Changed Icon Box Widget content padding to not impact images if set as full width
* UPD - Added 'px' option to Testimonial Widget's avatar size, allowing small avatars to fit better
* UPD - Turned Icon List Widget 'Vertical Alignment Offset' option responsive
* UPD - Turned Form Widgets field padding option responsive

= 1.0.9 =
* FIX - Fixed Accordion bug where links in content weren't clickable
* FIX - Fixed Advanced Post widgets bug where readmore button icons weren't printed if Elementor Inline Font Icon feature was enabled
* FIX - Fixed APG bug where Item Limits weren't respected
* FIX - Fixed Motion Blur Counter animation bug caused by Waypoint update
* UPD - Totally removed Waypoint from the plugin, replacing it with InterserctionObserver
* UPD - Added dragging control to Testimonial Slider's Circular Avatar animation
* UPD - Small improvements on instructions texts and code overall

= 1.0.8 =
* FIX - Fixed Grid and Carousel entrance animations

= 1.0.7 =
* NEW - Added new widget: Newsletter
* NEW - Added new widget: Custom Carousel (with Nested capabilities)
* NEW - Added new widget: Custom Slider (with Nested capabilities)
* NEW - Added new animation to Testimonial Slider: Circular Avatar
* NEW - Added Mailchimp Integration to form widgets
* FIX - Fixed Slider Widget's Stacked animation bug
* FIX - Fixed Counter icon color bug with Inline Font Icon experiment
* FIX - Fixed Icon List numbers bug that outputted only odd values
* FIX - Fixed Testimonial Grid masonry bug where desktop columns overcome mobile values
* FIX - Fixed Icon Box bug where title spacing, with inline icon, was also applied to the media
* FIX - Fixed Icon Box background icon color bug
* UPD - Made several improvements to Carousel and Slider widgets
* UPD - Improved Testimonial widget's layout 6 design by setting the media above content on mobile
* UPD - Added hover style options to the Icon Box badge
* UPD - Improved Icon Box readmore options by setting Box Hover and Button Hover separated styles

= 1.0.6 =
* NEW - Added new widget: Tabs (with Nested capabilities)
* NEW - Added WPML String Translation support to all our widgets
* FIX - Fixed Icon Box log errors; global link bug on the editor and readmore button justified alignment design
* FIX - Fixed Accordion accessibility errors pointed on Lighthouse and similar performance tools
* FIX - Fixed Meta Component bug that made some widget's hover actions get triggered at hidden menus
* FIX - Fixed Slider Animation bug that blocked some item's animation from being triggered
* FIX - Contact Form Submission API dinamic url
* UPD - Added Stroke Color & Gradient text options to Highlighted Text widget
* UPD - Small Icon Box improvements
* UPD - Optimized Accordion widget script
* UPD - Optimized Accordion widget accessibility by adding keyboard navigation

= 1.0.5 =
* NEW - Added new widget: Contact Form
* NEW - Added Vertical Alignment option on all Testimonial widgets
* NEW - Added Multiple animations to Carousel and Slider Widgets
* FIX - Fixed issues with Accordion's active items and width
* FIX - Fixed button alignment on both Advanced Post Grid & Advanced Post Carousel
* FIX - Fixed transition problems on Icon Box
* FIX - The content dummy content issue
* UPD - Several improvements on assets and components
* UPD - Optimized Carousel widgets markup

= 1.0.4 =
* NEW - Added new widget: Advanced Post Carousel
* FIX - Fixed issue with Highlighted text using div tag
* FIX - Fixed Split layout skin on Advanced Post Grid
* FIX - Fixed warning on Post Grid
* FIX - Fixed issue with Advanced Post Grid showing incorrect post date
* FIX - Added correct ARIA attributes to Accordion

= 1.0.3 =
* FIX – Highlighted Text stoke and content bugs
* UPD – Small improvement on the Motion Blur animation CSS for Counter widget

= 1.0.2 =
* FIX - Better widgets markup filter

= 1.0.1 =
* FIX - Highlighted text SVG issues
* FIX - Advanced Post widget filter display

= 1.0.0 =
* Initial release