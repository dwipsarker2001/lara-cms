@extends('marketing.layouts.app')
<!doctype html>
<title>ACCOUNT : EDIT TEMPLATE</title>
@section('customStyle')
<link rel="stylesheet" href="{{ asset('assets/libs/dist/builder.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
    integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.js"
    integrity="sha512-sn/GHTj+FCxK5wam7k9w4gPPm6zss4Zwl/X9wgrvGMFbnedR8lTUSLdsolDRBRzsX6N+YgG6OWyvn9qaFVXH9w=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
@section('content')
<div class="content-box">
    <div class="sub-header">
        Edit Template
    </div>

    <div class="contact-content mt-2">
        <div style="text-align: center;
            height: 100vh;
            vertical-align: middle;
            padding: auto;
            display: flex;">
            <div style="margin:auto" class="lds-dual-ring"></div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script src="{{ asset('assets/libs/dist/builder.js') }}"></script>
<script>
    var editor;
    var params = new URLSearchParams(window.location.search);
    // var templates = [{
    //         "name": "Blank",
    //         "url": "design.php?id=6037a0a8583a7&type=default",
    //         "thumbnail": "templates\/default\/6037a0a8583a7\/thumb.png"
    //     },
    //     {
    //         "name": "Pricing Table",
    //         "url": "design.php?id=6037a2135b974&type=default",
    //         "thumbnail": "templates\/default\/6037a2135b974\/thumb.png"
    //     },
    //     {
    //         "name": "Listing & Tables",
    //         "url": "design.php?id=6037a2250a3a3&type=default",
    //         "thumbnail": "templates\/default\/6037a2250a3a3\/thumb.png"
    //     },
    //     {
    //         "name": "Forms Building",
    //         "url": "design.php?id=6037a23568208&type=default",
    //         "thumbnail": "templates\/default\/6037a23568208\/thumb.png"
    //     },
    //     {
    //         "name": "1-2-1 column layout",
    //         "url": "design.php?id=6037a2401b055&type=default",
    //         "thumbnail": "templates\/default\/6037a2401b055\/thumb.png"
    //     },
    //     {
    //         "name": "1-2 column layout",
    //         "url": "design.php?id=6037a24ebdbd6&type=default",
    //         "thumbnail": "templates\/default\/6037a24ebdbd6\/thumb.png"
    //     },
    //     {
    //         "name": "1-3-1 column layout",
    //         "url": "design.php?id=6037a25ddce80&type=default",
    //         "thumbnail": "templates\/default\/6037a25ddce80\/thumb.png"
    //     },
    //     {
    //         "name": "1-3-2 column layout",
    //         "url": "design.php?id=6037a26b0a286&type=default",
    //         "thumbnail": "templates\/default\/6037a26b0a286\/thumb.png"
    //     },
    //     {
    //         "name": "1-3 column layout",
    //         "url": "design.php?id=6037a275bf375&type=default",
    //         "thumbnail": "templates\/default\/6037a275bf375\/thumb.png"
    //     },
    //     {
    //         "name": "One column layout",
    //         "url": "design.php?id=6037a28418c95&type=default",
    //         "thumbnail": "templates\/default\/6037a28418c95\/thumb.png"
    //     },
    //     {
    //         "name": "2-1-2 column layout",
    //         "url": "design.php?id=6037a29a35e05&type=default",
    //         "thumbnail": "templates\/default\/6037a29a35e05\/thumb.png"
    //     },
    //     {
    //         "name": "2-1 column layout",
    //         "url": "design.php?id=6037a2aa315d4&type=default",
    //         "thumbnail": "templates\/default\/6037a2aa315d4\/thumb.png"
    //     },
    //     {
    //         "name": "Two columns layout",
    //         "url": "design.php?id=6037a2b67ed27&type=default",
    //         "thumbnail": "templates\/default\/6037a2b67ed27\/thumb.png"
    //     },
    //     {
    //         "name": "3-1-3 column layout",
    //         "url": "design.php?id=6037a2c3d7fa1&type=default",
    //         "thumbnail": "templates\/default\/6037a2c3d7fa1\/thumb.png"
    //     },
    //     {
    //         "name": "Three columns layout",
    //         "url": "design.php?id=6037a2dcb6c56&type=default",
    //         "thumbnail": "templates\/default\/6037a2dcb6c56\/thumb.png"
    //     }
    // ];
    var templates = []

    var tags = [
        {
            type: 'label',
            tag: '{UNSUBSCRIBE_URL}'
        },
        {
            type: 'label',
            tag: '{CONTACT_FIRST_NAME}'
        },
        {
            type: 'label',
            tag: '{CONTACT_LAST_NAME}'
        },
        {
            type: 'label',
            tag: '{CONTACT_FULL_NAME}'
        },
        {
            type: 'label',
            tag: '{CONTACT_EMAIL}'
        },
        {
            type: 'label',
            tag: '{CONTACT_PHONE}'
        },
        {
            type: 'label',
            tag: '{CONTACT_ADDRESS}'
        },
        {
            type: 'label',
            tag: '{ORDER_ID}'
        },
        {
            type: 'label',
            tag: '{ORDER_DUE}'
        },
        {
            type: 'label',
            tag: '{ORDER_TAX}'
        },
        {
            type: 'label',
            tag: '{PRODUCT_NAME}'
        },
        {
            type: 'label',
            tag: '{PRODUCT_PRICE}'
        },
        {
            type: 'label',
            tag: '{PRODUCT_QTY}'
        },
        {
            type: 'label',
            tag: '{PRODUCT_SKU}'
        },
        {
            type: 'label',
            tag: '{AGENT_NAME}'
        },
        {
            type: 'label',
            tag: '{AGENT_SIGNATURE}'
        },
        {
            type: 'label',
            tag: '{AGENT_MOBILE_PHONE}'
        },
        {
            type: 'label',
            tag: '{AGENT_ADDRESS}'
        },
        {
            type: 'label',
            tag: '{AGENT_WEBSITE}'
        },
        {
            type: 'label',
            tag: '{AGENT_DISCLAIMER}'
        },
        {
            type: 'label',
            tag: '{CURRENT_DATE}'
        },
        {
            type: 'label',
            tag: '{CURRENT_MONTH}'
        },
        {
            type: 'label',
            tag: '{CURRENT_YEAR}'
        },
        {
            type: 'button',
            tag: '{PERFORM_CHECKOUT}',
            'text': 'Checkout'
        },
        {
            type: 'button',
            tag: '{PERFORM_OPTIN}',
            'text': 'Subscribe'
        }
    ];

    $(document).ready(function () {
        var strict = true;

        if (params.get('type') == 'custom') {
            strict = false;
        }

        editor = new Editor({
            strict: strict, // default == true
            showInlineToolbar: true, // default == true
            root: "{{ asset('assets/libs/dist') }}",
            url: "{{ 'public/templates/'. $type. '/'. $id }}?v={{ time() }}",
            urlBack: "{{ url('design/save_name?template_id='. $id) }}",
            uploadAssetUrl: "{{ url('template/uploadAsset') }}",
            uploadAssetMethod: 'POST',
            uploadTemplateUrl: 'upload.php',
            uploadTemplateCallback: function (response) {
                window.location = response.url;
            },
            saveUrl: "{{ url('design/save') }}",
            saveMethod: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: "{{ $type }}",
                template_id: "{{ $id }}"
            },
            templates: templates,
            tags: tags,
            changeTemplateCallback: function (url) {
                window.location = url;
            },

            /*
                Disable features: 
                change_template|export|save_close|footer_exit|help
            */
            // disableFeatures: [ 'change_template', 'export', 'save_close', 'footer_exit', 'help' ], 
            disableFeatures: ['change_template', 'export'],


            // disableWidgets: [ 'HeaderBlockWidget' ], // disable widgets
            export: {
                url: 'export.php'
            },
            backgrounds: [
                "{{ asset('assets/builder/assets/image/backgrounds/images1.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images2.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images3.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images4.png') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images5.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images6.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images9.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images11.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images12.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images13.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images14.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images15.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images16.jpg') }}",
                "{{ asset('assets/builder/assets/image/backgrounds/images17.png') }}",
            ]
        });

         // @RSS plugin We don't use it for now.
        // var rssWidget = new RssWidget({ handler: 'rss.php' });
        // editor.addWidget(rssWidget, { index: 10 });

        $( document ).ready(function() {
            editor.init();
        });
        // editor.init();
    });

    function savethumbnail() {
        console.log("Thumbnail");
        // console.log($("#builder_iframe").contents().find(".builderjs-layout")[0]);
        var images = $("#builder_iframe").contents().find("img");
        var links = $("#builder_iframe").contents().find("link");

        for (i = 0; i < links.length; i++) {
            links[i].href = links[i].href;
        }

        for (i = 0; i < images.length; i++) {
            images[i].src = images[i].src;
        }
        console.log($("#builder_iframe").contents().find(".builderjs-layout div")[0]);
        html2canvas($("#builder_iframe").contents().find(".builderjs-layout div")[0]).then((canvas) => {
            console.log("done ... ");
            var data = canvas.toDataURL('image/png');
            // console.log(data);
            $.ajax({
                url: "{{ route('app.template.save-thumbnail') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                method: "post",
                data: {
                    templateId: params.get('id'),
                    data: data
                    // user_id: Send user id here
                }
            }).then(() => {

            })
        });
    }

</script>
@endsection
