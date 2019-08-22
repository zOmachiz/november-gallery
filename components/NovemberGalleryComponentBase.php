<?php
namespace ZenWare\NovemberGallery\Components;

use Cms\Classes\ComponentBase;
use ZenWare\NovemberGallery\NovemberHelper;
use ZenWare\NovemberGallery\Models\Settings;
use ZenWare\NovemberGallery\Models\Gallery as Galleries;
use System\Classes\PluginManager;
use ZenWare\NovemberGallery\Classes\GalleryItem;
use October\Rain\Support\Collection;
use Illuminate\Support\Str;

// use Debugbar;   // http://wiltonsoftware.nz/blog/post/debug-october-cms-plugin

abstract class NovemberGalleryComponentBase extends ComponentBase {

	public $galleryitems;
	public $allowedExtensions = array();
	public $error;

    /**
     * Please override this!
     * 
     * @return array ['name' => '...', 'description' => '...']
     */
    abstract public function componentDetails();

	/**
	 * Inject gallery scripts and styles. This should be overridden on the component implementation level.
	 */
	abstract public function InjectScripts();

    /**
     * Configuration options that can be set on the component properties page after 
     * being dropped into a CMS page.
     * 
     * @return array Component properties page configuration options
     */
    public function defineProperties()
    {
    
        return [
            'maxItems' => [
                 'title'             => 'Max Images',
                 'description'       => 'The maximum number of images to display',
                 'default'           => 100,
                 'type'              => 'string',
                 'validationPattern' => '^[0-9]+$',
                 'validationMessage' => 'The Max Images property can only contain numbers!'
            ]
        ];
    }

    /**
     * Set proprety defaults, such as allowed extensions, and load the list of images to display.
     */
    public function onRun() {
        $this->addCss('assets/css/novembergallery.css');
		
		if (Settings::instance()->inject_jquery) 
		{
	    	$this->addJs('//cdn.jsdelivr.net/npm/jquery@3/dist/jquery.min.js');
		}

		$this->InjectScripts();

        $this->allowedExtensions = array();
        if (Settings::instance()->allowed_extensions_jpg)
        {
            $this->allowedExtensions[] = 'jpg';
            $this->allowedExtensions[] = 'jpeg';
        }
        if (Settings::instance()->allowed_extensions_gif)
        {
            $this->allowedExtensions[] = 'gif';
        }
        if (Settings::instance()->allowed_extensions_png)
        {
            $this->allowedExtensions[] = 'png';
        }
		// Debugbar::info("NovemberGallery allowed extensions is: " . \implode('|', $this->allowedExtensions));
		// Debugbar::info("Config::get('cms.storage.uploads.path') = " . Config::get('cms.storage.uploads.path'));
		// Debugbar::info("Config::get('cms.storage.media.path') = " . Config::get('cms.storage.media.path'));
		// Debugbar::info("url(Config::get('cms.storage.media.path')) = " . url(Config::get('cms.storage.media.path')));
        
		$this->galleryitems = $this->page['galleryitems'] = $this->loadMedia();
	}

	/** 
	 * Get the width of the thumbnails for this gallery.
	 */
	public function getThumbnailWidth() {
		$width = false;
		if (!empty($this->property('imageResizerWidth'))) 
		{
			$width = $this->property('imageResizerWidth');
		} 
		elseif (empty($this->property('imageResizerHeight')) && !empty(Settings::instance()->image_resizer_width))
		{
			$width = Settings::instance()->image_resizer_width;
		}
		return $width;
	}

	/** 
	 * Get the height of the thumbnails for this gallery.
	 */
	public function getThumbnailHeight() {
		$height = false;
		if (!empty($this->property('imageResizerHeight'))) 
		{
			$height = $this->property('imageResizerHeight');
		} 
		elseif (empty($this->property('imageResizerWidth')) && !empty(Settings::instance()->image_resizer_height)) 
		{
			$height = Settings::instance()->image_resizer_height;
		}
		return $height;
	}

	/** 
	 * Get the height of the thumbnails for this gallery.
	 */
	public function getGalleryWidth() {
		$width = false;
		if (!empty($this->property('galleryWidth'))) 
		{
			$width = $this->property('galleryWidth');
		} 
		if (Str::contains($width, '%')) 
		{
			$width = '"' . $width . '"';
		}
		return $width;
	}

