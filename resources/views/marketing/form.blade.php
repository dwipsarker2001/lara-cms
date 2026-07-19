@extends('marketing.layouts.app')
<title>ACCOUNT : Forms</title>
@section('content')
<div class="content-box">
    <div class="my_box">

    <div class="box_head">
        <div class="item">
        <h1>Forms</h1>
        </div>
        <div class="item">
        <div class="top_link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#db0505" class="bi bi-arrow-right-square-fill" viewBox="0 0 16 16">
            <path d="M0 14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v12zm4.5-6.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5a.5.5 0 0 1 0-1z"/>
            </svg> &nbsp;<a href="new_campaign.php">Start Your New Campaign Now</a>
        </div>
        </div>
    </div>

    <div class="box_body" style="min-height:680px">

    <frameset>
    <iframe name='FRAME1' src='https://hybridmail.techics.com/builder/drag-n-drop-form-builder/index.html' width='100%' height='100%' marginheight='0' marginwidth='0' scrolling='auto' frameborder='0' style="padding:0;margin:0;min-height:1030px;height:100%"> 
    </iframe>
    </frameset>

    </div>









    </div>
</div>
@endsection
