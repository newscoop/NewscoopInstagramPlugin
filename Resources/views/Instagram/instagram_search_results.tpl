<html>
<head>
  <link rel="stylesheet" href="/bundles/newscoopinstagramplugin/css/frontend.css">
</head>

<body>

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

</body>
</html>
