@extends('marketing.layouts.app')
<title>ACCOUNT : Edit Form</title>

@section('customStyle')
<link rel="stylesheet" href="https://www.phpformbuilder.pro/documentation/assets/stylesheets/bootstrap.min.css">
<link rel="stylesheet" href="https://www.phpformbuilder.pro/documentation/assets/stylesheets/drag-and-drop.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/formbuilder.css') }}">
<link rel="stylesheet"
    href="{{ asset('assets/formbuilder/assets/css/icomoon.min.css') }}">
<style>
    .my_sidebar li a {
        color: white!important;
    }
</style>
@endsection

@section('content')
<div class="form-builder-content">
    <div class="sub-header">
        Edit Form
    </div>

    @if( session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <div class="mt-4">
        <div id="form-generator-container" class="container-fluid">
            <div class="d-flex mb-4">
                <label style="width: 200px; font-size:20px;">Form Name: <span class="text-danger">*</span></label>
                <input class="form-control container-fluid" name="form_name" id="form_name"
                    value="{{ $data->name }}" />
                <input class="form-control container-fluid" name="form_path" id="form_path" value="{{ $data->path }}"
                    hidden />
            </div>
            <div class="row mx-0 mb-1 overflow-hidden">
                <div id="ui-icon-bars-left-column"
                    class="col-md-2 mb-1 d-none d-md-flex align-items-center justify-content-start">
                    <a href="#" class="d-block p-3 text-decoration-none"><i
                            class="icon-bars text-secondary fs-4"></i></a>
                </div>
                <div class="col-md-8 d-flex justify-content-between mb-1">
                    <button id="update-json-on-server-btn" class="btn nowrap w-100 h-100 mx-1 btn-success text-white" type="button"
                        base-url="{{ url('') }}" csrf="{{ csrf_token() }}"
                        file-name="{{ $data->path }}" style="font-size: 20px;">Save
                        <i class="fa fa-save ms-2 text-white"></i></button>
                    <button id="main-settings-btn" class="btn nowrap w-100 h-100 mx-1 btn-primary" type="button"
                        data-bs-toggle="modal" data-bs-target="#main-settings-modal" style="font-size: 20px;">Form
                        settings <i class="icon-tools ms-2 text-white"></i></button>
                    <div class="text-right">
                        <a id="back-btn" href="{{ route('app.form.index') }}"
                            class="btn w-100 mb-1 nowrap text-white" style="background: #888">Go Back<i class="fa fa-sign-in ms-2"></i></a>
                        <a id="preview-btn" href="#" class="btn w-100 mb-1 nowrap btn-primary" data-bs-toggle="modal"
                            data-bs-target="#preview-modal">Preview<i class="icon-eye ms-2"></i></a>
                    </div>
                </div>
                <div id="ui-icon-bars-right-column"
                    class="col-md-2 mb-1 d-none d-md-flex align-items-center justify-content-end">
                    <a href="#" class="d-block p-3 text-decoration-none"><i
                            class="icon-bars text-secondary fs-4"></i></a>
                </div>
            </div>
            <div id="app" class="row flex-nowrap overflow-hidden mx-0">

                <!--=====================================
        =              Left column              =
        ======================================-->

                <aside id="ui-left-column" class="col-md-3 mb-3" aria-label="Components">
                    <div class="card border-secondary h-100">
                        <div class="card-header text-white bg-dark bg-opacity-75 text-uppercase small">Components</div>
                        <div class="card-body small">

                            <ul id="sidebar-components"
                                class="list-group list-group-horizontal text-secondary align-items-stretch flex-wrap">
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="input">
                                    <i class="icon-input icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">Input</span>
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="textarea">
                                    <i class="icon-textarea icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">Textarea</span>
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="select">
                                    <i class="icon-select icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">Select</span>
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="radio">
                                    <i class="icon-radio-checked icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">Radio</span>
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="checkbox">
                                    <i class="icon-checkbox-checked icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">Checkbox</span>
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="fileuploader">
                                    <i class="icon-upload icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">Fileupload</span>
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="hcaptcha">
                                    <i class="icon-hcaptcha icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">Hcaptcha</span>
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="recaptcha">
                                    <i class="icon-recaptcha icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">Recaptcha</span>
                                </li>
                                <li class="list-unstyled-item w-100">

                                    <hr class="mx-5">
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="button">
                                    <i class="icon-button icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">Button</span>
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="buttongroup">
                                    <i class="icon-btn-group icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray nowrap">Btn group</span>
                                </li>
                                <li class="list-unstyled-item w-100">

                                    <hr class="mx-5">
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center nowrap"
                                    data-component="dependent">
                                    <i class="icon-bubbles icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">Start<br>Condition</span>
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center nowrap"
                                    data-component="dependentend">
                                    <i class="icon-bubbles icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">End<br>Condition</span>
                                </li>
                                <li class="list-unstyled-item w-100">

                                    <hr class="mx-5">
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="heading">
                                    <i class="icon-title icon-lg text-secondary aria-hidden"></i><span
                                        class="text-muted">Title</span>
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="paragraph">
                                    <i class="icon-short_text icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">Paragraph</span>
                                </li>
                                <li class="list-group-item d-flex flex-column justify-content-center align-items-center"
                                    data-component="html">
                                    <i class="icon-html-five icon-lg text-secondary aria-hidden"></i><span
                                        class="text-gray">HTML</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </aside>

                <!--=====================================
        =              Main Content             =
        ======================================-->

                <main class="col mb-3">
                    <div class="card border-secondary w-100 h-100">
                        <div class="card-header text-white bg-dark bg-opacity-75 text-uppercase small">Drop components
                            here
                            to build your form</div>
                        <div class="card-body px-1">
                            <div id="fg-form-wrapper"></div>
                        </div>
                    </div>
                </main>

                <!--=====================================
        =              Right column             =
        ======================================-->

                <aside id="ui-right-column" class="col-md-4 col-lg-3 mb-3" aria-label="Component settings">
                    <div class="card border-secondary h-100">
                        <div class="card-header text-white bg-dark bg-opacity-75 text-uppercase small">Component
                            settings
                        </div>
                        <div class="card-body">
                            <div id="components-settings"></div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <!--=====================================
    =                Modals                 =
    ======================================-->

        <!-- load JSON from disk -->
        <!-- <div id="load-json-from-disk-modal" class="modal fade" tabindex="-1" role="dialog"
            aria-labelledby="load-json-from-disk-btn" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <h5 class="modal-title">Upload your form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <input id="json-file-disk-input" style="display: none" type="file" accept=".json">
                        <button class="btn btn-primary"
                            onclick="document.getElementById('json-file-disk-input').click();">Browse</button>
                        <div id="json-file-disk-output"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                        <button id="json-file-disk-load-btn" type="button" class="btn btn-primary">Load <i
                                class="icon-upload1 text-white ms-2"></i></button>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- load JSON from server -->
        <!-- <div id="load-json-from-server-modal" class="modal fade" tabindex="-1" role="dialog"
            aria-labelledby="load-json-from-server-btn" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <h5 class="modal-title">Upload your form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="demo-server-delete-disabled"></div>
                        <div id="json-file-server-output"></div>
                        <div id="json-forms-file-tree-wrapper">
                            <div class="ft-tree"></div>
                            <div class="ft-explorer"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                        <button id="json-file-server-load-btn" type="button" class="btn btn-primary">Load <i
                                class="icon-upload text-white ms-2"></i></button>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- main settings -->
        <div id="main-settings-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="main-settings-btn"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content" id="user-form-settings">
                    <div class="modal-header">

                        <h5 class="modal-title">Form Settings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">

                            <div class="row justify-content-center">

                                <ul class="col-md-8 nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link text-secondary active" id="nav-tab-main-settings-action"
                                            data-bs-toggle="tab" href="#tab-main-settings-action" role="tab"
                                            aria-controls="tab-main-settings-action" aria-selected="false">Email Setting</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-secondary" id="nav-tab-main-settings-main"
                                            data-bs-toggle="tab" href="#tab-main-settings-main" role="tab"
                                            aria-controls="tab-main-settings-main" aria-selected="true">Style Setting</a>
                                    </li>
                                    <!-- <li class="nav-item">
                                        <a class="nav-link text-secondary" id="nav-tab-main-settings-plugins"
                                            data-bs-toggle="tab" href="#tab-main-settings-plugins" role="tab"
                                            aria-controls="tab-main-settings-plugins" aria-selected="false">Form
                                            plugins</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-secondary" id="nav-tab-main-settings-ajax"
                                            data-bs-toggle="tab" href="#tab-main-settings-ajax" role="tab"
                                            aria-controls="tab-main-settings-ajax" aria-selected="false">Ajax
                                            loading</a>
                                    </li> -->
                                </ul>
                            </div>
                            <div class="row justify-content-center">
                                <p class="col-md-8 small text-right text-muted py-2 mb-4">All the changes are registered
                                    in
                                    real time.</p>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row justify-content-center tab-content">
                                <div id="tab-main-settings-main" class="col-md-8 tab-pane fade"
                                    role="tabpanel" aria-labelledby="nav-tab-main-settings-main">

                                    <section id="user-form-settings-main"></section>
                                </div>
                                <div id="tab-main-settings-action" class="col-md-8 tab-pane fade show active" role="tabpanel"
                                    aria-labelledby="nav-tab-main-settings-action">

                                    <section id="user-form-settings-action"></section>
                                    <div id="collapsible-form-actions" class="accordion mt-4">
                                        <fieldset id="send-email" class="collapse show"
                                            data-parent="#collapsible-form-actions">
                                            <legend class="h4 p-2 border-bottom border-bottom-gray fw-light bg-danger">Send email
                                            </legend>

                                            <section id="user-form-settings-sendmail"></section>
                                        </fieldset>
                                        <fieldset id="db-insert" class="collapse"
                                            data-parent="#collapsible-form-actions">
                                            <legend class="h4 pb-2 border-bottom border-bottom-gray fw-light">Database
                                                insert</legend>

                                            <section id="user-form-settings-db-insert"></section>
                                        </fieldset>
                                        <fieldset id="db-update" class="collapse"
                                            data-parent="#collapsible-form-actions">
                                            <legend class="h4 pb-2 border-bottom border-bottom-gray fw-light">Database
                                                update</legend>

                                            <section id="user-form-settings-db-update"></section>
                                        </fieldset>
                                        <fieldset id="db-delete" class="collapse"
                                            data-parent="#collapsible-form-actions">
                                            <legend class="h4 pb-2 border-bottom border-bottom-gray fw-light">Database
                                                delete</legend>

                                            <section id="user-form-settings-db-delete"></section>
                                        </fieldset>
                                    </div>
                                </div>
                                <div id="tab-main-settings-plugins" class="col-md-8 tab-pane fade" role="tabpanel"
                                    aria-labelledby="nav-tab-main-settings-plugins">

                                    <section id="user-form-settings-plugins"></section>
                                </div>
                                <div id="tab-main-settings-ajax" class="col-md-8 tab-pane fade" role="tabpanel"
                                    aria-labelledby="nav-tab-main-settings-ajax">
                                    <div class="alert alert-info text-center mb-5">

                                        <h5 class="alert-heading">If you use a CMS, enable Ajax loading</h5>

                                        <hr>
                                        <p>Ajax loading allows you to insert the form into your HTML page without PHP
                                            code.
                                        </p>
                                        <p class="mb-0">The form is saved in an separate PHP file, called by the Ajax
                                            script.</p>
                                    </div>

                                    <section id="user-form-settings-ajax" class="text-center w-50 mx-auto"></section>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- preview -->
        <div id="preview-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="preview-btn"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">

                        <h5 class="modal-title">Form Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- get code -->
        <!-- <div id="get-code-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="get-code-btn"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">

                        <h5 class="modal-title">Form Code</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- errors -->
        <div id="errors-modal" class="modal fade" tabindex="-1" role="dialog" data-show="false" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">

                        <h5 class="modal-title fw-light">Your form has errors</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
 document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            var script1 = document.createElement('script');
            script1.src = "{{ asset('assets/formbuilder/assets/javascripts/loadjs.min.js') }}";
            document.head.appendChild(script1);

            var script2 = document.createElement('script');
            script2.src = "{{ asset('assets/formbuilder/assets/javascripts/bundle.js') }}";
            document.head.appendChild(script2);

            $("#form_name").focus();
        }, 500); // 0.5 seconds delay
    });
</script>
@endsection
