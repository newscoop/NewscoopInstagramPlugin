NewscoopInstagramPluginBundle
===================

This Newscoop Plugin adds smarty functions and Admin tools to enable you to ingest, manage, and display Instagram photos in Newscoop.


Install instructions for Newscoop v4.3 on OSX with MAMP
------------------------

1. php application/console plugin:install newscoop/instagram-plugin-bundle 
2. Edit the newscoop/application/configs/parameters/custom_parameters.yml file (or create it if it does not exist) and add the following text:
```
parameters:
    instagram_bundle:
        client_id: "your client id"
        client_secret: "your client secret"
        baseurl: "https://api.instagram.com/v1/"
        max_count: 500
```
3. Run the following command to ingest the initial 500 photos from Instagram:
```
php application/console instagram_photos:ingest lennonwall
```

Update instructions for Newscoop v4.3 on OSX with MAMP
------------------------

1. php application/console plugin:remove newscoop/instagram-plugin-bundle
6. follow steps for manual install above



Instagram Photo View
------------------------

Provides endpoint, **/instagram/photos/{id}** for viewing single instagram photos cached locally.  Loads template **Resources/views/Instagram/instagram_photo.tpl** or **_views/instagram_photo.tpl** if it exists in the loaded theme.

Usage:
```smarty
<img src="{{ $instagramPhoto->getThumbnailUrl() }}" width="{{ $instagramPhoto->getThumbnailWidth() }}" height="{{ $instagramPhoto->getThumbnailHeight() }}">

<img src="{{ $instagramPhoto->getLowResolutionUrl() }}" width="{{ $instagramPhoto->getLowResolutionWidth() }}" height="{{ $instagramPhoto->getLowResolutionHeight() }}">

<img src="{{ $instagramPhoto->getStandardResolutionUrl() }}" width="{{ $instagramPhoto->getStandardResolutionWidth() }}" height="{{ $instagramPhoto->getStandardResolutionHeight() }}">

<p>Id: {{ $instagramPhoto->getId() }}</p>
<p>Link: {{ $instagramPhoto->getLink() }}</p>
<p>Caption: {{ $instagramPhoto->getCaption() }}</p>
<p>Tags: {{ $instagramPhoto->getTags() }}</p>
<p>Created by Instagram User: {{ $instagramPhoto->getInstagramUserName() }}</p>
<p>Created on: {{ $instagramPhoto->getCreatedAt()|date_format:"Y-m-d" }}</p>
```

Instagram Photos Search 
------------------------

Note that this only searches the locally stored InstagramPhoto entities, it does NOT make a call to the Instagram Api

Provides endpoint **/instagram/photosearch**, which takes the following params:

1. search - search string, matches against caption, username, tags, and locationName fields
2. perPage - number of records per page to send in the results
3. offset - the first record to start with (used for pagination)

Results are delivered to **_views/instagram_search_results.tpl**, if defined in your theme, or a default internal view is used.

Usage:
```smarty
<div id="nav">
   <div class="left-nav"><a href="{{ $prevPageUrl }}">Previous</a></div>
   <div class="center-nav"><span>found {{ $instagramPhotoCount }} results</span></div>
   <div class="right-nav"><a href="{{ $nextPageUrl }}">Next</a></div>
</div>
<br class="clear">

<ul id="photo-results-container">
{{ foreach $instagramPhotos as $photo }}
    <li>
        <div id="box" style="background: url({{ $photo->getLowResolutionUrl() }})">
            <div id="overlay">
                <span id="plus">
                    Posted By: {{ $photo->getInstagramUserName() }}<br>
                    On: {{ $photo->getCreatedAt()|date_format:"Y-m-d" }}<br>
                    <br>
                    {{ $photo->getCaption() }}
                </span>
            </div>
        </div>
    </li>
{{ /foreach }}
</ul>
```
 
Instagram List Photos Smarty Block
------------------------

Provides a smarty block to list instagrams photos with a specfific hashtag.

Usage:
```smarty
{{ list_instagram_photos tag='lennonwall'  length=30 }}
  <p>{{ $photoIndex }}</p>
  <p>{{ $photo->getCaption() }}</p>
  <p>{{ $photo->getCreatedAt() }}</p>
  <p>{{ $photo->getId() }}</p>
  <p>{{ $photo->getInstagramUserName() }}</p>
  <p>{{ $photo->getTags() }}</p>
  <p>{{ $photo->getLocationName() }}</p>
  <p>{{ $photo->getLocationLatitude() }}</p>
  <p>{{ $photo->getLocationLingitude() }}</p>
  <p>{{ $photo->getLink() }}</p>

  <p>{{ $photo->getThumbnailUrl() }}</p>
  <p>{{ $photo->getThumbnailWidth() }}</p>
  <p>{{ $photo->getThumbnailHeight() }}</p>

  <p>{{ $photo->getStandardResolutionUrl() }}</p>
  <p>{{ $photo->getStandardResolutionWidth() }}</p>
  <p>{{ $photo->getStandardResolutionThumbnailHeight() }}</p>

  <p>{{ $photo->getLowResolutionUrl() }}</p>
  <p>{{ $photo->getLowResolutionWidth() }}</p>
  <p>{{ $photo->getLowResolutionHeight() }}</p>

{{ /list_instagram_photos }}
```

Instagram Photo Ingest Console Command
------------------------

Usage:
```
php application/console instagram_photos:ingest lennonwall
```

where **lennonwall** is the instagram hashtag that you wish to import from
