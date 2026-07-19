@extends('marketing.layouts.app')

<title>ACCOUNT : EDIT CONTACT</title>

@section('content')
<div class="content-box contact-form">
    <div class="sub-header">
        Edit Contact
    </div>
    <div class="content-tool mt-3 mb-4">
        <a href="{{ route('app.contact.index', $contact->group_id) }}">
            <button class="btn-form-danger text-white">
                <i class="fa fa-arrow-left"></i> Back To Contact List
            </button>
        </a>
    </div>
    <form method="POST" action="{{ route('app.contact.update') }}">
        @csrf
        <input type="text" placeholder="Email Address*" name="id" class="span2 my_input w200" value="{{ $contact->id }}" hidden>
        <input name="group_id" value="{{ $contact->group_id }}" hidden>

        <div class="row m-0" style="padding-top:20px;">
            <div class="col-12 col-md-6">
                <input type="email" placeholder="Email Address*" name="email" id="email" class="span2 my_input w200" value="{{ $contact->email }}" required>
                <span id="email-error" class="text-danger"></span>
            </div>
            <div class="col-12 col-md-6">
                <label class="mt-3 ms-2 text-danger">Required*</label>
            </div>
        </div>

        <div class="row m-0">
            <div class="col-12 col-md-6">
                <input type="text" placeholder="First Name" name="firstname" class="span2 my_input w200" value="{{ $contact->firstname }}">
            </div>
			<div class="col-12 col-md-6">
                <input type="text" placeholder="Last Name" name="lastname" class="span2 my_input w200" value="{{ $contact->lastname }}">
            </div>
        </div>
        <div class="row m-0">
            <div class="col-12 col-md-6">
                <input type="text" placeholder="SMS" name="sms" class="span2 my_input w200" value="{{ $contact->sms }}">
            </div>
            <div class="col-12 col-md-6">
                <input type="text" placeholder="Whatsapp" name="whatsapp" class="span2 my_input w200" value="{{ $contact->whatsapp }}">
            </div>
        </div>
        <div class="row m-0">
            <div class="col-12 col-md-6">
                <input type="text" placeholder="DOUBLE_OPT-IN" name="double_opt_in" class="span2 my_input w200" value="{{ $contact->double_opt_in }}">
            </div>
            <div class="col-12 col-md-6">
                <input type="text" placeholder="OPT_IN" name="opt_in" class="span2 my_input w200" value="{{ $contact->opt_in }}">
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn-form-primary float-end">
                Save Contact
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#email').on('input', function() {
        var email = $(this).val();
        var emailError = $('#email-error');
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email === '') {
            emailError.text('Email is required.');
        } else if (!emailRegex.test(email)) {
            emailError.text('Please enter a valid email address.');
        } else {
            emailError.text('');
        }
    });
});
</script>
@endsection