	/** 
	 * Get the height of the thumbnails for this gallery.
	 */
	public function getGalleryHeight() {
		$height = false;
		if (!empty($this->property('galleryHeight'))) 
		{
			$height = $this->property('galleryHeight');
		} 
		if (Str::contains($height, '%')) 
		{
			$height = '"' . $height . '"';
		}
		return $height;
	}

    /**
     * Retrieve a list of all gallery items (images and videos) under the gallery path.
     * 
     * @return array Array of filenames (with extension, without path)
     */
    function loadMedia()
    {
		$maxImages = $this->property('maxItems', 100);
		$page = null;
		if (property_exists($this, 'page')) $page = $this->page;

		if (	!empty($this->property('mediaFolder')) 
			&& 	$this->property('mediaFolder') == '<post>') 
		{
			// We need to get the gallery from the post object
			// Could also go this route: Debugbar::info($this->page->components['blogPost']); 
			// but then we'd need to figure out the actual component alias instead of "blogPost"
			// In any case the RainLab posts component injects the "post" variable into the page se we can use that:
			$pluginManager = PluginManager::instance()->findByIdentifier('Rainlab.Blog');
			if (	$pluginManager
				&& 	!$pluginManager->disabled
				&& 	isset($page)
				&& 	!is_null($page->post)	// isset doesn't seem to work here, maybe it's a "magic getter" or some other Laravel/Eloquent voodoo?
				&& 	$page->post instanceof \RainLab\Blog\Models\Post
			)
			{
				$post = $page->post;
				$images     = new Collection();
				// Another method to check - but this doesn't work: isset($post->relations['novembergalleries'])
				// Some reading: https://github.com/laravel/framework/blob/5.3/src/Illuminate/Database/Eloquent/Model.php#L3239
				// https://stackoverflow.com/questions/23910553/laravel-check-if-related-model-exists
				// https://laracasts.com/discuss/channels/eloquent/this-relationloaded-for-distant-relationships
				if (	!is_null($post->novembergalleries)	// Could also use $post->relationLoaded('novembergalleries')
					&&	$post->relationLoaded('novembergalleries')
					&& 	$post->novembergalleries) 
				{
					foreach ($post->novembergalleries as $gallery) 
					{
						foreach($gallery->images->take($maxImages) as $image) {
							$images->push(GalleryItem::createFromOctoberImageFile($this, $image));
						}
					}
				}
				if (	!is_null($post->novembergalleryfields)
					&&	$post->novembergalleryfields->exists()
					&& 	$post->relationLoaded('novembergalleryfields')
					&&	!is_null($post->novembergalleryfields->media_folder)
					&& 	!empty($post->novembergalleryfields->media_folder))
				{
					// Debugbar::info($this->page->post->novembergalleryfields->media_folder);
					$baseMediaFolder = Settings::instance()->base_folder;
					if (Settings::instance()->base_blogmedia_folder != '<inherit>') {
						$baseMediaFolder = Settings::instance()->base_blogmedia_folder;
					}
					// Debugbar::info($this->page->post->novembergalleryfields);
					// Debugbar::info($this->page->post->novembergalleryfields->media_folder);
					$images = $images->merge($this->getImagesInMediaFolder($this->getGalleryPath($baseMediaFolder, $this->page->post->novembergalleryfields->media_folder), $maxImages));
				}
				return $images;
			}
		}
		elseif (	!empty($this->property('mediaFolder')) 
			&& NovemberHelper::startsWith($this->property('mediaFolder'), '[') 
			&& NovemberHelper::endsWith($this->property('mediaFolder'), ']')) 
		{
			// We have a gallery uploaded using the NovemberGallery backend menu
			
			$images     = new Collection();
			$gallery = Galleries::find(substr($this->property('mediaFolder'), 1, strlen($this->property('mediaFolder')) - 2));
			if ($gallery) // && $gallery->count() == 1 does not work for some reason, I am getting "2"??
			{
				foreach($gallery->images->take($maxImages) as $image) {
					// Debugbar::info($image);
					$images->push(GalleryItem::createFromOctoberImageFile($this, $image));
				}
			}
			return $images;
		} 
		else 
		{
			// We have a gallery uploaded using the MediaManager

			return $this->getImagesInMediaFolder($this->getGalleryPath($this->getBaseMediaFolder(), $this->getRelativeMediaFolder()), $maxImages);
		}
        return new Collection();
	}

