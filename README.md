NewscoopInstagramBundle
===================

Tag View
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
