<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">    
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between">            
            <h1 class="mb-4">Weekly Report</h1>
            <small class="text-muted">{{ date_format(now(), 'd/m/Y h:m:s') }}</small>
        </div>
        <div class="card">
            <div class="card-body">                
                <ul class="list-group mb-4">
                    <!-- List items with Bootstrap styles -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Recipients&nbsp;&nbsp;
                        <span class="text-right">
                            <?php $a = 0; ?> @foreach($stats as $stat) <?php $a += $stat->total_sent == '' ? 0 : count(explode(',', $stat->total_sent)); ?> @endforeach {{$a}}
                        </span>                        
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Opened&nbsp;&nbsp;                        
                        <span>
                            <?php $a = 0; ?> @foreach($stats as $stat) <?php $a += $stat->opened == '' ? 0 : count(explode(',', $stat->opened)); ?> @endforeach {{$a}}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Clicked&nbsp;&nbsp;
                        <span>
                            <?php $a = 0; ?> @foreach($stats as $stat) <?php $a += $stat->clicked == '' ? 0 : count(explode(',', $stat->clicked)); ?> @endforeach {{$a}}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Unsubscribed&nbsp;&nbsp;
                        <span>
                            <?php $a = 0; ?> @foreach($stats as $stat) <?php $a += $stat->black_list == '' ? 0 : count(explode(',', $stat->black_list)); ?> @endforeach {{$a}}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Bounced&nbsp;&nbsp;
                        <span>
                            <?php $a = 0; ?> @foreach($stats as $stat) <?php $a += $stat->bounced == '' ? 0 : count(explode(',', $stat->bounced)); ?> @endforeach {{$a}}
                        </span>
                    </li>
                </ul>

                @foreach($stats as $stat)
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="card-text">ID: <strong>#{{ $stat->camp_id }}</strong> </p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p class="card-text">Name: <strong>{{ $stat->campaign->name }}</strong> </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="card-text">Created on: <strong>{{ date_format($stat->campaign->created_at, 'd/m/Y h:m:s') }}</strong> </p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <p class="card-text">Updated on: <strong>{{ date_format($stat->campaign->updated_at, 'd/m/Y h:m:s') }}</strong> </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <p class="card-text">Sent on: <strong>{{ date_format($stat->created_at, 'd/m/Y h:m:s') }}</strong> </p>
                                </div>
                            </div>
                            
                            <ul class="campaign-stats-list">                                                
                                <li class="tooltip-help" data-placement="auto" data-toggle="tooltip" data-original-title="Number of contacts the campaign was sent to" data-container="body">
                                    <span>Contacts</span>
                                    <span class="text-right">
                                        {{ $stat->total_sent == ''? 0 : count(explode(',', $stat->total_sent))}}
                                    </span>                                    
                                </li>
                                <li class="tooltip-help text-info" data-placement="auto" data-toggle="tooltip" data-original-title="Number of recipients that opened a campaign any number of times" data-container="body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span>Opened</span>
                                            <span class="text-right">                        
                                                {{ $stat->opened == ''? 0 : count(explode(',', $stat->opened))}}
                                            </span>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="w3-light-grey w3-round">
                                                <div class="w3-blue w3-round" style="height:5px;width:{{ $stat->total_sent == '' ? 0 : round($stat->opened == ''? 0 : count(explode(',', $stat->opened)) / (count(explode(',', $stat->total_sent))) * 100) }}%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <span class="text-primary">{{ $stat->total_sent == '' ? 0 : round($stat->opened == ''? 0 : count(explode(',', $stat->opened)) / (count(explode(',', $stat->total_sent))) * 100) }}%</span>
                                        </div>
                                    </div>
                                    
                                    
                                </li>
                                <li class="tooltip-help text-success" data-placement="auto" data-toggle="tooltip" data-original-title="Number of recipients that clicked any tracked link any number of times in a campaign" data-container="body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span>Clicked</span>
                                            <span class="text-right">                        
                                                {{ $stat->clicked == ''? 0 : count(explode(',', $stat->clicked))}}
                                            </span>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="w3-light-grey w3-round">
                                                <div class="w3-green w3-round" style="height:5px;width:{{ $stat->total_sent == '' ? 0 : round($stat->clicked == ''? 0 : count(explode(',', $stat->clicked)) / (count(explode(',', $stat->total_sent))) * 100) }}%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <span class="text-primary">{{ $stat->total_sent == '' ? 0 : round($stat->clicked == ''? 0 : count(explode(',', $stat->clicked)) / (count(explode(',', $stat->total_sent))) * 100) }}%</span>
                                        </div>
                                    </div>                                                                                                        
                                </li>
                                <li class="tooltip-help text-warning" data-placement="auto" data-toggle="tooltip" data-original-title="Number of contacts that opted out of your list using the unsubscribe link in a campaign" data-container="body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span>Unsubscribed</span>
                                            <span class="text-right">                        
                                                {{ $stat->black_list == ''? 0 : count(explode(',', $stat->black_list))}}
                                            </span>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="w3-light-grey w3-round">
                                                <div class="w3-orange w3-round" style="height:5px;width:{{ $stat->total_sent == '' ? 0 : round($stat->black_list == ''? 0 : count(explode(',', $stat->black_list)) / (count(explode(',', $stat->total_sent))) * 100) }}%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <span class="text-primary">{{ $stat->total_sent == '' ? 0 : round($stat->black_list == ''? 0 : count(explode(',', $stat->black_list)) / (count(explode(',', $stat->total_sent))) * 100) }}%</span>
                                        </div>
                                    </div>                                    
                                </li>
                                <li class="tooltip-help text-danger" data-placement="auto" data-toggle="tooltip" data-original-title="Total of non-existent address or blocked email address" data-container="body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span>Bounced</span>
                                            <span class="text-right">                        
                                                {{ $stat->bounced == ''? 0 : count(explode(',', $stat->bounced))}}
                                            </span>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="w3-light-grey w3-round">
                                                <div class="w3-red w3-round" style="height:5px;width:{{ $stat->total_sent == '' ? 0 : round($stat->bounced == ''? 0 : count(explode(',', $stat->bounced)) / (count(explode(',', $stat->total_sent))) * 100) }}%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <span class="text-primary">{{ $stat->total_sent == '' ? 0 : round($stat->bounced == ''? 0 : count(explode(',', $stat->bounced)) / (count(explode(',', $stat->total_sent))) * 100) }}%</span>
                                        </div>
                                    </div>                                     
                                </li>
                            </ul>
                            <!-- Add more card details as needed -->
                        </div>
                    </div>
                @endforeach
                <div class="text-center">
                    <img src="https://www.techics.net/account/public/assets/img/Logo-Color.png" alt="Hybrid Mail Account" class="account_logo" style="width: 200px;" />

                </div>

                
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js (if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-eVAPvl7C1k0xxdUms3hAAB95noHJDKT/egR245cqqfKCIfp6NT0LdRr5HzfN2HQv" crossorigin="anonymous"></script>
</body>
</html>