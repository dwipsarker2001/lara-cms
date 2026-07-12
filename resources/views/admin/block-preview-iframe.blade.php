<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1200">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        html, body { margin: 0; padding: 0; overflow: hidden; }
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        [data-edit] { cursor: default !important; }
        [data-edit]:hover { outline: none !important; }
        .preview-desktop {
            width: 1200px;
            transform-origin: top left;
        }
    </style>
</head>
<body>
    <div class="preview-desktop" id="preview-desktop">
        @if(view()->exists($block->view()))
            @include($block->view(), [
                'data' => $data,
                '_key' => '',
                'preview' => true,
            ])
        @endif
    </div>
    <script>
        window.addEventListener('load', function () {
            const wrapper = document.getElementById('preview-desktop');
            const scale = window.innerWidth / 1200;
            wrapper.style.transform = 'scale(' + scale + ')';
            const h = Math.ceil(wrapper.scrollHeight * scale);
            parent.postMessage({ blockName: '{{ $block->name }}', iframeHeight: h }, '*');
        });
    </script>
</body>
</html>
