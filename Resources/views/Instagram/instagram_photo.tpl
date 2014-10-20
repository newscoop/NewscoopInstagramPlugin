<h4>instagram_photo.tpl</h4>

<img src="{{ $instagramPhoto->getThumbnailUrl() }}" width="{{ $instagramPhoto->getThumbnailWidth() }}" height="{{ $instagramPhoto->getThumbnailHeight() }}">

<img src="{{ $instagramPhoto->getLowResolutionUrl() }}" width="{{ $instagramPhoto->getLowResolutionWidth() }}" height="{{ $instagramPhoto->getLowResolutionHeight() }}">

<img src="{{ $instagramPhoto->getStandardResolutionUrl() }}" width="{{ $instagramPhoto->getStandardResolutionWidth() }}" height="{{ $instagramPhoto->getStandardResolutionHeight() }}">

<p>Id: {{ $instagramPhoto->getId() }}</p>
<p>Link: {{ $instagramPhoto->getLink() }}</p>
<p>Caption: {{ $instagramPhoto->getCaption() }}</p>
<p>Tags: {{ $instagramPhoto->getTags() }}</p>
<p>Created by Instagram User: {{ $instagramPhoto->getInstagramUserName() }}</p>
<p>Created on: {{ $instagramPhoto->getCreatedAt()|date_format:"Y-m-d" }}</p>
