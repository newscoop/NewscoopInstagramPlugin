NewscoopInstagramBundle
===================

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



Instagram List Photos Smarty Block
------------------------

Provides a smarty block to list instagrams photos with a specfific hashtag.
Requires the following parameters to be defined in newscoop/application/configs/parameters/custom_parameters.yml 

```
parameters:
    instagram_bundle:
        client_id: "96554735526e43ca92ce82915054c5a5"
        client_secret: "c034d3fd55aa4ab69c58bba8d924a245"
        baseurl: "https://api.instagram.com/v1/"
        max_count: 500
```

Usage:
```smarty
{{ list_instagram_photos tag='lennonwall' }}
  <p>{{ $photo.getCaption() }}</p>
  <p>{{ $photo.getCreatedAt() }}</p>
  <p>{{ $photo.getId() }}</p>
  <p>{{ $photo.getInstagramUserName() }}</p>
  <p>{{ $photo.getTags() }}</p>
  <p>{{ $photo.getLocationName() }}</p>
  <p>{{ $photo.getLocationLatitude() }}</p>
  <p>{{ $photo.getLocationLingitude() }}</p>
  <p>{{ $photo.getLink() }}</p>

  <p>{{ $photo.getThumbnailUrl() }}</p>
  <p>{{ $photo.getThumbnailWidth() }}</p>
  <p>{{ $photo.getThumbnailHeight() }}</p>

  <p>{{ $photo.getStandardResolutionUrl() }}</p>
  <p>{{ $photo.getStandardResolutionWidth() }}</p>
  <p>{{ $photo.getStandardResolutionThumbnailHeight() }}</p>

  <p>{{ $photo.getLowResolutionUrl() }}</p>
  <p>{{ $photo.getLowResolutionWidth() }}</p>
  <p>{{ $photo.getLowResolutionHeight() }}</p>

{{ /list_instagram_photos }}
```

Instagram Photo Ingest Console Command
------------------------

Usage:
```
php application/console instagram_photos:ingest lennonwall
```

where **lennonwall** is the instagram hashtag that you wish to import from
