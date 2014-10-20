<h4>instagram_photo_list.tpl</h4>

{{ list_instagram_photos tag="lennonwall" length=30 }}
  <img src="{{ $photo->getThumbnailUrl() }}" width="{{ $photo->getThumbnailWidth() }}" height="{{ $photo->getThumbnailHeight() }}">
{{ /list_instagram_photos }}