	protected function getBaseMediaFolder()
	{
		return Settings::instance()->base_folder;
	}

	protected function getRelativeMediaFolder()
	{
		return $this->property('mediaFolder');
	}

	/**
	* Retrieve the full path to the gallery taking into account the base folder selected 
	* in the backend NovemberGallery settings page.
	* 
	* This can be called from the front-end with: {{ __SELF__.galleryPath() }}
	* 
	* @return string Path to the gallery of images to display
	*/
   protected function getGalleryPath($baseMediaFolder, $relativeMediaFolder) {
	   $galleryPath = Settings::instance()->mediaPath;
	   
	   if (!empty($baseMediaFolder)) {
		   $galleryPath .= $baseMediaFolder;
	   }
	   
	   if (!empty($relativeMediaFolder)) {
		   $galleryPath .= DIRECTORY_SEPARATOR . $relativeMediaFolder;
	   }

	   return $galleryPath;
   }
	
	protected function getImagesInMediaFolder($galleryPath, $maxImages)
	{
		$extensions = $this->allowedExtensions;
		$images     = new Collection();
		// Debugbar::info("NovemberGallery MediaPath is: {$galleryPath}");

		if (!\File::exists($galleryPath)) {
			$this->error = "NovemberGallery error: cannot find the path " . $galleryPath;
			return array();
		}
		
		$files     = \File::allFiles($galleryPath);
		// Debugbar::info("NovemberGallery found files: " . count($files));
		$i = 1;
		foreach ($files as $file) {
			if ($file->isFile() && $file->isReadable() && in_array ($file->getExtension(), $extensions)) {
				$images->push(GalleryItem::createFromPhpFile($this, $file));
			}
			if ($i >= $maxImages) break;
			$i++;
		}   
		return $images;
	}

    /**
     * Retrieve a list of folders under the "Base Media Folder" as set on the 
     * plugin backend settings page. The user can select from this list of folders 
     * in the component properties page after being dropped into a CMS page.
     * 
	 * See "Dropdown properties" in https://octobercms.com/docs/plugin/components#component-properties
	 * 
     * @return array List of folders
     */
    public function getMediaFolderOptions()
    {
		$options = NovemberHelper::getSubdirectories(Settings::instance()->base_folder);
		// Debugbar::info(Galleries::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id'));

		// Re-key the collection: https://adamwathan.me/2016/07/14/customizing-keys-when-mapping-collections/
		$galleries = Galleries::select('id', 'name')->orderBy('name')->get()->reduce(function ($galleries, $gallery) {
			$galleries->put('[' . $gallery->id . ']', $gallery->name);
			return $galleries;
		}, new Collection());

		$pluginManager = PluginManager::instance()->findByIdentifier('Rainlab.Blog');
		if ($pluginManager && !$pluginManager->disabled) {
			$galleries->put('<post>', \Lang::get('zenware.novembergallery::lang.component_properties.folder_type_post'));
		}

		$options = $options->merge($galleries); //->pluck('name', 'id'));

		return $options->toArray();
	}
	
	
    public static function getSubdirectories($baseFolder)
    {
        $mediaPath = Settings::instance()->mediaPath;
        if (!empty($baseFolder)) {
            $mediaPath .= $baseFolder;
        }

        // https://laravel.com/api/5.7/Illuminate/Contracts/Filesystem/Filesystem.html#method_allDirectories
        $directories = \File::directories($mediaPath);

        // https://laravel.com/docs/5.7/collections
        // https://hotexamples.com/site/file?hash=0xbf04831db113aec866fc4024ff9bb7faaa2503700d9125560cc67bea8cd6b2cb&fullName=src/MediaManager.php&project=talv86/easel
        $matches = collect($directories)->reduce(function ($allDirectories, $directory) use ($mediaPath) {
            $relativePath = str_replace(@"{$mediaPath}" . DIRECTORY_SEPARATOR, '', $directory);
            $allDirectories[$relativePath] = '/' . $relativePath;
            return $allDirectories;
        }, collect())->sort();

        if ($matches->count() == 0) {
            if (!empty(Settings::instance()->base_folder)) {
                return collect([DIRECTORY_SEPARATOR => "Your base folder (" . Settings::instance()->base_folder . ") does not contain any subfolders!"]);
            }
            return collect([DIRECTORY_SEPARATOR => "Create folders for your media first!"]);
        }
        return $matches;
    }
}