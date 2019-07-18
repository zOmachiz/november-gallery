
![NovemberGallery Banner](/media/november-gallery-octobercms-banner.jpg)

<div align="right"><a href="https://gitter.im/november-gallery/community?utm_source=badge&amp;utm_medium=badge&amp;utm_campaign=pr-badge&amp;utm_content=badge"><img src="https://badges.gitter.im/november-gallery/community.svg" alt="Join the chat at https://gitter.im/november-gallery/community"></a></div>

Check out the live demo site!

A gallery plugin for OctoberCMS that tries to Keep It Simple and Stupid.

 1. Upload your images using the Media Manager built into OctoberCMS
 2. Drop November Gallery onto your page or partial
 3. Select the folder you uploaded your images to and how you want them displayed

Behind the scenes, November Gallery makes use of the [Image Resizer plugin](https://octobercms.com/plugin/toughdeveloper-imageresizer) to generate thumbnails of your images, and the excellent [UniteGallery jQuery Gallery Plugin](https://github.com/vvvmax/unitegallery). 

## Fetaures:

- Three components included: 
	 - Embedded Gallery if you wish to show a gallery of images in your page
	 - Popup Lightbox for galleries that can be opened from a link or button on your page
	 - Image List Only if you want to handle the display of the images yourself
 - Various options for the Embedded Gallery that can be configured in the back-end without writing a line of code: 
	 - tiles (arranged in columns, justified, or laid out in a grid)
	 - carousel
	 - slider
	 - combined (large image + lightbox)
	 - video
 - Set which folder of images to display in the component configuration panel OR pass it dynamically as a page-variable
 - Responsive/touch enabled/skinnable/themable/gallery buttons/keyboard control etc.

### Limitations / ToDo:
 
 - [ ] Ability to add to pages (not just CMS)
 - [ ] Carousels!
 - [ ] Alternative lightbox gallery script support
 - [ ] Sort the displayed images by file date, date taken, or filename
 - [ ] Support titles & captions embedded into the image files, or in sidecar files (with support for multiple languages)
 - [ ] Test embedding & linking video
 - [ ] Get the demo page up and running
 - [ ] Extensive help docs

## Deployment

<blockquote>
<p><strong>Note</strong>: You must have a <code>{% styles %}</code> and <code>{% scripts %}</code> tag in your page header/footer so that the plugin can inject the required assets.</p>
</blockquote>

```html
<head>
    ...
    {% styles %}
</head>
<body>
    ...
    {% scripts %}
</body>
```
For more information see the [OctoberCMS docs](https://octobercms.com/docs/cms/pages#injecting-assets)!

### Installation

To install from your site "backend": go to  **Settings → Updates & Plugins → Install Plugins**  and then search for  `November Gallery`.

To install from the  [Marketplace](https://octobercms.com/plugins): click "Add to Project" and select the project you wish to use the plugin on, and then from the backend area of your site use the `Check for updates` button on the **Settings → Updates & Plugins** page.

To install from [the repository](https://github.com/lieszkol/november-gallery): clone it into the **/plugins/** folder of your site and then run  `sudo php artisan plugin:refresh zenware.novembergallery`  from your project root.

## Usage

For further usage examples and documentation please see [The NovemberGallery Manual](https://its.zensoft.hu/books/november-gallery-for-octobercms)

### (1) Prepare and Upload your Images

You will need to 1) create folders for your images 2) resize your images and 3) upload them to your website using OctoberCMS's built-in Media Manager. 

<details>
<summary>Read more...</summary>
Although you can use any folder structure that you'd like, we recommend that you create a folder and create separate folders underneath that folder to store your albums. We also recommend not to use spaces in folder names - it's better to use dashes or underscores instead. Also, it is customary to use lowercase when creating folders for publishing on the web. 

So, your folder structure may look like this:

- my_galleries
	- my_travels
		- 2018-argentina
		- 2015-vietnam
		- 2010-hungary
	- cat_pictures
	- awesome_vacuum_cleaners

Next, prepare your pictures for display on the WWW. First, make sure that your photos are in a format that web browsers understand. This can be .jpg or .png, or .gif (which is more suitable for graphics with fewer colors and geometric lines, such as charts or icons).

Although the plugin will automatically generate thumbnails of your pictures, the full-size images will be displayed as-is. Therefore, it's also a good idea to resize all of your pictures before uploading them to the gallery. A plethora of free options exist to help you with that, I highly recommend [FastStone Image Viewer](https://www.faststone.org) (Windows), [Fast Image Resizer](https://adionsoft.net/) (Windows), or [Image Resizer for Windows](http://www.bricelam.net/ImageResizer/) (Windows). I'm sure there are great options for Mac as well - but [watch a little Louis Rossmann](https://www.youtube.com/user/rossmanngroup) to understand why Macs are evil so if you're on a Mac I'm sorry but I can't help you :-(

A typical screen resolution nowadays is around 1600x1200 pixels - so if you're looking to allow your users to see your pictures in decent quality full-screen, then resize them to fit within these constraints.

Finally, upload your pictures using the OctoberCMS Media manager into the folders you created earlier.
</details>

### (2) Configure The Plugin
Log into your "backend" and go to Settings → November Gallery to configure your defaults. Things to look out for:

 - `Settings` Tab: Select the folder that you created your galleries under from the Base Media Folder drop-down, you can also select a default layout for your galleries. 
- `Thumbnails` Tab: It is recommended to use the image resizer to automatically generate thumbnails of your pictures. For this, make sure that the "Use Image Resizer" option is ON on the Thumbnails plugin configuration tab. You can also set either the width or the height for your thumbnails here.
- `Advanced` Tab: *Inject UniteGallery Assets* should probably be on. If your theme already includes jQuery, then you can set *Inject jQuery* to OFF.

### (3) Drop the Plugin Onto your CMS Page(s)

The plugin provides three components that you can drop onto your CMS pages.

#### Embedded Gallery

Drop this component onto your CMS Page/Partial/Layout to show a gallery

#### Pop-up Lightbox
#### Image List Only

## Support

Feel free to [file an issue](https://github.com/lieszkol/november-gallery/issues/new). Feature requests are always welcome.

If there's anything you'd like to chat about, please join the NovemberGallery  [Gitter chat](https://gitter.im/november-gallery/community)!

## Credits / Major Dependencies

 - [UniteGallery jQuery Gallery Plugin](https://github.com/vvvmax/unitegallery)
 - OctoberCMS [Image Resizer plugin](https://octobercms.com/plugin/toughdeveloper-imageresizer)
 - [OctoberCMS]([https://github.com/octobercms/october](https://github.com/octobercms/october)) :-)

## License

This plugin is available in two editions: 

 1. The Personal Edition is a free plugin meant for non-profit use, such as:
	 2. Building a personal website that is not offering a product or services for sale
	 3. Academic use
	 4. Use by non-profit organizations
 2. The Professional Edition is meant for commercial use, such as:
	 1. A website selling or promoting products or services
	 2. Use by a for-profit organization

Both versions are available from the OctoberCMS Marketplace.

Commercial Use governed by the  [OctoberCMS Marketplace Purchased License](https://octobercms.com/help/license/regular)
<!--stackedit_data:
eyJoaXN0b3J5IjpbLTUxMDAzMDA3MCwtMjA1MDU5ODQ1MywxOT
A2Mjk3NDI5LDIwMTE4MzAwOTYsLTIwMDczMzE2MzksLTE2OTEy
NzcwMDMsLTE4NzkxMDc3ODcsLTM5NTczMDg2MywtMTU1NTk3OD
kwMCwtMTAxMDY4OTU5NiwyMTQ3MjAzODYsMzY0MDk3MTE2LDY0
NTMyNzE4LC00ODU2OTQ5NCw2NDY2MzA1MDUsLTE2MjY0NTExOT
csMzAxNDI0OTU3LC0xNjY0NzI3MDI0XX0=
-->