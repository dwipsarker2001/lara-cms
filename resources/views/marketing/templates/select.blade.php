@extends('marketing.layouts.app')
<title>ACCOUNT : TEMPLATES</title>
<style>
    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .item-desc a {
        color: #555;
    }

    .coming_soon {
        font-size: 33px;
        font-weight: 100;
        color: #999;
        margin: 45px auto;
        padding: 20px;
    }

    .card img {
        -webkit-transition: opacity 0.5s ease-in-out;
        -moz-transition: opacity 0.5s ease-in-out;
        -ms-transition: opacity 0.5s ease-in-out;
        -o-transition: opacity 0.5s ease-in-out;
        transition: opacity 0.5s ease-in-out;
    }

    .btn-primary {
        color: #fff;
        background-color: #cf0b0b !important;
        border-color: #cf0b0b !important;
    }

    .btn-primary:hover {
        color: #fff;
        background-color: #000 !important;
        border-color: #000 !important;
    }

    .footer {
        font-size: 12px;
    }

    .btn-primary:not(:disabled):not(.disabled).active,
    .btn-primary:not(:disabled):not(.disabled):active,
    .show>.btn-primary.dropdown-toggle {
        color: #fff;
        background-color: #ff7d28;
        border-color: #ff7d28;
    }

    .btn-primary.focus,
    .btn-primary:focus {
        color: #fff;
        background-color: #ff7d28;
        border-color: #ff7d28;
        box-shadow: 0 0 0 0.2rem rgba(255, 166, 38, 0.5);
    }

    .card img:hover {
        opacity: 0.8;
    }

    .shadow-sm {
        box-shadow: 0 .125rem .5rem rgba(0, 0, 0, .2) !important;
    }

</style>
@section('content')
<div class="content-box contact-form">
    <div class="sub-header" style="text-align:center; color: #db0505">
        Select template for campaign
    </div>
    <!-- <div class="content-tool mt-3 mb-4">
        <a href="">
            <button class="btn-form-danger text-white">
                <i class="fa fa-plus"></i>Create Template
            </button>
        </a>
    </div> -->

    @if ( session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- Customized User Template Start -->
    
    <div class="album bg-light" id="mytemplates">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="font-weight-normal font-size-40" style="font-size: 20px;">Here are predefined templates made by Hybrid and yourself.</h2>
                <p class="text-muted">
                    You can cutomize your own amazing templates in Templates page.
                </p>
            </div>
            <div class="row">

            <!-- User Defined Templates -->
            <?php
            foreach ($mylist as $template) {
                $template_id = $template->template_id;
                $name = $template->name;
                $user = $template->user;
                if ($template_id != '0_3_form_builder') {
                    $path = __DIR__ . DIRECTORY_SEPARATOR . "../../../public/templates/user/" . $template_id;
                    if(file_exists($path . "/index.html")){
                        $files = glob($path . "/index.html");
                        $content = file_get_contents($files[0]);
                        $preg_matchs = preg_match_all('/(<title\>([^<]*)\<\/title\>)/i', $content, $m);
                        $title = $m[2][0];

                        $id = $template_id; 
                    }
                    ?>
                    <?php if(file_exists($path . "/index.html")){ ?>
                    <div class="col-md-3" id="template_card_{{ $id }}">
                        <div class="card mb-4 shadow-sm"
                            style="{{ session('badge') == $id ? 'border: solid 3px red' : '' }}">
                            <div style="height: 400px; width: 100%; background-size: 100% auto; background-repeat: no-repeat; background-image:url('{{asset('public/templates/user/'. $template_id. '/thumb.png')}}')">

                            </div>
                            <div class="card-body">
                                <h5><?php echo $name ?></h5>
                                <div class="JHf2a mb-4 small text-muted item-desc">
                                    {{ date_format($template->created_at, 'H:i d-m-Y') }}
                                </div>
                                <form method="POST" action="{{route('app.campaign.usetemplate')}}">
                                @csrf
                                <div class="d-flex justify-content-between align-items-center">
                                    <input name="campaign_id" value="{{$campaign_id}}" hidden/>
                                    <input name="template_id" value="{{$template->id}}" hidden/>
                                    
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary" type="submit">Use Template</button>
                                    </div>
                                    <a style="cursor:pointer; text-decoration:none"><small class=" text-danger fw-bold"
                                            onClick="openPreviewModal('{{$id}}')">Preview</small></a>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                    <?php
                }
            } ?>
            </div>
        </div>
    </div>
    <!-- Customized User Template End -->
</div>
<div class="modal fade" id="previewModal" class="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="dismissPreviewModal()">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body" style="padding:0px;">
                <iframe id="iframe-preview" style="width: 100%; height: 80vh"
                        src="http://localhost/Hybrid/account/public/templates/user/predefine-1/"></iframe>
            </div>
            <!-- <div class="modal-footer">
                <a href="<?php echo env('base_url'). '?page_id=492' ?>">
                    <button type="button" class="button-upgrade">Upgrade Your Account</button>
                </a>
            </div> -->
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    function openPreviewModal(id) {
        $("#iframe-preview").attr("src", "{{url('/public/templates/user/')}}" + '/' + id);
        $("#previewModal").modal('show');
    }
    
    function dismissPreviewModal() {
        $("#previewModal").modal('hide');
    }
    
</script>
@endsection
