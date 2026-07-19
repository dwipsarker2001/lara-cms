@extends('marketing.layouts.app')
<title>ACCOUNT : Help</title>
@section('content')
<div class="content-box">
    <div class="my_box">

    <div class="box_head">
        <div class="item">
        <h1>Help & Support</h1>
        </div>
        <div class="item">
        <div class="top_link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#db0505" class="bi bi-arrow-right-square-fill" viewBox="0 0 16 16">
            <path d="M0 14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v12zm4.5-6.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5a.5.5 0 0 1 0-1z"/>
            </svg> &nbsp;<a href="new_campaign.php">Start Your New Campaign Now</a>
        </div>
        </div>
    </div>


    <br><br>
    <h3 class="mt-15">FAQ</h3>

    <div class="box_body" style="margin-top:5px">


    <style>
    .accordion {
    width: 100%;
    }
    </style>

    <div class="accordion" id="accordionExample">
    <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
    How to design your HTML email?
    </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
    <div class="accordion-body">
    <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
    </div>
    </div>
    </div>
    <div class="accordion-item">
    <h2 class="accordion-header" id="headingTwo">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
    How to start an campaign?
    </button>
    </h2>
    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
    <div class="accordion-body">
    <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
    </div>
    </div>
    </div>
    <div class="accordion-item">
    <h2 class="accordion-header" id="headingFour">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseTwo">
    Can I use images and videos in my emails?
    </button>
    </h2>
    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
    <div class="accordion-body">
    <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
    </div>
    </div>
    </div>
    <div class="accordion-item">
    <h2 class="accordion-header" id="headingThree">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
    How do I import my contact list?
    </button>
    </h2>
    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
    <div class="accordion-body">
    <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
    </div>
    </div>
    </div>
    </div>

    </div>

    <div class="box_body4" style="min-height:260px">
    <h3 class="mt-15">Support</h3>
    <div class="row mt-15">
    <div class="col-md-3">
                <button class="my_but">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
        <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
        <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
        </svg><span class="spacer-left-xxs">Create New Ticket</span>
                </button>
    </div>
    <div class="col-md-3">
    <button class="my_but">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-chat-left-dots" viewBox="0 0 16 16">
    <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
    <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
    </svg><span class="spacer-left-xxs">Contact Support Team</span>
                </button>
    </div>
    </div>

    </div>











    </div>
</div>
@endsection
