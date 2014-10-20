NewscoopInstagramBundle
===================

Instagram Photo View
------------------------

Provides endpoint for viewing single instagram photos cached locally.  Loads template **Resources/views/Instagram/instagram_photo.tpl** or **_views/instagram_photo.tpl** if it exists in the loaded theme.

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
        client_id: "AIzaSyDgk-HEOYiLoespmJtB3BOExYJ1yTg0xZw"
        client_secret: "https://www.googleapis.com/youtube/v3/"
        baseurl: "https://api.instagram.com/v1/"
        max_count: 500
```

Usage:
```smarty
{{ list_instagram_photos tag='lennonwall' }}
  <p>{{$photo.caption}}</p>
  <p>{{$photo.created_time}}</p>
  <p>{{$photo.id}}</p>
  <p>{{$photo.username}}</p>
  <p>{{$photo.tags}}</p>
  <p>{{$photo.location}}</p>
  <p>{{$photo.link}}</p>
{{ /list_instagram_photos }}
```
